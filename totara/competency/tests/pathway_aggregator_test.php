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
 * @package totara_competency
 */

use totara_competency\pathway_aggregator;
use totara_competency\entities\pathway_achievement;
use totara_competency\pathway;
use totara_competency\entities\scale_value;

class totara_competency_pathway_aggregator_testcase extends advanced_testcase {

    public function test_aggregate_with_no_users() {

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $pathway = $competency_generator->create_test_pathway();

        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([]);
    }

    public function test_aggregate_with_one_complete_user() {

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $pathway = $competency_generator->create_test_pathway($competency, pathway::PATHWAY_STATUS_ACTIVE);
        /** @var scale_value $scale_value */
        $scale_value = $competency->scale->sorted_values_high_to_low->first();
        $pathway->set_test_aggregate_current_value($scale_value);

        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        $achievement = pathway_achievement::get_current($pathway, $user->id);

        $this->assertEquals($scale_value->id, $achievement->scale_value_id);
    }

    public function test_aggregate_with_one_incomplete_user() {

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $pathway = $competency_generator->create_test_pathway($competency, pathway::PATHWAY_STATUS_ACTIVE);
        $pathway->set_test_aggregate_current_value(null);

        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        $achievement = pathway_achievement::get_current($pathway, $user->id);

        $this->assertNull($achievement->scale_value_id);
    }

    public function test_aggregate_with_changing_completion_values() {

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $pathway = $competency_generator->create_test_pathway($competency, pathway::PATHWAY_STATUS_ACTIVE);
        $pathway->set_test_aggregate_current_value(null);

        // Start with creating a null record.
        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $this->assertNull($achievement->scale_value_id);


        /** @var scale_value[] $scale_values */
        $scale_values = $competency->scale->sorted_values_high_to_low->all();
        $scale_value1 = array_pop($scale_values);
        $pathway->set_test_aggregate_current_value($scale_value1);

        // Now we go to a scale value.
        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $this->assertEquals($scale_value1->id, $achievement->scale_value_id);

        $scale_value2 = array_pop($scale_values);
        $pathway->set_test_aggregate_current_value($scale_value2);

        // Now we go to another scale value.
        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $this->assertEquals($scale_value2->id, $achievement->scale_value_id);

        $pathway->set_test_aggregate_current_value(null);

        // Now back to null.
        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $this->assertNull($achievement->scale_value_id);

        $archived_achievements = pathway_achievement::repository()
            ->where('pathway_id', '=', $pathway->get_id())
            ->where('user_id', '=', $user->id)
            ->where('status', '=', pathway_achievement::STATUS_ARCHIVED)
            ->get();

        $this->assertCount(3, $archived_achievements);
    }

    public function test_same_value_aggregation_updated() {

        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $pathway = $competency_generator->create_test_pathway($competency, pathway::PATHWAY_STATUS_ACTIVE);
        /** @var scale_value $scale_value */
        $scale_value = $competency->scale->sorted_values_high_to_low->first();
        $pathway->set_test_aggregate_current_value($scale_value);

        $aggregator = new pathway_aggregator($pathway);
        $aggregation_time1 = 100;
        $aggregator->aggregate([$user->id], $aggregation_time1);

        $achievement = pathway_achievement::get_current($pathway, $user->id);

        $this->assertEquals($scale_value->id, $achievement->scale_value_id);
        $this->assertEquals($aggregation_time1, $achievement->last_aggregated);

        $aggregator = new pathway_aggregator($pathway);
        $aggregation_time2 = 200;
        $aggregator->aggregate([$user->id], $aggregation_time2);

        $achievement = pathway_achievement::get_current($pathway, $user->id);

        $this->assertEquals($scale_value->id, $achievement->scale_value_id);
        $this->assertEquals($aggregation_time2, $achievement->last_aggregated);

        // Should be no archived achievements if the first value never changed.
        $archived_achievements = pathway_achievement::repository()
            ->where('pathway_id', '=', $pathway->get_id())
            ->where('user_id', '=', $user->id)
            ->where('status', '=', pathway_achievement::STATUS_ARCHIVED)
            ->get();

        $this->assertCount(0, $archived_achievements);
    }

