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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use core\orm\collection;
use core\orm\query\table;
use criteria_childcompetency\childcompetency;
use criteria_childcompetency\watcher\achievement;
use totara_competency\entities\competency as competency_entity;
use totara_competency\entities\competency_achievement as competency_achievement_entity;
use totara_competency\hook\competency_achievement_updated;
use totara_criteria\criterion;
use totara_criteria\entities\criteria_item as item_entity;
use totara_criteria\entities\criteria_item_record as item_record_entity;
use totara_criteria\entities\criteria_metadata as metadata_entity;
use totara_criteria\entities\criterion as criterion_entity;
use totara_criteria\hook\criteria_achievement_changed;

/**
 * Test hooks in this plugin
 */
class criteria_childcompetency_hooks_testcase extends advanced_testcase {

    /**
     * Test observer when a user's competency achievement changes
     */
    public function test_competency_achievement_updated() {
        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        /** @var totara_criteria_generator $criteria_generator */
        $criteria_generator = $this->getDataGenerator()->get_plugin_generator('totara_criteria');

        $framework = $competency_generator->create_framework();

        // Create 2 competencies, each with 1 child. First one has childcompetency criteria
        $competencies = [];
        foreach (['Comp A', 'Comp B'] as $name) {
            $competencies[$name] = $competency_generator->create_competency($name, $framework);

            $childname = $name . '-1';
            $competencies[$childname] = $competency_generator->create_competency(
                $childname,
                $framework,
                ['parentid' => $competencies[$name]->id]
            );
        }
        $criterion = $criteria_generator->create_childcompetency(['competency' => $competencies['Comp A']->id]);

        // Verify generated data
        $this->assertSame(4, competency_entity::repository()->count());
        $this->assertSame(1, criterion_entity::repository()->count());

        // The competency_created observer should have created the necessary items
        $this->verify_items($competencies['Comp A']->id, [$competencies['Comp A-1']->id]);
        $this->assertSame(0, item_record_entity::repository()->count());

        $user = $this->getDataGenerator()->create_user();

        // Now for the test :- We only check that the correct hooks are executed
        $sink = $this->redirectHooks();

        // First for child competency of parent without criteria
        $test_hook = $this->create_achievement_hook($competencies['Comp B-1']->id, $user->id);
        achievement::updated($test_hook);
        $this->assertSame(0, $sink->count());

        // Parent without criteria
        $test_hook = $this->create_achievement_hook($competencies['Comp B']->id, $user->id);
        achievement::updated($test_hook);
        $this->assertSame(0, $sink->count());

        // Child competency of parent with criteria
        $test_hook = $this->create_achievement_hook($competencies['Comp A-1']->id, $user->id);
        achievement::updated($test_hook);
        $this->verify_hook($sink, [$user->id => [$criterion->get_id()]]);
        $sink->clear();

        // Parent with criteria
        $test_hook = $this->create_achievement_hook($competencies['Comp A']->id, $user->id);
        achievement::updated($test_hook);
        $this->assertSame(0, $sink->count());

        $sink->close();
    }


    /**
     * Get the items linked to childcompetency criteria on the specified competency
     * @param int $competency_id
     * @return collection
     */
    private function get_items(int $competency_id): collection {
        $item_type = (new childcompetency())->get_items_type();

        return item_entity::repository()
            ->join((new table(metadata_entity::TABLE))->as('metadata'), 'criterion_id', '=', 'criterion_id')
            ->where('item_type', $item_type)
            ->where('metadata.metakey', criterion::METADATA_COMPETENCY_KEY)
            ->where('metadata.metavalue', $competency_id)
            ->get();
    }

    /**
     * Verify the criteria_items are as expected.
     *
     * @param int $competency_id - Competency
     * @param array $expected_item_ids - Exepected item ids
     */
    private function verify_items(int $competency_id, array $expected_item_ids) {
        $current_items = $this->get_items($competency_id);

        $this->assertSame(count($expected_item_ids), $current_items->count());
        foreach ($current_items as $item) {
            $this->assertTrue(in_array($item->item_id, $expected_item_ids));
        }
    }

    /**
     * Simulate generation of new competency achievement
     *
     * @param int $competency_id
     * @param int $user_id
     * @return competency_achievement_updated
     */
    private function create_achievement_hook(int $competency_id, int $user_id) {
        $aggregation_time = time();

        /** @var totara_competency_assignment_generator $assign_generator */
        $assign_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency')->assignment_generator();
        $assignment = $assign_generator->create_user_assignment($competency_id, $user_id);

        // We need to have a valid competency_achievement record for the event
        $new_comp_achievement = new competency_achievement_entity();
        $new_comp_achievement->comp_id = $competency_id;
        $new_comp_achievement->user_id = $user_id;
        $new_comp_achievement->assignment_id = $assignment->id;
        $new_comp_achievement->scale_value_id = 1;
        $new_comp_achievement->proficient = 0;
        $new_comp_achievement->status = competency_achievement_entity::ACTIVE_ASSIGNMENT;
        $new_comp_achievement->time_created = $aggregation_time;
        $new_comp_achievement->time_status = $aggregation_time;
        $new_comp_achievement->time_proficient = $aggregation_time;
        $new_comp_achievement->time_scale_value = $aggregation_time;
        $new_comp_achievement->last_aggregated = $aggregation_time;
        $new_comp_achievement->save();

        return new competency_achievement_updated($new_comp_achievement);
    }

    /**
     * Verify the executed hook
     *
     * @param phpunit_hook_sink $sink
     * @param array $expected_criteria_ids
     */
    private function verify_hook(phpunit_hook_sink $sink, array $expected_criteria_ids) {
        $this->assertSame(1, $sink->count());
        $hooks = $sink->get_hooks();
        /** @var criteria_achievement_changed $hook */
        $hook = reset($hooks);
        $this->assertInstanceOf(criteria_achievement_changed::class, $hook);
        $this->assertEqualsCanonicalizing($expected_criteria_ids, $hook->get_user_criteria_ids());
    }

}
