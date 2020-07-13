<?php
/**
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\notification\brokers\instance_created_reminder;
use mod_perform\notification\factory;

require_once(__DIR__ . '/notification_testcase.php');

class mod_perform_notification_broker_testcase extends mod_perform_notification_testcase {
    public function test_instance_created_reminder_check_trigger_condition() {
        $activity = $this->create_activity();
        $notification = $this->create_notification($activity, 'instance_created_reminder');
        $notification->set_triggers([1, 2, 5]);
        $broker = factory::create_broker('instance_created_reminder');
        /** @var instance_created_reminder $broker */

        $record = (object)['created_at' => time()];
        $clock = factory::create_clock();
        $this->assertFalse($broker->check_trigger_condition($notification, $record, $clock));

        // Loop  Bias   <---- 0 ----><==== 1 ====><==== 2 ====><---- 3 ----><---- 4 ----><==== 5 ====><---- 6 ----><---- 7 ---->
        //  0   0d 17h           *
        //  1   1d 10h                    *
        //  2   2d  3h                              *
        //  3   2d 20h                                      *
        //  4   3d 13h                                                *
        //  5   4d  6h                                                        *
        //  6   4d 23h                                                                *
        //  7   5d 16h                                                                           *
        //  8   6d  9h                                                                                    *
        //  9   7d  2h                                                                                              *
        $expection = [         false,  true,      true,    false,   false,  false,   false,    true,    false,   false];
        $bias = 0;
        for ($i = 0; $i < 10; $i++) {
            $clock = factory::create_clock_with_time_machine(17 * HOURSECS);
            $this->assertEquals($expection[$i], $broker->check_trigger_condition($notification, $record, $clock), sprintf("Failure at #%d (%dd %02dh)", $i, $bias / DAYSECS, ($bias % DAYSECS) / HOURSECS));
            $notification->set_last_run_time($clock->get_time());
        }
    }
}
