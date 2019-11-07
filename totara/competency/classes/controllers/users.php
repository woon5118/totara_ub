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
 * @package tassign_competency
 */

namespace tassign_competency\controllers;

use context_system;
use totara_mvc\has_report;

class users extends base {

    use has_report;

    protected $admin_external_page_name = 'competency_assignment_users';

    public function action() {
        $this->require_capability('totara/competency:view', context_system::instance());

        // Reportbuilder basic arguments
        $sid = $this->get_param('sid',  PARAM_INT, 0);
        $debug = $this->get_param('debug', PARAM_BOOL, false);

        $report = $this->load_embedded_report('assignment_competency_users');

        \totara_reportbuilder\event\report_viewed::create_from_report($report)->trigger();

        return new \tassign_competency\views\users('tassign_competency/users', $report, $sid, $debug);
    }

}
