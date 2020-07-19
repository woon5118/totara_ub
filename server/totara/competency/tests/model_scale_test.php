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
use totara_competency\entities\competency_achievement;
use totara_competency\entities\scale as scale_entity;
use totara_competency\entities\scale_value;
use totara_competency\models\scale;
use totara_core\advanced_feature;

/**
 * Class totara_competency_model_scale_testcase
 *
 * @coversDefaultClass \totara_competency\models\scale
 */
class totara_competency_model_scale_testcase extends advanced_testcase {

    /**
     * @covers ::load_by_id
     * @covers ::load_by_id_with_values
     * @covers ::load_by_ids
     * @covers ::__construct
     */
    public function test_it_loads_scales_using_ids() {
        $data = $this->create_data();

        $expected = $data['scales'];

        $scales = scale::load_by_ids([$expected->item(0)->id, $expected->item(2)->id, 'bottom', 'bogus', -5], false);

        $this->assertEqualsCanonicalizing(['Scale 1', 'Scale 3'], $scales->pluck('name'));

        $this->assert_scale_is_good($scales, false);

        // Let's also check that it loaded values correctly
        $this->assert_scale_is_good(
            scale::load_by_ids([$expected->item(0)->id, $expected->item(2)->id], true),
            true
        );

        $this->assertEqualsCanonicalizing(
            (new scale_entity($expected->item(1)->id))->to_array(),
            scale::load_by_id($expected->item(1)->id)->to_array()
        );

        // Let's also check that it loaded values correctly
        $this->assert_scale_is_good(new collection([scale::load_by_id_with_values($expected->item(1)->id)]), true);
    }

    /**
     * @covers ::find_by_competency_id
     * @covers ::find_by_competency_ids
     * @covers ::sanitize_ids
     */
    public function test_it_loads_scales_using_competency_ids() {
        $data = $this->create_data();

        $expected = $data['scales'];
        $comps = $data['competencies'];

        $scales = scale::find_by_competency_ids(
            [
                'I am negative :(',
                '-2',
                -2,
                0,
                $comps->item(0)->id, // Scale 1
                $comps->item(1)->id, // Scale 1
                $comps->item(3)->id, // Scale 3
                $comps->item(4)->id, // Scale 3
                'I don\'t exist',
            ],
            false
        );

        $this->assertEqualsCanonicalizing(['Scale 1', 'Scale 3'], $scales->pluck('name'));
        $this->assert_scale_is_good($scales, false);

        // Let's check the same for when we have scale values loaded
        $this->assert_scale_is_good(scale::find_by_competency_ids([
            $comps->item(1)->id, // Scale 1
            $comps->item(3)->id, // Scale 3
        ], true), true);

        $this->assertEqualsCanonicalizing(
            (new scale_entity($expected->item(1)->id))->to_array(),
            scale::find_by_competency_id($comps->item(2)->id, false)->to_array()
        );

        // Let's check that it loads scale values correctly
        $this->assert_scale_is_good(new collection([scale::find_by_competency_id($comps->item(2)->id, true)]), true);
    }

    public function test_if_scale_is_assigned() {
        $generator = $this->generator();

        $scale = $generator->create_scale('comp', ['name' => 'Scale 1']);

        $scale_model = scale::load_by_id_with_values($scale->id);

        $this->assertFalse($scale_model->is_assigned());

        // Now create a framework using the scale
        $generator->create_comp_frame(['scale' => $scale->id]);

        $this->assertTrue($scale_model->is_assigned());
    }

    public function test_if_scale_is_used() {
        $generator = $this->generator();

        $user = $this->getDataGenerator()->create_user();

        $scale = $generator->create_scale('comp', ['name' => 'Scale 1']);
        $framework = $generator->create_comp_frame(['scale' => $scale->id]);

        $comp = $generator->create_comp(['frameworkid' => $framework->id]);

        $scale_model = scale::load_by_id_with_values($scale->id);

        $this->assertFalse($scale_model->is_in_use());

        /** @var totara_competency_generator $comp_generator */
        $comp_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $assignment = $comp_generator->assignment_generator()->create_user_assignment($comp->id, $user->id);

        // Creating a record with a null scale_value_id value
        $achievement = new competency_achievement();
        $achievement->user_id = $user->id;
        $achievement->competency_id = $comp->id;
        $achievement->assignment_id = $assignment->id;
        $achievement->scale_value_id = null;
        $achievement->proficient = 0;
        $achievement->status = competency_achievement::ACTIVE_ASSIGNMENT;
        $achievement->time_created = time();
        $achievement->time_proficient = time();
        $achievement->time_scale_value = time();
        $achievement->time_status = time();
        $achievement->save();

        $this->assertFalse($scale_model->is_in_use());

        $achievement->scale_value_id = $scale_model->minproficiencyid;
        $achievement->save();

        $this->assertTrue($scale_model->is_in_use());
    }

