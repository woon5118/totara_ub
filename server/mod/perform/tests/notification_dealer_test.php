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

use mod_perform\constants;
use mod_perform\models\activity\notification_recipient;
use mod_perform\notification\factory;
use mod_perform\notification\internals\message;
use totara_job\job_assignment;

require_once(__DIR__ . '/notification_testcase.php');

/**
 * Class mod_perform_notification_dealer_testcase
 *
 * @group perform
 */
class mod_perform_notification_dealer_testcase extends mod_perform_notification_testcase {
    public function test_post() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = $this->create_notification($activity, 'instance_created', true);
        $relationships = $this->create_section_relationships($section);
        $sink = factory::create_sink();

        $user = $this->getDataGenerator()->create_user(['username' => 'subject']);
        $manager = $this->getDataGenerator()->create_user(['username' => 'manager']);
        $appraiser = $this->getDataGenerator()->create_user(['username' => 'appraiser']);

        $userja = job_assignment::create_default($user->id, ['appraiserid' => $appraiser->id]);
        job_assignment::create_default($manager->id, ['managerjaid' => $userja->id]);

        $this->toggle_recipients($notification, [
            constants::RELATIONSHIP_SUBJECT => true,
            // constants::RELATIONSHIP_MANAGER => default to false,
            constants::RELATIONSHIP_APPRAISER => true,
        ]);
        $recipients = notification_recipient::load_by_notification($notification, true);
        $this->assertCount(2, $recipients);

        $sink->clear();
        $dealer = factory::create_dealer_on_notification($notification);
        $this->assertNotNull($dealer);
        $this->redirect_messages();
        $time = time();
        $dealer->post($user, $relationships[constants::RELATIONSHIP_SUBJECT]);
        $dealer->post($manager, $relationships[constants::RELATIONSHIP_MANAGER]);
        $dealer->post($appraiser, $relationships[constants::RELATIONSHIP_APPRAISER]);
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

        $messages = $sink->get_all();
        $this->assertCount(2, $messages);

        $message = $sink->get_by_relationship(constants::RELATIONSHIP_SUBJECT)->first();
        /** @var message|null $message */
        $this->assertNotNull($message);
        $this->assertEqualsWithDelta($time, $message->sent_at, 2);

        $message = $sink->get_by_relationship(constants::RELATIONSHIP_MANAGER)->first();
        /** @var message|null $message */
        $this->assertNull($message);

        $message = $sink->get_by_relationship(constants::RELATIONSHIP_APPRAISER)->first();
        /** @var message|null $message */
        $this->assertNotNull($message);
        $this->assertEqualsWithDelta($time, $message->sent_at, 2);
    }
}
