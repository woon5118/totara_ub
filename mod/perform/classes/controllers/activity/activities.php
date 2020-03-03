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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers\activity;

use mod_perform\models\activity\activity as activity_model;
use mod_perform\util;
use moodle_url;
use totara_mvc\admin_controller;
use totara_mvc\tui_view;

class activities extends admin_controller {

    protected $admin_external_page_name = 'mod_perform_manage_activities';

    /**
     * @inheritDoc
     */
    protected function setup_context(): \context {
        $category_id = util::get_default_categoryid();
        return \context_coursecat::instance($category_id);
    }

    /**
     * @return tui_view
     */
    public function action(): tui_view {
        $this->require_capability('mod/perform:view_manage_activities', $this->get_context());

        $props = [
            'edit-url' => (string) edit_activity::get_url(),
            'can-add' => activity_model::can_create(),
        ];

        return tui_view::create('mod_perform/pages/Activities', $props);
    }

    public static function get_url(): moodle_url {
        return new moodle_url('/mod/perform/manage/activity');
    }

}