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
 * @package tassign_competencies
 */

namespace tassign_competency\views;

use moodle_url;
use reportbuilder;
use totara_mvc\report_view;

class users extends report_view {

    protected $title = ['title:users', 'tassign_competency'];

    protected function prepare_output($report) {
        $output = array_merge(
            parent::prepare_output($report),
            [
                'index_url' => new moodle_url($this->config->wwwroot.'/totara/assignment/plugins/competency/index.php'),
                'title' => $this->title,
            ]
        );
        return $output;
    }

    protected function set_button(reportbuilder $report) {
        $edit_button = $report->edit_button();
        $sync_button = '';
        if (has_capability('totara/competency:manage', \context_system::instance())) {
            $sync_button = $this->renderer->single_button(
                new moodle_url('/totara/assignment/plugins/competency/sync.php'),
                get_string('button:sync_users', 'tassign_competency'),
                'post'
            );
        }

        $this->page->set_button($sync_button.$edit_button);
    }

}
