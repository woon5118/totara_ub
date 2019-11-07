<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\views;

use moodle_url;
use totara_core\output\select_region_panel;
use totara_mvc\view;

abstract class base extends view {

    protected $content_template = '';

    protected $location = '/totara/competency/assignments/';

    protected function prepare_output($output) {
        $output = [
            'create_btn_url' => $this->get_absolute_url('create.php'),
            'index_url' => $this->get_absolute_url('index.php'),
            'save_url' => $this->get_absolute_url('save.php'),
            'report_btn_url' => 'users.php',
            'has_back_btn' => false,
            'has_page_btns' => true,
            'content_template' => $this->content_template,
            'filter_region_panel' => $this->create_region_panel(),
            'title' => $this->title
        ];

        return $output;
    }

    abstract protected function create_region_panel(): select_region_panel;

}
