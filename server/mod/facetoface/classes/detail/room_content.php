<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\detail;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use context;
use context_system;
use core\entity\user;
use core\orm\query\builder;
use mod_facetoface\output\helper\virtualroom_card_factory;
use mod_facetoface\output\seminarevent_detail_section;
use mod_facetoface\output\seminarresource_card;
use mod_facetoface\output\session_time;
use moodle_url;
use mod_facetoface_renderer;
use rb_facetoface_summary_room_embedded;
use mod_facetoface\room;
use mod_facetoface\room_dates_virtualmeeting;
use mod_facetoface\room_helper;
use mod_facetoface\seminar_attachment_item;
use mod_facetoface\seminar_session;
use mod_facetoface\signup;
use mod_facetoface\signup_helper;
use totara_core\entity\virtual_meeting as virtual_meeting_entity;
use totara_core\virtualmeeting\exception\meeting_exception;
use totara_core\virtualmeeting\virtual_meeting as virtual_meeting_model;

/**
 * Generate room details.
 */
class room_content extends content_generator {
    /**
     * Constructor.
     * @param string $idparam a parameter name that represents 'id'
     * @param string|moodle_url $pageurl the URL to this page
     */
    public function __construct(string $idparam, $pageurl) {
        parent::__construct($idparam, 'modfacetofacerooms', 'facetoface_summary_room', $pageurl);
    }

    protected function load(int $id): seminar_attachment_item {
        return new room($id);
    }

    protected function get_title(seminar_attachment_item $item): string {
        /** @var room $item */
        return $item->get_name();
    }

    protected function has_edit_capability(seminar_attachment_item $item, context $context, stdClass $user): bool {
        // The managesitewide capability is always system level.
        return has_capability('mod/facetoface:managesitewiderooms', context_system::instance(), $user);
    }

    protected function has_report_capability(seminar_attachment_item $item, context $context, stdClass $user): bool {
        return rb_facetoface_summary_room_embedded::is_capable_static($user->id);
    }

    protected function get_report_header(seminar_attachment_item $item): string {
        return get_string('upcomingsessionsinroom', 'mod_facetoface');
    }

    protected function render_details(seminar_attachment_item $item, stdClass $user, mod_facetoface_renderer $renderer): string {
        /** @var room $item */
        return $renderer->render_room_details($item);
    }

    protected function render_empty(moodle_url $manageurl): string {
        return get_string('reports:selectroom', 'mod_facetoface', $manageurl->out());
    }

    /**
     * See if the user can manage ad-hoc rooms.
     *
     * @param user $user
     * @param context $context
     * @return boolean
     */
    private static function can_manage_custom_rooms(user $user, context $context): bool {
        return has_capability('mod/facetoface:manageadhocrooms', $context, $user->id);
    }

    /**
     * Serve a virtual meeting card when coming from the manage page.
     *
     * @param room $room unused
     * @param user $user unused
     * @param context $context unused
     * @return seminarresource_card|null always returns null; no card is served
     */
    private function virtual_meeting_card_data_from_manage(room $room, user $user, context $context): ?seminarresource_card {
        return virtualroom_card_factory::none();
    }

    /**
     * Serve a virtual room card when coming from the manage page.
     *
     * @param room $room
     * @param user $user
     * @param context $context
     * @return seminarresource_card
     */
    private function virtual_room_card_data_from_manage(room $room, user $user, context $context): seminarresource_card {
        if (self::can_manage_custom_rooms($user, $context)) {
            return virtualroom_card_factory::go_to_room($room->get_name(), $room->get_url(), null);
        } else {
            return virtualroom_card_factory::unavailable();
        }
    }

    /**
     * Serve a virtual meeting card when coming from the event page.
     *
     * @param seminar_session $session
     * @param room $room
     * @param user $user
     * @param context $context
     * @return seminarresource_card
     */
    private function virtual_meeting_card_data_from_event(seminar_session $session, room $room, user $user, context $context): seminarresource_card {
        $room_vm = room_dates_virtualmeeting::load_by_session_room($session, $room);
        $model = $room_vm->get_virtualmeeting();
        if ($model === null) {
            return virtualroom_card_factory::unavailable();
        }
        $room_url = $model->get_join_url(false);
        return $this->virtual_x_card_data_from_event($session, $room, $user, $context, $room_url, $model);
    }

    /**
     * Serve a virtual room card when coming from the event page.
     *
     * @param seminar_session $session
     * @param room $room
     * @param user $user
     * @param context $context
     * @return seminarresource_card
     */
    private function virtual_room_card_data_from_event(seminar_session $session, room $room, user $user, context $context): seminarresource_card {
        $room_url = $room->get_url();
        return $this->virtual_x_card_data_from_event($session, $room, $user, $context, $room_url, null);
    }

