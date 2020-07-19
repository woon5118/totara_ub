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
use context_coursecat;
use core\entities\user;
use mod_perform\controllers\perform_controller;
use mod_perform\util;
use totara_mvc\tui_view;

/*
 * This page lists perform activities the logged in user are a participant in.
 */
class user_activities extends perform_controller {

    /**
     * @inheritDoc
     */
    protected function setup_context(): context {
        $category_id = util::get_default_category_id();
        return context_coursecat::instance($category_id);
    }

    /**
     * @return tui_view
     */
    public function action(): tui_view {
        $this->set_url(self::get_url());

        $props = [
            'current-user-id' => user::logged_in()->id,
            'view-activity-url' => (string) view_user_activity::get_url(),
            'show-about-others-tab' => (bool) $this->get_optional_param('show_about_others_tab', false, PARAM_BOOL),
            'completion-save-success' => (bool) $this->get_optional_param('completion_save_success', false, PARAM_BOOL),
            'closed-on-completion' => (bool) $this->get_optional_param('closed_on_completion', false, PARAM_BOOL),
            'can-potentially-manage-participants' => util::can_potentially_manage_participants(user::logged_in()->id),
        ];

        return self::create_tui_view('mod_perform/pages/UserActivities', $props)
            ->set_title(get_string('user_activities_page_title', 'mod_perform'));
    }

    /**
     * @return string
     */
    public static function get_base_url(): string {
        return '/mod/perform/activity/index.php';
    }

}