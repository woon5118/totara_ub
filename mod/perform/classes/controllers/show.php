<?php
/**
 *
 * This file is part of Totara LMS
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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 *
 */
namespace mod_perform\controllers;

use context;
use totara_mvc\controller;
use totara_mvc\view;
use mod_perform\models\activity;

class show extends controller {

    /**
     * @var activity $model;
     */
    protected $model;

    protected function setup_context(): context {
        $id = $this->get_param('id',  PARAM_INT, null, true);
        // Store the model for use in the action.
        $this->model = activity::load_by_id($id);
        return $this->model->get_context();
    }

    public function action() {
        $this->require_capability('mod/perform:view', $this->get_context());

        // Not how you access model data, just for demonstration purposes.
        $data = $this->model->get_entity()->to_array();
        $data['rawdata'] = var_export((array)$data, true);

        return new view('mod_perform/show', $data);
    }

}