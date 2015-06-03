<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Nathan Lewis <nathan.lewis@totaralms.com>
 * @package totara_certification
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

class behat_totara_certification extends behat_base {

    /**
     * Runs the update certification task, the same as when cron runs.
     *
     * @When /^I run the update certification task$/
     */
    public function i_run_update_certification_task() {
        global $CFG;

        require_once($CFG->dirroot . '/totara/certification/classes/task/update_certification_task.php');

        $event = new update_certification_task();
        $event->execute();
    }
}
