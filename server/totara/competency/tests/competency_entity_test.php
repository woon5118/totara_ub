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

use core\orm\entity\relations\has_many_through;
use core\orm\query\builder;
use totara_competency\entity\assignment;
use totara_competency\entity\competency;
use totara_competency\entity\competency_framework;
use totara_competency\entity\scale;

/**
 * @group totara_competency
 */
class totara_competency_competency_entity_testcase extends \advanced_testcase {
    /**
     * Create data for the competency to test.
     *
     * return array an array containing these elements in this order:
     *        - the test competency
     *        - [assignment name => assignment] for that competency
     *        - a [customfield type, customfield title, customfield value] tuple
     */
    public function create_data() {
        self::setAdminUser();

        $generator = $this->generator();
        $competency = $generator->create_competency();
        $competency_id = $competency->id;

        $assignments = $generator->assignment_generator();
        $user = $assignments->create_user();
        $user_assignment = $assignments->create_user_assignment(
            $competency_id,
            $user->id,
            ['type' => assignment::TYPE_ADMIN]
        );

        $hierarchies = $this->hierarchy_generator();
        $fw = $hierarchies->create_pos_frame(['fullname' => 'Framework 2']);
        $pos = $hierarchies->create_pos(['frameworkid' => $fw->id, 'fullname' => 'Position 1']);
        $pos_assignment = $assignments->create_position_assignment($competency_id, $pos->id);

        $fw = $hierarchies->create_org_frame(['fullname' => 'Framework 3']);
        $org = $hierarchies->create_org(['frameworkid' => $fw->id, 'fullname' => 'Organisation 1']);
        $org_assignment = $assignments->create_organisation_assignment($competency_id, $org->id);

        $cohort = $assignments->create_cohort();
        $cohort_assignment = $assignments->create_cohort_assignment($competency_id, $cohort->id);

        $type_idnumber = 'COMPTYPE';
        $comp_type = $hierarchies->create_comp_type(['idnumber' => $type_idnumber]);

        $customfield_type = 'menu';
        $customfield_title = 'Custom Field Label';
        $customfield_value = "ZZZ";
        $customfield_values = ['AAA', $customfield_value, 'CCC'];
        builder::table('comp_type_info_field')->insert([
            'typeid' => $comp_type,
            'shortname' => $customfield_type,
            'datatype' => $customfield_type,
            'sortorder' => 1,
            'hidden' => 0,
            'locked' => 0,
            'required' => 0,
            'forceunique' => 0,
            'param1' => implode("\n", $customfield_values),
            'fullname' => 'Custom Field Label'
        ]);

        $hierarchies->create_hierarchy_type_generic_menu([
            'hierarchy' => 'competency',
            'value' => implode(',', $customfield_values),
            'typeidnumber' => $type_idnumber
        ]);

        customfield_save_data(
            (object) [
                'id' => $competency_id,
                'typeid' => $comp_type,
                "customfield_$customfield_type" => 1,
            ],
            'competency',
            'comp_type'
        );

        return [
            $competency,
            [
                fullname($user) => $user_assignment,
                $pos->fullname => $pos_assignment,
                $org->fullname => $org_assignment,
                $cohort->name => $cohort_assignment
            ],
            [
                'type' => $customfield_type,
                'title' => $customfield_title,
                'value' => $customfield_value
            ]
        ];
    }

    public function test_competency_entity() {
        [$competency, ] = $this->create_data();
        $this->assertEqualsCanonicalizing($competency->to_array(), competency::repository()->find($competency->id)->to_array());
    }

    public function test_it_has_related_scale() {
        [$competency, ] = $this->create_data();

        $competency_id = (int) $competency->id;
        $framework_id = (int) $competency->frameworkid;

        $framework = new competency_framework($framework_id);
        $competency = new competency($competency_id);

        // We need to join this weird table to get scale for the framework
        $scale = scale::repository()
            ->join('comp_scale_assignments', 'id', 'scaleid')
            ->where('comp_scale_assignments.frameworkid', $framework_id)
            ->one(true);

        // Let's create a few other scales
        $another_scale = (new scale())
            ->set_attribute('name', 'Test scale')
            ->set_attribute('timemodified', time())
            ->set_attribute('usermodified', 2)
            ->save();

        // Let's create a framework
        $another_framework = $this->hierarchy_generator()->create_comp_frame(['scale' => $another_scale->id]);

        // Let's create another competency
        $another_competency = new competency($this->generator()->create_competency(null, $another_framework->id)->id);

        // Let's check that we have a framework created
        $this->assertEquals($framework_id, $framework->id);

        // Let's check that the relation works
        $this->assertInstanceOf(has_many_through::class, $competency->scale());

        // Let's assert that correct scale has been returned
        $this->assertEquals($scale->id, $competency->scale->id);

        // Test that another competency returns only one scale correctly
        $this->assertEquals($another_scale->id, $another_competency->scale()->one(true)->id);

        $comps = competency::repository()
            ->with('scale')
            ->get();
    }

    public function test_it_has_custom_fields_attribute() {
        [,, $customfield] = $this->create_data();
        $competencies = competency::repository()->get();
        $this->assertEquals(1, $competencies->count(), 'wrong retrieval count');

        $actual_customfields = $competencies->first()->display_custom_fields;
        $this->assertCount(1, $actual_customfields, 'wrong custom field count');

        $actual_customfield = (array)$actual_customfields[0];
        $this->assertEqualsCanonicalizing($customfield, $actual_customfield, 'wrong customfield');
    }

    public function test_it_can_preload_assigned_user_groups() {
        [, $assignments] = $this->create_data();
        $competencies = competency::repository()->get();
        $this->assertEquals(1, $competencies->count(), 'wrong retrieval count');

        $actual_assignments = $competencies->first()->assigned_user_groups;
        $this->assertCount(count($assignments), $actual_assignments, 'wrong assignment count');
        $this->assertEqualsCanonicalizing(array_keys($assignments), $actual_assignments, 'wrong user group names');
    }

    /**
     * Get competency data generator
     *
     * @return totara_competency_generator
     * @throws coding_exception
     */
    public function generator() {
        return $this->getDataGenerator()->get_plugin_generator('totara_competency');
    }

    /**
     * Get competency data generator
     *
     * @return totara_hierarchy_generator
     * @throws coding_exception
     */
    public function hierarchy_generator() {
        return $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
    }
}