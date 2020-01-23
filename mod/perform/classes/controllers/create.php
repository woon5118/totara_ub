<?php
/**
 *
 *  * This file is part of Totara LMS
 *  *
 *  * Copyright (C) 2020 onwards Totara Learning Solutions LTD
 *  *
 *  * This program is free software; you can redistribute it and/or modify
 *  * it under the terms of the GNU General Public License as published by
 *  * the Free Software Foundation; either version 3 of the License, or
 *  * (at your option) any later version.
 *  *
 *  * This program is distributed in the hope that it will be useful,
 *  * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  * GNU General Public License for more details.
 *  *
 *  * You should have received a copy of the GNU General Public License
 *  * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *  *
 *  * @author Simon Coggins <simon.coggins@totaralearning.com>
 *
 */

namespace mod_perform\controllers;
use container_perform\perform;
use mod_perform\models\activity;
use context_system;
use context;
use moodle_url;
use totara_mvc\controller;

class create extends controller {
    protected function setup_context(): context {
        $category_id = perform::get_default_categoryid();
        return \context_coursecat::instance($category_id);
    }

    public function action() {
        global $CFG;

        // TODO should this go in authorize() method instead of here?
        if (!perform::can_create_instance(null, $this->get_context())) {
            // TODO this should be a string key and module, or different exception type?
            throw new \moodle_exception('Insufficient permissions');
        }

        $data = new \stdClass();
        $data->name = 'New activity ' . rand(1, 100);

        $activity = activity::create($data);

        redirect(new moodle_url($CFG->wwwroot . '/mod/perform/index.php'));
    }
}
