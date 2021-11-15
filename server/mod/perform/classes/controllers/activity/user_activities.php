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
use core\entity\user;
use mod_perform\controllers\perform_controller;
use mod_perform\data_providers\activity\activity_type;
use mod_perform\models\activity\helpers\manual_participant_helper;
use mod_perform\state\participant_instance\participant_instance_progress;
use mod_perform\state\state_helper;
use mod_perform\state\subject_instance\subject_instance_progress;
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

        $current_user_id = user::logged_in()->id;

        $props = [
            'current-user-id' => $current_user_id,
            'view-activity-url' => (string) view_user_activity::get_url(),
            'print-activity-url' => (string) print_user_activity::get_url(),
            'show-about-others-tab' => (bool) $this->get_optional_param('show_about_others_tab', false, PARAM_BOOL),
            'completion-save-success' => (bool) $this->get_optional_param('completion_save_success', false, PARAM_BOOL),
            'closed-on-completion' => (bool) $this->get_optional_param('closed_on_completion', false, PARAM_BOOL),
            'require-manual-participants-notification' => manual_participant_helper::for_user($current_user_id)
                ->has_pending_selections(),
            'can-potentially-manage-participants' => util::can_potentially_manage_participants($current_user_id),
            'is-historic-activities-enabled' => util::is_historic_activities_enabled(),
            'filter-options' => $this->get_filter_options(),
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
    
    /**
     * @return array
     */
    private function get_filter_options(): array {
        $activity_types = [];
        foreach ((new activity_type())->get() as $type) {
            $activity_types[$type->id] = $type->display_name;
        }

        /** @var array $progress_options */
        // Order by code
        $progress_names = state_helper::get_all_names('participant_instance', participant_instance_progress::get_type());
        ksort($progress_names);

        $progress_display_names = state_helper::get_all_display_names('participant_instance', subject_instance_progress::get_type());
        ksort($progress_display_names);
        $progress_options = array_combine($progress_names, $progress_display_names);

        return [
            'activityTypes' => $activity_types,
            'progressOptions' => $progress_options,
        ];
    }

}