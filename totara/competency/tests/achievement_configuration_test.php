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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

use criteria_coursecompletion\coursecompletion;
use pathway_manual\manual;
use totara_competency\achievement_configuration;
use totara_competency\achievement_criteria;
use totara_competency\entities\competency;
use totara_competency\entities\configuration_change;
use totara_competency\entities\scale;
use totara_criteria\criterion;

class totara_competency_achievement_configuration_testcase extends advanced_testcase {

    private function setup_data() {
        global $DB;

        $data = new class() {
            /** @var competency $comp */
            public $comp;
            /** @var achievement_configuration $config */
            public $config;
            /** @var \stdClass[] $courses */
            public $courses;
            /** @var coursecompletion[] $cc */
            public $cc;
        };

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $data->comp = $competency_generator->create_competency();
        $data->config = new achievement_configuration($data->comp);

        // Some courses
        for ($i = 1; $i <= 5; $i++) {
            $record = [
                'shortname' => "Course $i",
                'fullname' => "Course $i",
                'enablecompletion' => true,
            ];

            $data->courses[$i] = $this->getDataGenerator()->create_course($record);
        }

        // Create 2 coursecompletion criteria
        //      - Course 1 AND Course 2
        //      - Course 1 OR Course 3 OR Course 5

        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');
        $data->cc[1] = $criteria_generator->create_coursecompletion([
            'aggregation' => criterion::AGGREGATE_ALL,
            'courseids' => [$data->courses[1]->id, $data->courses[2]->id],
        ]);

        $data->cc[2] = $criteria_generator->create_coursecompletion([
            'aggregation' => [
                'method' => criterion::AGGREGATE_ANY_N,
                'req_items' => 1,
            ],
            'courseids' => [$data->courses[1]->id, $data->courses[3]->id, $data->courses[5]->id],
        ]);

        return $data;
    }

    public function test_log_configuration_change_invalid_type() {
        $data = $this->setup_data();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageRegExp('/Invalid configuration change type/');

        configuration_change::add_competency_entry(
            $data->config->get_competency()->id,
            'whatever'
        );
    }

    public function test_log_configuration_change_no_action_time() {
        global $DB;

        $data = $this->setup_data();

        // Ensure we start with a clean table
        $this->assertSame(0, $DB->count_records('totara_competency_configuration_change'));

        $config = $data->config;

        configuration_change::add_competency_entry(
            $config->get_competency()->id,
            configuration_change::CHANGED_CRITERIA
        );
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_change'));

        // Log the same again. Because the times differ - we should get 2 log entries
        $this->waitForSecond();
        configuration_change::add_competency_entry(
            $config->get_competency()->id,
            configuration_change::CHANGED_CRITERIA
        );
        $this->assertSame(2, $DB->count_records('totara_competency_configuration_change'));
    }

    public function test_log_configuration_change_same_action_time() {
        global $DB;

        $data = $this->setup_data();

        // Ensure we start with a clean table
        $this->assertSame(0, $DB->count_records('totara_competency_configuration_change'));

        $config = $data->config;
        $action_time = time();

        configuration_change::add_competency_entry(
            $config->get_competency()->id,
            configuration_change::CHANGED_CRITERIA,
            $action_time
        );
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_change'));

        // Log the same again. Because the times are the same - we should get only 1 log entry
        configuration_change::add_competency_entry(
            $config->get_competency()->id,
            configuration_change::CHANGED_CRITERIA,
            $action_time
        );
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_change'));

        // Now log with the same time, but a different type - should get 2 log entries
        configuration_change::add_competency_entry(
            $config->get_competency()->id,
            configuration_change::CHANGED_AGGREGATION,
            $action_time
        );
        $this->assertSame(2, $DB->count_records('totara_competency_configuration_change'));
    }

