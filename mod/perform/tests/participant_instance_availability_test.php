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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\event\participant_instance_availability_closed;
use mod_perform\event\participant_instance_progress_updated;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\activity_setting;
use mod_perform\models\activity\participant_instance;
use mod_perform\observers\participant_instance_availability;
use mod_perform\state\participant_instance\closed;
use mod_perform\state\participant_instance\complete;
use mod_perform\state\participant_instance\in_progress;
use mod_perform\state\participant_instance\not_started;
use mod_perform\state\participant_instance\open;
use mod_perform\state\participant_instance\participant_instance_availability as participant_instance_availability_state;
use mod_perform\state\state;
use totara_core\relationship\resolvers\subject;
use totara_core\relationship\relationship_provider;
use totara_job\relationship\resolvers\manager;

require_once(__DIR__ . '/state_testcase.php');

/**
 * @group perform
 */
class mod_perform_participant_instance_availability_testcase extends state_testcase {

    protected static function get_object_type(): string {
        return participant_instance_availability_state::get_type();
    }

    public function state_transitions_data_provider(): array {
        return [
            'Open to Closed' => [open::class, closed::class, true],
            'Closed to Open' => [closed::class, open::class, false],
            'Open to Open' => [open::class, open::class, false],
            'Closed to Closed' => [closed::class, closed::class, false],
        ];
    }

    /**
     * @dataProvider state_transitions_data_provider
     * @param string $initial_state_class
     * @param string $final_state_class
     * @param bool $can_switch
     */
    public function test_state_switching(string $initial_state_class, string $final_state_class, bool $can_switch): void {
        [$participant_instance] = $this->create_data();

        /** @var state $initial_state */
        $initial_state = new $initial_state_class($participant_instance);

        $this->assertEquals($can_switch, $initial_state->can_switch($final_state_class));
    }

    public function test_participant_instance_closed_upon_instance_completion(): void {
        /**
         * @var participant_instance $participant1
         * @var participant_instance $participant2
         * @var participant_instance_entity $participant1_entity
         */
        [$participant1, $participant2, $participant1_entity] = $this->create_data();

        $participant1->update_progress_status();
        $participant1_entity->refresh();

        $this->assertEquals(closed::get_code(), $participant1->availability_state::get_code());
        $this->assertEquals(open::get_code(), $participant2->availability_state::get_code());
    }

    public function test_participant_instance_not_closed_when_instance_not_complete(): void {
        /**
         * @var participant_instance $participant1
         * @var participant_instance $participant2
         * @var participant_instance_entity $participant1_entity
         */
        [$participant1, $participant2, $participant1_entity] = $this->create_data();

        $participant1_entity->progress = not_started::get_code();
        $participant1_entity->save();

        participant_instance_availability::close_completed_participant_instance(
            participant_instance_progress_updated::create_from_participant_instance($participant1)
        );
        $participant1_entity->refresh();

        $this->assertEquals(open::get_code(), $participant1->availability);
        $this->assertEquals(open::get_code(), $participant2->availability);
    }

    public function test_participant_instance_closed_event(): void {
        /**
         * @var participant_instance $participant1
         * @var participant_instance $participant2
         * @var participant_instance_entity $participant1_entity
         */
        [$participant1, $participant2, $participant1_entity] = $this->create_data();

        $participant1_entity->progress = complete::get_code();
        $participant1_entity->save();
        $event_sink = $this->redirectEvents();
        participant_instance_availability::close_completed_participant_instance(
            participant_instance_progress_updated::create_from_participant_instance($participant1)
        );
        $event_sink->close();
        $events = $event_sink->get_events();
        $participant1_entity->refresh();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(participant_instance_availability_closed::class, reset($events));

        $this->assertEquals(closed::get_code(), $participant1->availability_state::get_code());
        $this->assertEquals(open::get_code(), $participant2->availability_state::get_code());
    }

    public function test_instance_is_not_closed_if_activity_close_on_completion_is_not_set(): void {
        /**
         * @var participant_instance $participant1
         * @var participant_instance $participant2
         * @var participant_instance_entity $participant1_entity
         * @var activity $activity
         */
        [$participant1, $participant2, $participant1_entity, $activity] = $this->create_data();

        $activity->settings->update([activity_setting::CLOSE_ON_COMPLETION => false]);

        $event_sink = $this->redirectEvents();
        $participant1->update_progress_status();
        $participant1_entity->refresh();
        $event_sink->close();
        $events = $event_sink->get_events();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(participant_instance_progress_updated::class, reset($events));

        $this->assertEquals(open::get_code(), $participant1->availability_state::get_code());
        $this->assertEquals(open::get_code(), $participant2->availability_state::get_code());
    }

    /**
     * Create activity and participant instances required for testing.
     *
     * @return array
     */
    private function create_data(): array {
        self::setAdminUser();

        /** @var mod_perform_generator $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        $activity = $generator->create_activity_in_container();
        $activity->settings->update([activity_setting::CLOSE_ON_COMPLETION => true]);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $subject_instance = $generator->create_subject_instance([
            'activity_id' => $activity->id,
            'subject_user_id' => $user1->id,
        ]);

        //$section1 = $generator->create_section($activity);

        $subject_relationship_id = relationship_provider::get_by_class(manager::class)->id;
        $manager_relationship_id = relationship_provider::get_by_class(subject::class)->id;

        $participant1_entity = $generator->create_participant_instance($user1, $subject_instance->id, $subject_relationship_id);
        $participant2_entity = $generator->create_participant_instance($user2, $subject_instance->id, $manager_relationship_id);

        $participant1_model = participant_instance::load_by_entity($participant1_entity);
        $participant2_model = participant_instance::load_by_entity($participant2_entity);

        $this->assertEquals(open::get_code(), $participant1_model->availability_state::get_code());
        $this->assertEquals(open::get_code(), $participant2_model->availability_state::get_code());

        $participant1_entity->progress = in_progress::get_code();
        $participant1_entity->save();
        $participant2_entity->progress = in_progress::get_code();
        $participant2_entity->save();

        return [$participant1_model, $participant2_model, $participant1_entity, $activity];
    }

}
