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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use core\collection;
use core\entities\user;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\models\activity\activity;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\models\activity\element_response;
use mod_perform\models\activity\element_validation_error;
use mod_perform\models\activity\participant_section;
use mod_perform\state\invalid_state_switch_exception;
use mod_perform\state\participant_section\complete;
use mod_perform\state\participant_section\incomplete;
use mod_perform\state\participant_section\not_started;
use mod_perform\state\state_helper;

require_once(__DIR__ . '/relationship_testcase.php');

/**
 * @group perform
 */
class mod_perform_participant_section_model_testcase extends advanced_testcase {

    public function state_transitions_data_provider(): array {
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
    public function test_switch_state(
        string $initial_state_class,
        string $target_state_class,
        bool $transition_possible = true
    ): void {
        $this->setAdminUser();
        $subject_user = self::getDataGenerator()->create_user();
        $other_participant = self::getDataGenerator()->create_user();

        // Set and verify initial state.
        $entity = $this->create_participant_section($subject_user, $other_participant);
        $entity->progress = $initial_state_class::get_code();
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

        $db_progress = participant_section_entity::repository()->find($participant_section->get_id())->progress;
        $this->assertEquals($target_state_class::get_code(), $db_progress);
        $this->assertInstanceOf($target_state_class, $participant_section->get_state());

        // Check that event has been triggered.
        $this->assert_section_updated_event($sink, $participant_section, $subject_user->id);
    }

    public function test_duplicate_state_codes(): void {
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
    ): void {
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('mod_perform\event\participant_section_progress_updated', $event);
        $this->assertEquals($participant_section->get_id(), $event->objectid);
        $this->assertEquals($user_id, $event->relateduserid);

        $this->assertEquals($participant_section->get_context(), $event->get_context());

        $only_activity = new activity(activity_entity::repository()->one());

        $this->assertEquals($only_activity->get_context(), $event->get_context());

        $sink->close();
    }

    public function test_complete_success(): void {
        $participant_section = participant_section::load_by_entity($this->create_participant_section());

        $responses = new collection([
            $this->create_valid_element_response(),
            $this->create_valid_element_response(),
            $this->create_valid_element_response(),
        ]);

        self::assertEquals(
            not_started::get_code(),
            $participant_section->progress,
            'Participant section should start with the incomplete status'
        );

        $participant_section->set_element_responses($responses);
        $completion_success = $participant_section->complete();

        self::assertTrue($completion_success);

        self::assertEquals(
            complete::get_code(),
            $participant_section->progress,
            'Participant section should have the complete status'
        );

        foreach ($responses as $response) {
            self::assertTrue($response->was_saved, 'All element responses should been saved');
        }
    }

    /**
     * @dataProvider invalid_responses_provider
     * @param element_response ...$element_responses
     */
    public function test_complete_with_validation_errors(element_response ...$element_responses): void {
        $participant_section = participant_section::load_by_entity($this->create_participant_section());

        self::assertEquals(
            not_started::get_code(),
            $participant_section->progress,
            'Participant section should start with the incomplete status'
        );

        $participant_section->set_element_responses(new collection($element_responses));
        $completion_success = $participant_section->complete();

        self::assertFalse($completion_success);

        self::assertEquals(
            not_started::get_code(),
            $participant_section->progress,
            'Participant section should have the complete status'
        );

        foreach ($element_responses as $response) {
            self::assertFalse($response->was_saved, 'No element responses should have been saved');
        }
    }

    public function invalid_responses_provider(): array {
        return [
            'Partial success' => [
                $this->create_valid_element_response(),
                $this->create_valid_element_response(),
                $this->create_element_response_with_validation_errors(),
            ],
            'No success' => [
                $this->create_element_response_with_validation_errors(),
            ],
            'Multiple errors' => [
                $this->create_element_response_with_validation_errors(),
                $this->create_element_response_with_validation_errors(),
                $this->create_element_response_with_validation_errors(),
            ],
        ];
    }

    /**
     * @param stdClass|null $subject_user
     * @param stdClass|null $other_participant
     * @return participant_section_entity
     */
    private function create_participant_section(
        stdClass $subject_user = null,
        stdClass $other_participant = null
    ): participant_section_entity {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        self::setAdminUser();

        $subject_user_id = $subject_user ? $subject_user->id : user::logged_in()->id;
        $other_participant_id = $other_participant ? $other_participant->id : null;

        $perform_generator->create_subject_instance([
            'subject_user_id' => $subject_user_id,
            'other_participant_id' => $other_participant_id,
            'subject_is_participating' => true,
        ]);

        return participant_section_entity::repository()
            ->join([participant_instance_entity::TABLE, 'pi'], 'participant_instance_id', '=', 'id')
            ->where('pi.participant_id', $subject_user_id)
            ->one();
    }

    private function create_valid_element_response(): element_response {
        return new class extends element_response {
            public $was_saved = false;

            public function __construct() {
            }

            public function save(): element_response {
                $this->was_saved = true;
                return $this;
            }

            public function validate_response(): bool {
                $this->validation_errors = new collection();
                return true;
            }
        };
    }

    private function create_element_response_with_validation_errors(): element_response {
        return new class extends element_response {
            public $was_saved = false;

            public function __construct() {
            }

            public function save(): element_response {
                $this->was_saved = true;
                return $this;
            }

            public function validate_response(): bool {
                $error1 = new element_validation_error(1, 'one');
                $error2 = new element_validation_error(2, 'two');

                $this->validation_errors = new collection([$error1, $error2]);
                return false;
            }
        };
    }

}