    public function test_save_configuration_history() {
        global $DB;

        $data = $this->setup_data();
        $config = $data->config;

        // Ensure we start with an empty totara_competency_configuration_history
        $this->assertSame(0, $DB->count_records('totara_competency_configuration_history'));

        // Dump an empty configuration - no action time
        $config->save_configuration_history();
        $rows = $DB->get_records('totara_competency_configuration_history');
        $this->assertSame(1, count($rows));
        // For now just testing that there is a row - empty content is tested later

        // Generate some configuration data
        /** @var totara_competency_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $manual = $generator->create_manual($data->comp, [manual::ROLE_MANAGER], 1);

        $scale_value = $data->comp->scale->sorted_values_high_to_low->first();
        $cg = $generator->create_criteria_group($data->comp, [$data->cc[1], $data->cc[2]], $scale_value, null, null, 2);

        // Dump the populated configuration - Use an action_time value to allow us to retrieve the correct entry
        $action_time = 123;
        $config->save_configuration_history($action_time);
        $rows = $DB->get_records('totara_competency_configuration_history');
        $this->assertSame(2, count($rows));

        foreach ($rows as $row) {
            $this->assertSame($row->active_from == $action_time, is_null($row->active_to));

            $dumped_configuration = json_decode($row->configuration, true);
            $this->assertSame($row->active_from != $action_time, empty($dumped_configuration['pathways']));
        }

        // Make some changes and save it
        $initial_configuration = $DB->get_record('totara_competency_configuration_history', ['active_from' => $action_time]);

        $config->set_aggregation_type('first');

        // Check that we don't dump second configuration if action_time is the same
        $config->save_aggregation($action_time);
        $rows = $DB->get_records('totara_competency_configuration_history');
        $this->assertSame(2, count($rows));

        // Although the configuration was changed, we didn't dump the new configuration because the same action_time was used
        $latest_configuration = $DB->get_record('totara_competency_configuration_history', ['active_from' => $action_time]);
        $this->assertEquals($initial_configuration, $latest_configuration);

        // Now dump with a new action_time. The changed version should be dumped
        $action_time = 456;
        $config->save_configuration_history($action_time);
        $rows = $DB->get_records('totara_competency_configuration_history');
        $this->assertSame(3, count($rows));
        $latest_configuration = $DB->get_record('totara_competency_configuration_history', ['active_from' => $action_time]);
        $this->assertNotEquals($initial_configuration, $latest_configuration);
    }


    public function test_link_default_preset() {
        global $DB;

        $data = $this->setup_data();
        $competency = $data->comp;
        /** @var achievement_configuration $config */
        $config = $data->config;

        /** @var totara_competency_generator $competency_generator */
        $def_pathways = achievement_criteria::get_default_pathways($competency->scale, $competency->id);

        $pathways = $config->get_active_pathways();

        // Validate that it has no pathways
        $this->assertEquals(0, count($pathways));

        // No link the default pathways
        $config->link_default_preset();

        // Retrieve the competency from the db and validate that the pathways have been linked
        $comp_id = $competency->id;
        $competency = new competency($comp_id);
        $config = new achievement_configuration($competency);
        $pathways = $config->get_active_pathways();

        $this->assertEquals(count($def_pathways), count($pathways));

        // Should also be logged
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_change'));
    }

    /**
     * Test user_can_become_proficient through single value pathways
     */
    public function test_user_can_become_proficient() {
        $data = $this->setup_data();

        /** totara_competency_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        /** @var achievement_configuration $config */
        $config = $data->config;

        // Initially no pathways
        $this->assertFalse($config->user_can_become_proficient());

        // Now we add a pathway which doesn't lead to a proficient value
        $pw1 = $generator->create_criteria_group($data->comp, $data->cc[1], $data->comp->scale->default_value, 1);

        // We need to re-initialize the configuration as the pathways have changed
        // We also need to refresh the competency-pathway relationship
        // TODO: v2 - adding/removing/saving etc. of pathways should be handled through the configuration in a similar way
        //            as criteria are handled through criteria_group
        $data->comp->load_relation('active_pathways');
        $config = new achievement_configuration($data->comp);
        $this->assertFalse($config->user_can_become_proficient());


        // Add a second pathway that leads to a proficient value
        $pw2 = $generator->create_criteria_group($data->comp, $data->cc[2], $data->comp->scale->min_proficient_value, 2);
        $data->comp->load_relation('active_pathways');
        $config = new achievement_configuration($data->comp);
        $this->assertTrue($config->user_can_become_proficient());

        // Now we remove pw2 and add a manual pw. User can still become proficient
        $pw2->delete();
        $data->comp->load_relation('active_pathways');
        $config = new achievement_configuration($data->comp);
        $this->assertFalse($config->user_can_become_proficient());

        $pw3 = $generator->create_manual($data->comp, [manual::ROLE_MANAGER], 3);
        $data->comp->load_relation('active_pathways');
        $config = new achievement_configuration($data->comp);
        $this->assertTrue($config->user_can_become_proficient());
    }
}
