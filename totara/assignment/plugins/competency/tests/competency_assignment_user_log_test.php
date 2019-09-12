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
 * @category test
 */

defined('MOODLE_INTERNAL') || die();

use totara_competency\entities\competency_assignment_user_log;

class tassign_competency_competency_assignment_user_log_testcase extends advanced_testcase {

    public function test_action_name() {
        $log = new competency_assignment_user_log();
        $log->action = competency_assignment_user_log::ACTION_ASSIGNED;
        $this->assertEquals(competency_assignment_user_log::ACTION_ASSIGNED_NAME, $log->action_name);

        $result = $log->to_array();
        $this->assertArrayHasKey('action_name', $result);
        $this->assertEquals(competency_assignment_user_log::ACTION_ASSIGNED_NAME, $result['action_name']);

        $log->action = competency_assignment_user_log::ACTION_UNASSIGNED_USER_GROUP;
        $this->assertEquals(competency_assignment_user_log::ACTION_UNASSIGNED_USER_GROUP_NAME, $log->action_name);

        $log->action = competency_assignment_user_log::ACTION_UNASSIGNED_ARCHIVED;
        $this->assertEquals(competency_assignment_user_log::ACTION_UNASSIGNED_ARCHIVED_NAME, $log->action_name);

        $log = new competency_assignment_user_log();
        $log->action = competency_assignment_user_log::ACTION_TRACKING_START;
        $this->assertEquals(competency_assignment_user_log::ACTION_TRACKING_START_NAME, $log->action_name);

        $log = new competency_assignment_user_log();
        $log->action = competency_assignment_user_log::ACTION_TRACKING_END;
        $this->assertEquals(competency_assignment_user_log::ACTION_TRACKING_END_NAME, $log->action_name);

        $log->action = 'unknown_status';
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Unknown action name for assignment user log action \'unknown_status\'');
        $test = $log->action_name;
    }
}