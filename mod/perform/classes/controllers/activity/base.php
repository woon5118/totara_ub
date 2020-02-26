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

use mod_perform\util;
use moodle_url;
use totara_mvc\controller;

class base extends controller {

    /**
     * @inheritDoc
     */
    protected function setup_context(): \context {
        $category_id = util::get_default_categoryid();
        return \context_coursecat::instance($category_id);
    }

    protected function get_edit_url(): moodle_url {
        return new moodle_url('/mod/perform/manage/activity/edit.php');
    }

    protected function get_activity_list_url(): moodle_url {
        return new moodle_url('/mod/perform/manage/activity');
    }

}