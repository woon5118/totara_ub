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
 * @package totara_competency
 */

use totara_competency\models\activity_log;
use totara_competency\entities\competency_achievement;
use totara_competency\entities\scale_value;
use tassign_competency\entities\assignment;

class totara_competency_models_activity_log_achieved_via_testcase extends advanced_testcase {

    public function test_get_methods() {
        // Dummy values
        $user_id = 100;
        $comp_id = 200;
        $achievement_time = 300;

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $hierarchy_generator->create_scale(
            'comp',
            [],
            [
                ['name' => 'Great', 'proficient' => 1, 'sortorder' => 1, 'default' => 0],
                ['name' => 'Good', 'proficient' => 1, 'sortorder' => 2, 'default' => 1],
                ['name' => 'Bad', 'proficient' => 0, 'sortorder' => 3, 'default' => 0],
            ]
        );

        $great = scale_value::repository()->where('name', '=', 'Great')->one();

        $assignment = new assignment();
        $assignment->competency_id = $comp_id;
        $assignment->user_group_id = 300;
        $assignment->user_group_type = 'test';
        $assignment->created_by = 400;
        $assignment->save();

        $achievement = new competency_achievement();
        $achievement->user_id = $user_id;
        $achievement->comp_id = $comp_id;
        $achievement->scale_value_id = $great->id;
        $achievement->time_created = $achievement_time;
        $achievement->assignment_id = $assignment->id;

        $entry = activity_log\competency_achieved_via::load_by_entity($achievement);

        $this->assertEquals('Criteria met: . Achieved \'Great\' rating.', $entry->get_description());
        $this->assertEquals($achievement_time, $entry->get_date());
        $this->assertEquals($assignment->id, $entry->get_assignment()->id);

        // While the *scale value* is in the description. This entry doesn't have return whether or not
        // we're dealing with a proficient value or not. That is the responsibilty of the activity_log/competency_achievement
        // entries.
        $this->assertNull($entry->get_proficient_status());
    }
}
