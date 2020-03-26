<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\participant_section;
use mod_perform\models\activity\section;
use mod_perform\state\invalid_state_switch_exception;
use mod_perform\state\participant_section\complete;
use mod_perform\state\participant_section\incomplete;
use mod_perform\state\participant_section\not_started;
use mod_perform\state\state_helper;

require_once(__DIR__.'/relationship_testcase.php');

/**
 * @group perform
 */
class mod_perform_participant_section_model_testcase extends advanced_testcase {

    public function state_transitions_data_provider() {
        return [
            [not_started::class, incomplete::class, true],
            [not_started::class, complete::class, true],
            [incomplete::class, complete::class, true],
            [not_started::class, not_started::class, false],
            [complete::class, incomplete::class, false],
        ];
    }

    /**
     * Check switching section states.
     *
     * @dataProvider state_transitions_data_provider
     * @param string $initial_state_class
     * @param string $target_state_class
     * @param bool $transition_possible
     */
    public function test_switch_state(string $initial_state_class, string $target_state_class, bool $transition_possible = true) {
        $this->setAdminUser();
        $subject_user = self::getDataGenerator()->create_user();
        $other_participant = self::getDataGenerator()->create_user();

        $participant_section = $this->create_participant_section($subject_user, $other_participant);
        // Set and verify initial state.
        $entity = participant_section_entity::repository()->find($participant_section->get_id());
        $entity->status = $initial_state_class::get_code();
        $entity->update();
        $participant_section = participant_section::load_by_entity($entity);
        $this->assertInstanceOf($initial_state_class, $participant_section->get_state());

        $this->setUser($subject_user);
        $sink = $this->redirectEvents();

        if (!$transition_possible) {
            $this->expectException(invalid_state_switch_exception::class);
            $this->expectExceptionMessage('Cannot switch');
        }

        $participant_section->switch_state($target_state_class);

        $db_status = participant_section_entity::repository()->find($participant_section->get_id())->status;
        $this->assertEquals($target_state_class::get_code(), $db_status);
        $this->assertInstanceOf($target_state_class, $participant_section->get_state());

        // Check that event has been triggered.
        $this->assert_section_updated_event($sink, $participant_section, $subject_user->id);
    }

    public function test_state_switch_actor_condition() {
        $this->setAdminUser();

        $subject_user = self::getDataGenerator()->create_user();
        $other_participant = self::getDataGenerator()->create_user();

        $participant_section = $this->create_participant_section($subject_user, $other_participant);

        $this->setUser($other_participant);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Cannot switch');
        $participant_section->switch_state(complete::class);
    }

    public function test_duplicate_state_codes() {
        $all_states = state_helper::get_all_states('participant_section');
        $this->assertGreaterThanOrEqual(3, count($all_states));
        $all_codes = array_unique(array_map(function (string $state_class) {
            return call_user_func([$state_class, 'get_code']);
        }, $all_states));
        $this->assertCount(count($all_states), $all_codes);
    }

    /**
     * @param phpunit_event_sink $sink
     * @param participant_section $participant_section
     * @param int $user_id
     */
    private function assert_section_updated_event(
        phpunit_event_sink $sink,
        participant_section $participant_section,
        int $user_id
    ) {
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('mod_perform\event\participant_section_status_updated', $event);
        $this->assertEquals($participant_section->get_id(), $event->objectid);
        $this->assertEquals($user_id, $event->relateduserid);
        $activity = activity::load_by_entity($participant_section->section->activity);
        $this->assertEquals($activity->get_context(), $event->get_context());

        $sink->close();
    }

    private function create_participant_section(stdClass $subject_user, stdClass $other_participant) {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $perform_generator->create_activity_in_container();
        $section = section::create($activity, 'section name one');
        $subject_instance = $perform_generator->create_subject_instance([
            'activity_id' => $activity->id,
            'subject_user_id' => $subject_user->id,
            'other_participant_id' => $other_participant->id,
            'subject_is_participating' => true,
        ]);
        $participant_instance = \mod_perform\entities\activity\participant_instance::repository()
            ->where('subject_instance_id', $subject_instance->get_id())
            ->where('participant_id', $subject_user->id)
            ->one(true);
        return $perform_generator->create_participant_section($section->get_id(), $participant_instance->id);
    }
}