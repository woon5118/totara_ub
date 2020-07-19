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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\entities\activity\notification_message as notification_message_entity;
use mod_perform\models\activity\notification_recipient;
use mod_perform\notification\factory;
use totara_job\job_assignment;
use totara_core\relationship\resolvers\subject;
use totara_job\relationship\resolvers\appraiser;
use totara_job\relationship\resolvers\manager;

require_once(__DIR__ . '/notification_testcase.php');

class mod_perform_notification_dealer_testcase extends mod_perform_notification_testcase {
    public function test_post() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = $this->create_notification($activity, 'instance_created', true);
        $relationships = $this->create_section_relationships($section);

        $user = $this->getDataGenerator()->create_user();
        $manager = $this->getDataGenerator()->create_user();
        $appraiser = $this->getDataGenerator()->create_user();

        $userja = job_assignment::create_default($user->id, ['appraiserid' => $appraiser->id]);
        job_assignment::create_default($manager->id, ['managerjaid' => $userja->id]);

        $this->toggle_recipients($notification, [
            subject::class => true,
            // manager::class => default to false,
            appraiser::class => true,
        ]);
        $recipients = notification_recipient::load_by_notification($notification, true);
        $this->assertCount(2, $recipients);

        $dealer = factory::create_dealer($notification, $user->id, $userja->id);
        $this->redirect_messages();
        $time = time();
        $dealer->post();
        $messages = $this->get_messages();

        $this->assertCount(2, $messages);
        $filter_by_user = function (int $userid) {
            return function ($e) use ($userid) {
                return $e->useridto == $userid;
            };
        };

        // user
        $message = current(array_filter($messages, $filter_by_user($user->id)));
        $this->assertNotEmpty($message);
        $this->assertStringContainsString(' is ready for you to complete', $message->fullmessage);

        // appraiser
        $message = current(array_filter($messages, $filter_by_user($appraiser->id)));
        $this->assertNotEmpty($message);
        $this->assertStringContainsString(' you have been selected to participate in the following activity', $message->fullmessage);

        $entities = notification_message_entity::repository()->get();
        $this->assertCount(2, $entities);

        $entity = $entities->find('core_relationship_id', $this->perfgen->get_core_relationship(subject::class)->id);
        /** @var notification_message_entity $entity */
        $this->assertNotNull($entity);
        $this->assertEqualsWithDelta($time, $entity->sent_at, 2);

        $entity = $entities->find('core_relationship_id', $this->perfgen->get_core_relationship(manager::class)->id);
        $this->assertNull($entity);

        $entity = $entities->find('core_relationship_id', $this->perfgen->get_core_relationship(appraiser::class)->id);
        /** @var notification_message_entity $entity */
        $this->assertNotNull($entity);
        $this->assertEqualsWithDelta($time, $entity->sent_at, 2);
    }
}
