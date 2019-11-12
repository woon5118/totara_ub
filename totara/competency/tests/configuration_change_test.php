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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

use totara_competency\entities\configuration_change;
use totara_competency\entities\scale_value;
use totara_core\advanced_feature;

class totara_competency_configuration_change_testcase extends advanced_testcase {

    public function test_configuration_change_is_logged() {
        advanced_feature::enable('competency_assignment');

        $scale1 = $this->create_scale();
        $scale2 = $this->create_scale();
        $comp1 = $this->create_competency($scale1->id);
        $comp2 = $this->create_competency($scale1->id);
        $comp3 = $this->create_competency($scale2->id);
        $comp4 = $this->create_competency($scale2->id);
        $comp5 = $this->create_competency($scale2->id);

        $scale_value = scale_value::repository()
            ->where('scaleid', $scale1->id)
            ->order_by('sortorder', 'DESC')
            ->first();

        $this->assertEquals(0, configuration_change::repository()->count());

        configuration_change::min_proficiency_change($scale1->id, $scale_value->id);

        // Two competency are affected by that min proficiency value change

        /** @var configuration_change $change */
        $configuration_changes = configuration_change::repository()->get();
        $this->assertEquals(2, $configuration_changes->count());
        $this->assertEqualsCanonicalizing([$comp1->id, $comp2->id], $configuration_changes->pluck('comp_id'));
        $this->assertEquals(
            [configuration_change::CHANGED_MIN_PROFICIENCY],
            array_unique($configuration_changes->pluck('change_type'))
        );
        $this->assertEquals(
            [json_encode(['new_min_proficiency_id' => $scale_value->id])],
            array_unique($configuration_changes->pluck('related_info'))
        );
    }

    protected function create_scale() {
        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->generator()->hierarchy_generator();

        return $hierarchy_generator->create_scale(
            'comp',
            ['name' => 'Test scale', 'description' => 'Test scale'],
            [
                1 => ['name' => 'No clue', 'proficient' => 0, 'sortorder' => 5, 'default' => 1],
                2 => ['name' => 'Learning', 'proficient' => 0, 'sortorder' => 4, 'default' => 0],
                3 => ['name' => 'Getting there', 'proficient' => 0, 'sortorder' => 3, 'default' => 0],
                4 => ['name' => 'Almost there', 'proficient' => 1, 'sortorder' => 2, 'default' => 0],
                5 => ['name' => 'Arrived', 'proficient' => 1, 'sortorder' => 1, 'default' => 0],
            ]
        );
    }

    protected function create_competency(int $scale_id) {
        $hierarchy_generator = $this->generator()->hierarchy_generator();

        // We don't want the create event fired here
        $sink = $this->redirectEvents();

        $fw = $hierarchy_generator->create_comp_frame(['fullname' => 'Framework one', 'idnumber' => 'f1', 'scale' => $scale_id]);
        $comp = $hierarchy_generator->create_comp([
            'frameworkid' => $fw->id,
            'idnumber' => 'c1',
            'parentid' => 0,
        ]);

        // Stop redirecting events from now
        $sink->close();

        return $comp;
    }

    /**
     * @return totara_competency_generator
     */
    protected function generator() {
        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        return $competency_generator;
    }

}
