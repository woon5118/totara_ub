<?php
/*
 * This file is part of Totara Perform
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers\activity;

use context;
use mod_perform\controllers\perform_controller;
use mod_perform\models\activity\activity;
use moodle_url;

class manage_participation extends perform_controller {
    use manage_participation_tabs;

    /**
     * @var activity $activity
     */
    private $activity;
    /**
     * @var string
     */
    private $current_tab;

    public function setup_context(): context {
        $activity_id = $this->get_required_param('activity_id', PARAM_INT);
        $this->activity = activity::load_by_id($activity_id);

        return $this->activity->get_context();
    }

    public function action() {
        $this->current_tab = 'subject_instances';
        return $this->render_page();
    }

    public function action_subjects() {
        $this->current_tab = 'subject_instances';
        return $this->render_page();
    }

    public function action_participants() {
        $this->current_tab = 'participant_instances';
        return $this->render_page();
    }

    public function action_sections() {
        $this->current_tab = 'participant_sections';
        return $this->render_page();
    }

    public static function get_base_url(): string {
        return '/mod/perform/manage/participation/';
    }

    private function render_page() {
        $data = [
            'backurl' => new moodle_url('/mod/perform/manage/activity/index.php'),
            'tabs' => self::get_participation_tabs($this->activity->id, $this->current_tab),
            'heading' => get_string('manage_participation_heading', 'mod_perform', $this->activity->name),
            'name' => $this->current_tab,
        ];
        $url = static::get_base_url() . $this->current_tab . '.php';
        $this->set_url(new moodle_url($url, ['activity_id' => $this->activity->id]));

        return self::create_view('mod_perform/participation', $data)
            ->set_title($this->activity->name);
    }
}
