<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package totara_engage
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../totara/core/tests/language_pack_faker_trait.php');

use core\task\manager;
use totara_engage\task\share_notify_task;

class totara_engage_share_notify_task_testcase extends advanced_testcase {
    use language_pack_faker_trait;

    public function test_recipients_language_setting_is_observed(): void {
        $generator = self::getDataGenerator();
        $fake_language = 'xo_ox';
        $this->add_fake_language_pack(
            $fake_language,
            [
                'totara_engage' => [
                    'share_message_subject' => 'Fake language subject string'
                ]
            ]
        );

        $user_one = $generator->create_user(['lang' => $fake_language]);
        $user_two = $generator->create_user();

        $task = new share_notify_task();
        $task->set_component('totara_engage');
        $task->set_custom_data(
            [
                'component' => 'resource',
                'recipient_id' => $user_one->id,
                'sharer_id' => $user_two->id,
                'item_name' => 'Resource test name',
            ]
        );

        manager::queue_adhoc_task($task);

        // Start the sink and execute the adhoc tasks.
        $message_sink = phpunit_util::start_message_redirection();
        $this->execute_adhoc_tasks();

        $messages = $message_sink->get_messages();
        self::assertCount(1, $messages);
        $message = reset($messages);

        self::assertEquals($user_one->id, $message->useridto);
        self::assertEquals('Fake language subject string', $message->subject);
    }
}