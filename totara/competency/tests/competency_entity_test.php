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
use totara_competency\entities\competency;
use totara_competency\entities\competency_framework;
use totara_competency\entities\scale;

class totara_competency_entity_testcase extends \advanced_testcase {

    /**
     * Create data for the competency to test
     *
     * @throws coding_exception
     */
    public function create_data() {
        $data = [
            'competencies' => [],
        ];

        $data['competencies'][] = $this->generator()->create_competency();

        return $data;
    }

    public function test_competency_entity() {
        $data = $this->create_data();

        $this->assertEqualsCanonicalizing($data['competencies'][0]->to_array(), competency::repository()->find($data['competencies'][0]->id)->to_array());
    }

    public function test_it_has_related_scale() {
        $data = $this->create_data();

        $competency_id = (int) $data['competencies'][0]->id;
        $framework_id = (int) $data['competencies'][0]->frameworkid;

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
        $this->markTestSkipped('TODO'); // TODO add test for custom fields attribute
    }

    public function test_it_can_preload_assigned_user_groups() {
        $this->markTestSkipped('TODO'); // TODO add test for custom fields attribute
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