    public function test_if_scale_is_used_in_learning_plan() {
        $generator = $this->generator();

        $user = $this->getDataGenerator()->create_user();

        $scale = $generator->create_scale('comp', ['name' => 'Scale 1']);
        $framework = $generator->create_comp_frame(['scale' => $scale->id]);

        $comp = $generator->create_comp(['frameworkid' => $framework->id]);

        $scale_model = scale::load_by_id_with_values($scale->id);

        $this->assertFalse($scale_model->is_in_use());

        /** @var totara_plan_generator $plangenerator */
        $plangenerator = $this->getDataGenerator()->get_plugin_generator('totara_plan');

        $this->setAdminUser();

        $planrecord = $plangenerator->create_learning_plan(['userid' => $user->id]);

        $plangenerator->add_learning_plan_competency($planrecord->id, $comp->id);

        /** @var dp_competency_component $competency_component */
        $competency_component = (new development_plan($planrecord->id))->get_component('competency');
        $competency_component->set_value($comp->id, $user->id, $scale_model->minproficiencyid, new stdClass());

        $this->assertTrue($scale_model->is_in_use());

        // Check that a 0 value does not make it in use
        $competency_component->set_value($comp->id, $user->id, 0, new stdClass());

        $this->assertFalse($scale_model->is_in_use());
    }

    public function test_if_scale_is_used_with_learning_plan_disabled() {
        advanced_feature::disable('learningplans');

        $generator = $this->generator();

        $user = $this->getDataGenerator()->create_user();

        $scale = $generator->create_scale('comp', ['name' => 'Scale 1']);
        $framework = $generator->create_comp_frame(['scale' => $scale->id]);

        $comp = $generator->create_comp(['frameworkid' => $framework->id]);

        $scale_model = scale::load_by_id_with_values($scale->id);

        $this->assertFalse($scale_model->is_in_use());

        /** @var totara_plan_generator $plangenerator */
        $plangenerator = $this->getDataGenerator()->get_plugin_generator('totara_plan');

        $this->setAdminUser();

        $planrecord = $plangenerator->create_learning_plan(['userid' => $user->id]);

        $plangenerator->add_learning_plan_competency($planrecord->id, $comp->id);

        /** @var dp_competency_component $competency_component */
        $competency_component = (new development_plan($planrecord->id))->get_component('competency');
        $competency_component->set_value($comp->id, $user->id, $scale_model->minproficiencyid, new stdClass());

        // We don't count values in learning plans if it's disable
        $this->assertFalse($scale_model->is_in_use());
    }

    /**
     * Assert that given collection is a valid collection of scale models
     *
     * @param collection $scales Collection of scale models
     * @param bool $with_values Check whether it should have values loaded or not
     */
    protected function assert_scale_is_good(collection $scales, bool $with_values = false) {
        $scales->map(function (scale $scale) use ($with_values) {
            $this->assertInstanceOf(scale::class, $scale);

            $exp = (new scale_entity($scale->get_id()))->to_array();

            $scale_array = $scale->to_array();

            if ($with_values) {
                $this->assertTrue(isset($scale_array['values']));

                $expected = scale_value::repository()
                    ->where('scaleid', $scale->id)
                    ->order_by('sortorder', 'desc')
                    ->get();

                $this->assertEqualsCanonicalizing($expected->pluck('name'), $scale->values->pluck('name'));

                $scale->values->map(function (scale_value $scale_value) use ($expected) {
                    $this->assertEqualsCanonicalizing($expected->item($scale_value->id)->to_array(), $scale_value->to_array());
                });

                unset($scale_array['values']);
            } else {
                $this->assertFalse(isset($scale_array['values']));
            }

            $this->assertEqualsCanonicalizing($exp, $scale_array);
        });
    }

    /**
     * Create testing data
     *
     * @return array
     */
    protected function create_data() {
        // Let's create 3 scales
        $scales = new collection();

        $scales->append($this->generator()->create_scale('comp', ['name' => 'Scale 1']));
        $scales->append($this->generator()->create_scale('comp', ['name' => 'Scale 2']));
        $scales->append($this->generator()->create_scale('comp', ['name' => 'Scale 3']));

        // Let's create 4 frameworks
        $frameworks = new collection();

        $frameworks->append($this->generator()->create_comp_frame(['scale' => $scales->item(0)->id]));
        $frameworks->append($this->generator()->create_comp_frame(['scale' => $scales->item(1)->id]));
        $frameworks->append($this->generator()->create_comp_frame(['scale' => $scales->item(2)->id]));
        $frameworks->append($this->generator()->create_comp_frame(['scale' => $scales->item(2)->id]));


        // Let's create 5 competencies
        $competencies = new collection();

        $competencies->append($this->generator()->create_comp(['frameworkid' => $frameworks->item(0)->id]));
        $competencies->append($this->generator()->create_comp(['frameworkid' => $frameworks->item(0)->id]));
        $competencies->append($this->generator()->create_comp(['frameworkid' => $frameworks->item(1)->id]));
        $competencies->append($this->generator()->create_comp(['frameworkid' => $frameworks->item(2)->id]));
        $competencies->append($this->generator()->create_comp(['frameworkid' => $frameworks->item(3)->id]));

        return [
            'scales' => $scales,
            'frameworks' => $frameworks,
            'competencies' => $competencies,
        ];
    }

    /**
     * @return totara_hierarchy_generator
     */
    protected function generator() {
        return $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
    }
}