    /**
     * Serve a virtual meeting/room card when coming from the event page.
     *
     * @param seminar_session $session
     * @param room $room
     * @param user $user
     * @param context $context
     * @param string $room_url
     * @param virtual_meeting_model|null $model model instance for a virtual meeting, null for a virtual room
     * @return seminarresource_card
     */
    private function virtual_x_card_data_from_event(seminar_session $session, room $room, user $user, context $context, string $room_url, ?virtual_meeting_model $model): seminarresource_card {
        if (empty($room_url)) {
            return virtualroom_card_factory::unavailable();
        }
        $capable = self::can_manage_custom_rooms($user, $context) || room_helper::has_access_at_any_time($session, $user->id);
        if ($capable) {
            return $this->virtual_x_card_superuser_data_from_event($session, $room, $user, $room_url, $model);
        } else {
            return $this->virtual_x_card_learner_data_from_event($session, $room, $user, $room_url, $model);
        }
    }

    /**
     * Serve a virtual meeting/room card for managers, trainer and facilitators when coming from the event page.
     *
     * @param seminar_session $session
     * @param room $room
     * @param user $user
     * @param string $room_url
     * @param virtual_meeting_model|null $model model instance for a virtual meeting, null for a virtual room
     * @return seminarresource_card
     */
    private function virtual_x_card_superuser_data_from_event(seminar_session $session, room $room, user $user, string $room_url, ?virtual_meeting_model $model): seminarresource_card {
        if ($model && $user->id == $model->userid) {
            $host_url = $model->get_host_url(false);
            if ($host_url) {
                return virtualroom_card_factory::host_or_join($room->get_name(), $room_url, $host_url, $model);
            }
        }
        return virtualroom_card_factory::go_to_room($room->get_name(), $room_url, $model);
    }

    /**
     * Serve a virtual meeting/room card for learners when coming from the event page.
     *
     * @param seminar_session $session
     * @param room $room
     * @param user $user
     * @param string $room_url
     * @param virtual_meeting_model|null $model model instance for a virtual meeting, null for a virtual room
     * @return seminarresource_card
     */
    private function virtual_x_card_learner_data_from_event(seminar_session $session, room $room, user $user, string $room_url, ?virtual_meeting_model $model): seminarresource_card {
        $signup = signup::create($user->id, $session->get_seminar_event());
        if (signup_helper::is_booked($signup, false)) {
            if ($session->is_over() || $signup->get_seminar_event()->get_cancelledstatus()) {
                return virtualroom_card_factory::no_longer_available();
            }
            if (room_helper::has_time_come($session->get_seminar_event(), $session)) {
                return virtualroom_card_factory::join_now($room->get_name(), $room_url, $session, $model);
            }
            return virtualroom_card_factory::will_open($session);
        }
        return virtualroom_card_factory::unavailable();
    }

    protected function render_card(?seminar_session $session, seminar_attachment_item $item, stdClass $user, mod_facetoface_renderer $renderer): ?seminarresource_card {
        /** @var room $item */
        if (!$item->is_virtual()) {
            return null;
        }

        $user = new user($user, false);
        $context = $renderer->getcontext();
        if ($session !== null) {
            if ($item->is_virtual_meeting()) {
                return $this->virtual_meeting_card_data_from_event($session, $item, $user, $context);
            } else {
                return $this->virtual_room_card_data_from_event($session, $item, $user, $context);
            }
        } else {
            if ($item->is_virtual_meeting()) {
                return $this->virtual_meeting_card_data_from_manage($item, $user, $context);
            } else {
                return $this->virtual_room_card_data_from_manage($item, $user, $context);
            }
        }
    }

    protected function get_manage_button(bool $frommanage): string {
        if ($frommanage) {
            return get_string('backtorooms', 'mod_facetoface');
        } else {
            return get_string('viewallrooms', 'mod_facetoface');
        }
    }

    protected function get_manage_url(bool $frommanage): moodle_url {
        return new moodle_url('/mod/facetoface/room/manage.php');
    }

    protected function get_edit_button(seminar_attachment_item $item): string {
        return get_string('editroom', 'mod_facetoface');
    }

    protected function get_edit_url(seminar_attachment_item $item): ?moodle_url {
        /** @var room $item */
        if ($item->get_custom()) {
            return null;
        } else {
            return new moodle_url('/mod/facetoface/room/edit.php', ['id' => $item->get_id()]);
        }
    }
}
