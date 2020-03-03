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
use mod_perform\models\activity\activity;
use moodle_url;
use totara_mvc\controller;
use totara_mvc\tui_view;

class edit_activity extends controller {

    /**
     * @inheritDoc
     */
    protected function setup_context(): context {
        return activity::load_by_id($this->get_activity_id())->get_context();
    }

    /**
     * @return tui_view
     */
    public function action(): tui_view {
        $this->require_capability('mod/perform:manage_activity', $this->get_context());

        $props = [
            'activity-id' => $this->get_activity_id(),
            'go-back-link' => (string) activities::get_url(),
        ];

        return tui_view::create('mod_perform/pages/ManageActivity', $props)
            ->set_url(self::get_url())
            ->set_title(get_string('perform:manage_activity_page_title', 'mod_perform'));
    }

    private function get_activity_id(): int {
        return required_param('activity_id', PARAM_INT);
    }

    public static function get_url(): moodle_url {
        return new moodle_url('/mod/perform/manage/activity/edit.php');
    }

}