<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use totara_criteria\item_evaluator;

class totara_criteria_item_evaluator_testcase extends advanced_testcase {

    public function test_create_item_records_no_users() {
        global $DB;

        // This method is not meant to be clever. Give it a non-genuine item id and it will use it.
        $item_id = 101;
        item_evaluator::create_item_records($item_id, []);

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(0, $item_records);
    }

    public function test_create_item_records_many_users() {
        global $DB;

        $item_id = 101;

        // Not only will it accept any item id, but also any user ids.
        $user_ids = [201, 202, 203];

        item_evaluator::create_item_records($item_id, $user_ids);

        $item_records = $DB->get_records('totara_criteria_item_record');
        $this->assertCount(3, $item_records);

        foreach ($item_records as $item_record) {
            $this->assertEquals('0', $item_record->criterion_met);
            $this->assertEquals($item_id, $item_record->criterion_item_id);
            $this->assertContains($item_record->user_id, $user_ids);

            // Todo: Changed this for now.... Used to be: This is 0 because it has not yet been evaluated.
            //$this->assertEquals(0, $item_record->timeevaluated);
        }
    }
}