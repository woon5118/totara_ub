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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_competency
 */

use core\collection;
use core\entities\user;
use totara_competency\models\profile\filter;
use totara_competency\models\profile\item;
use totara_competency\models\profile\progress;

global $CFG;

require_once($CFG->dirroot . '/totara/competency/tests/totara_competency_testcase.php');

/**
 * Class totara_competency_model_scale_testcase
 *
 * @coversDefaultClass \totara_competency\models\scale
 */
class totara_competency_model_profile_progress_testcase extends totara_competency_testcase {

    /**
     * @covers ::find_by_id
     * @covers ::find_by_ids
     * @covers ::__construct
     */
    public function test_it_loads_scales_using_ids() {
        $data = $this->create_sorting_testing_data(true);

        // Let's build data for a user
        /** @var user $user */
        $user = $data['users']->first()->add_extra_attribute('fullname');

        $progress = progress::for($user->id);

        $this->assertInstanceOf(progress::class, $progress);

        // Let's check that it has required objects

        // User
        $this->assertInstanceOf(stdClass::class, $progress->user);
        $this->assertEquals($user->to_array(), (array) $progress->user);

        // Individual progress items
        $this->assertInstanceOf(collection::class, $progress->items);

        $this->assertGreaterThan(0, count($progress->items));

        $progress->items->map(function (item $item) {
            // Well having type-hint will already assert that the item is of the correct type

            // TODO Let's quickly assert items for the correct structure and content
        });

        // Filters
        $this->assertIsArray($progress->filters);
        $this->assertGreaterThan(0, count($progress->filters));

        foreach ($progress->filters as $filter) {
            $this->assertInstanceOf(filter::class, $filter);
        }

        // Latest achievement
        $this->assertEquals($data['competencies']->first()->fullname, $progress->latest_achievement);

    }
}