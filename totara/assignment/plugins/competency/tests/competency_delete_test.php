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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_assignment
 * @category test
 */

use competency as competency_hierarchy;
use tassign_competency\entities\assignment;
use tassign_competency\entities\competency;

defined('MOODLE_INTERNAL') || die();

class tassign_competency_competency_delete_testcase extends advanced_testcase {

    public function test_it_removes_assignments_when_competency_deleted() {
        $comps = $this->generate_competencies();

        $ids = competency::repository()->where('path', 'like_starts_with', '/' . $comps[0]->id)
            ->get()->pluck('id');

        $hierarchy = new competency_hierarchy();
        $hierarchy->delete_hierarchy_item($comps[0]->id);

        $this->assertTrue(assignment::repository()->where('competency_id', $ids)->does_not_exist());

        $this->assertTrue(assignment::repository()->where('competency_id', $comps[3]->id)->exists());
    }

    /**
     * Create a few competencies with knows names to test search
     */
    protected function generate_competencies() {
        $this->resetAfterTest();

        $comps = [];

        $fw = $this->generator()->hierarchy_generator()->create_comp_frame([]);
        $type = $this->generator()->hierarchy_generator()->create_comp_type(['idnumber' => 'type1']);

        $comps[] = $comp_one = $this->generator()->create_competency([
            'shortname' => 'acc',
            'fullname' => 'Accounting',
            'description' => 'Counting profits',
            'idnumber' => 'accc',
            'typeid' => $type,
        ], $fw->id);

        $comps[] = $comp_two = $this->generator()->create_competency([
            'shortname' => 'c-chef',
            'fullname' => 'Chef proficiency',
            'description' => 'Bossing around',
            'idnumber' => 'cook-chef-c',
            'typeid' => $type,
            'parentid' => $comp_one->id,
        ], $fw->id);

        $comps[] = $comp_three = $this->generator()->create_competency([
            'shortname' => 'des',
            'fullname' => 'Designing interiors',
            'description' => 'Decorating things',
            'idnumber' => 'des',
            'parentid' => $comp_two->id,
            'typeid' => $type,
        ], $fw->id);

        $comps[] = $comp_four =  $this->generator()->create_competency([
            'shortname' => 'c-baker',
            'fullname' => 'Baking skill-set',
            'description' => 'Baking amazing things',
            'idnumber' => 'cook-baker',
            'typeid' => $type,
        ], $fw->id);

        // Create an assignment for a competency
        foreach ($comps as $competency) {
            $this->generator()->create_user_assignment($competency->id, null, ['type' => assignment::TYPE_ADMIN]);
        }

        return $comps;
    }

    /**
     * Get hierarchy specific generator
     *
     * @return tassign_competency_generator
     */
    protected function generator() {
        return $this->getDataGenerator()->get_plugin_generator('tassign_competency');
    }
}