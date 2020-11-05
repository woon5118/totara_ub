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
use mod_facetoface\output\seminarevent_detail_section;
use mod_facetoface\output\seminarresource_card;
use mod_facetoface\output\session_time;
use moodle_url;
use mod_facetoface_renderer;
use rb_facetoface_summary_room_embedded;
use mod_facetoface\room;
use mod_facetoface\room_helper;
use mod_facetoface\seminar_attachment_item;
use mod_facetoface\seminar_session;
use mod_facetoface\signup;

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

    protected function render_card(?seminar_session $session, seminar_attachment_item $item, stdClass $user, mod_facetoface_renderer $renderer): ?seminarresource_card {
        /** @var room $item */
        $roomurl = $item->get_url();
        if (empty($roomurl)) {
            // No virtual room link, no virtual room card.
            return null;
        }
        if ($session === null) {
            // Special case for direct access.
            if ($item->get_custom()) {
                $capable = has_capability('mod/facetoface:manageadhocrooms', $renderer->getcontext(), $user);
            } else {
                $capable = $this->has_edit_capability($item, $renderer->getcontext(), $user);
            }
            // A user with a management capability can always see the link.
            if ($capable) {
                return seminarresource_card::create(get_string('virtualroom_heading', 'mod_facetoface'), get_string('roomgoto', 'mod_facetoface'), new moodle_url($roomurl), false, null, false);
            }
            // Unavailable virtual room card.
            return seminarresource_card::create_simple(get_string('virtualroom_card_unavailable', 'mod_facetoface'), true);
        }
        $time = time();
        $signup = $signup = signup::create($user->id, $session->get_sessionid());
        if (!room_helper::show_room_link($session, $signup)) {
            return seminarresource_card::create_simple(get_string('virtualroom_card_unavailable', 'mod_facetoface'), true);
        }
        if (room_helper::has_access_at_any_time($session)) {
            return seminarresource_card::create(get_string('virtualroom_heading', 'mod_facetoface'), get_string('roomgoto', 'mod_facetoface'), new moodle_url($roomurl), false, null, false);
        }
        if ($session->is_over($time)) {
            return seminarresource_card::create_simple(get_string('virtualroom_card_over', 'mod_facetoface'), true);
        }

        $event = $session->get_seminar_event();
        $details = seminarevent_detail_section::builder()
            ->show_divider(false)
            ->add_detail(get_string('virtualroom_details_seminar', 'mod_facetoface'), $event->get_seminar()->get_name())
            ->add_detail_unsafe(get_string('virtualroom_details_session_time', 'mod_facetoface'), session_time::to_html($session->get_timestart(), $session->get_timefinish(), $session->get_sessiontimezone()))
            ->build();
        if (room_helper::has_time_come($event, $session, $time)) {
            return seminarresource_card::create(get_string('virtualroom_heading', 'mod_facetoface'), get_string('roomjoinnow', 'mod_facetoface'), new moodle_url($roomurl), true, $details, false);
        }
        return seminarresource_card::create_details(get_string('virtualroom_card_willopen', 'mod_facetoface'), $details, false);
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
