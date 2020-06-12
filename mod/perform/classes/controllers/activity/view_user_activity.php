<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers\activity;

use context;
use core\entities\user;
use mod_perform\controllers\perform_controller;
use mod_perform\entities\activity\participant_instance;
use mod_perform\util;
use totara_mvc\tui_view;

/*
 * This page lists perform activities the logged in user are a participant in.
 */
class view_user_activity extends perform_controller {

    /**
     * @inheritDoc
     */
    protected function setup_context(): context {
        $category_id = util::get_default_category_id();
        return \context_coursecat::instance($category_id);
    }

    /**
     * @return tui_view
     */
    public function action(): tui_view {
        $this->set_url(static::get_url(['participant_instance_id' => $this->get_participant_instance_id()]));

        $props = [
            'current-user-id' => user::logged_in()->id,
            'subject-instance-id' => $this->get_subject_instance_id(),
            'participant-instance-id' => $this->get_participant_instance_id(),
        ];

        return self::create_tui_view('mod_perform/pages/UserActivity', $props)
            ->set_title(get_string('user_activities_page_title', 'mod_perform'));
    }

    public static function get_base_url(): string {
        return '/mod/perform/activity/view.php';
    }

    protected function get_participant_instance_id(): int {
        return required_param('participant_instance_id', PARAM_INT);
    }

    protected function get_subject_instance_id(): ?int {
        /** @var participant_instance $participant_instance */
        $participant_instance = participant_instance::repository()->where('id', $this->get_participant_instance_id())
            ->order_by('id')
            ->first();

        // We allow the return of null, because the front end will handle showing the not found message.
        return $participant_instance ? $participant_instance->subject_instance_id : null;
    }

}