    public function test_multiple_users_and_multiple_pathways() {

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $pathway1 = $competency_generator->create_test_pathway($competency);
        /** @var scale_value[] $scale_values */
        $scale_values = $competency->scale->sorted_values_high_to_low->all();
        $scale_value1 = array_pop($scale_values);
        $scale_value2 = array_pop($scale_values);

        // User1 will get scale value1, all other users get scale_value2.
        $pathway1->set_test_aggregate_current_value(
            function ($user_id) use ($user1, $scale_value1, $scale_value2) {
                if ($user_id == $user1->id) {
                    return $scale_value1;
                } else {
                    return $scale_value2;
                }
            });

        $pathway2 = $competency_generator->create_test_pathway($competency);

        // User1 will get null, all other users get scale_value1.
        $pathway2->set_test_aggregate_current_value(
            function ($user_id) use ($user1, $scale_value1, $scale_value2) {
                if ($user_id == $user1->id) {
                    return null;
                } else {
                    return $scale_value1;
                }
            });

        $aggregator = new pathway_aggregator($pathway1);
        $aggregator->aggregate([$user1->id, $user2->id]);

        $this->assertEquals($scale_value1->id, pathway_achievement::get_current($pathway1, $user1->id)->scale_value_id);
        $this->assertEquals($scale_value2->id, pathway_achievement::get_current($pathway1, $user2->id)->scale_value_id);
        // User3 should not have been aggregated.
        $this->assertNull(pathway_achievement::get_current($pathway1, $user3->id)->scale_value_id);

        $aggregator = new pathway_aggregator($pathway2);
        $aggregator->aggregate([$user1->id, $user3->id]);

        $this->assertNull(pathway_achievement::get_current($pathway2, $user1->id)->scale_value_id);
        // User 2 was not aggregated for this pathway.
        $this->assertNull(pathway_achievement::get_current($pathway2, $user2->id)->scale_value_id);
        $this->assertEquals($scale_value1->id, pathway_achievement::get_current($pathway2, $user3->id)->scale_value_id);

        // Check the pathway1 values again to ensure they weren't affected in any way.
        $this->assertEquals($scale_value1->id, pathway_achievement::get_current($pathway1, $user1->id)->scale_value_id);
        $this->assertEquals($scale_value2->id, pathway_achievement::get_current($pathway1, $user2->id)->scale_value_id);
        $this->assertNull(pathway_achievement::get_current($pathway1, $user3->id)->scale_value_id);

        // No archiving was done here.
        $archived_achievements = pathway_achievement::repository()
            ->where('status', '=', pathway_achievement::STATUS_ARCHIVED)
            ->get();

        $this->assertCount(0, $archived_achievements);
    }

    public function test_archived_pathway_achievements_not_reaggregated() {
        $user = $this->getDataGenerator()->create_user();

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
        $competency = $competency_generator->create_competency();

        $pathway = $competency_generator->create_test_pathway($competency, pathway::PATHWAY_STATUS_ACTIVE);

        /** @var scale_value[] $scale_values */
        $scale_values = $competency->scale->sorted_values_high_to_low->all();
        $scale_value1 = array_pop($scale_values);
        $pathway->set_test_aggregate_current_value($scale_value1);

        // Go to a scale value 1.
        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        // Make sure that has been set correctly.
        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $this->assertEquals($scale_value1->id, $achievement->scale_value_id);

        $archived_achievements = pathway_achievement::repository()
            ->where('pathway_id', '=', $pathway->get_id())
            ->where('user_id', '=', $user->id)
            ->where('status', '=', pathway_achievement::STATUS_ARCHIVED)
            ->get();

        // Nothing archived yet.
        $this->assertCount(0, $archived_achievements);

        $scale_value2 = array_pop($scale_values);
        $pathway->set_test_aggregate_current_value($scale_value2);

        // Now we go to another scale value.
        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        $achievement = pathway_achievement::get_current($pathway, $user->id);
        $this->assertEquals($scale_value2->id, $achievement->scale_value_id);

        $archived_achievements = pathway_achievement::repository()
            ->where('pathway_id', '=', $pathway->get_id())
            ->where('user_id', '=', $user->id)
            ->where('status', '=', pathway_achievement::STATUS_ARCHIVED)
            ->get();

        $this->assertCount(1, $archived_achievements);

        // This is what should not change.
        $archived_last_aggregated = $archived_achievements->first()->last_aggregated;

        // We reaggregate based on timestamps.
        $this->waitForSecond();

        $aggregator = new pathway_aggregator($pathway);
        $aggregator->aggregate([$user->id]);

        // Check the new current value is the same. Since nothing changed. We're also hoping those no errors
        // around double ups here.
        $refetched_achievement = pathway_achievement::get_current($pathway, $user->id);
        $this->assertEquals($scale_value2->id, $refetched_achievement->scale_value_id);
        $this->assertEquals($achievement->id, $refetched_achievement->id);

        // The current achievement will have still been reaggregated.
        $this->assertGreaterThan($achievement->last_aggregated, $refetched_achievement->last_aggregated);

        // There should be no extra archived achievements.
        $archived_achievements = pathway_achievement::repository()
            ->where('pathway_id', '=', $pathway->get_id())
            ->where('user_id', '=', $user->id)
            ->where('status', '=', pathway_achievement::STATUS_ARCHIVED)
            ->get();

        $this->assertCount(1, $archived_achievements);

        // And the existing archived achievement should not have been reaggregated.
        $this->assertEquals($archived_last_aggregated, $archived_achievements->first()->last_aggregated);
    }
}
