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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_topic
 */
defined('MOODLE_INTERNAL') || die();

use totara_topic\task\delete_notify_task;

/**
 * This test is here to make sure that if anyone changes the behaviour
 * of the adhoc task will have to modify this test as well.
 *
 * Just a reminder to keep that person to take account of regression :)
 */
class totara_topic_delete_notify_task_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_execute_with_no_actor_data(): void {
        $task = new delete_notify_task();
        $task->set_custom_data([
            'topicvalue' => 'd',
            'components' => [],
        ]);

        $task->execute();
        $debug_messages = $this->getDebuggingMessages();

        $this->assertCount(1, $debug_messages);

        $debug_message = reset($debug_messages);
        $this->assertSame(
            "The custom data for the task does not have key 'actor'",
            $debug_message->message
        );

        $this->resetDebugging();
    }

    /**
     * @return void
     */
    public function test_execute_with_no_topic_value(): void {
        $task = new delete_notify_task();
        $task->set_custom_data([
            'actor' => 42,
            'components' => [],
        ]);

        $task->execute();
        $debug_messages = $this->getDebuggingMessages();

        $this->assertCount(1, $debug_messages);

        $debug_message = reset($debug_messages);
        $this->assertSame(
            "The custom data for the task does not have key 'topicvalue'",
            $debug_message->message
        );

        $this->resetDebugging();
    }

    /**
     * @return void
     */
    public function test_execute_with_no_components_data(): void {
        $task = new delete_notify_task();
        $task->set_custom_data([
            'actor' => 42,
            'topicvalue' => 'd'
        ]);

        $task->execute();
        $debug_messages = $this->getDebuggingMessages();

        $this->assertCount(1, $debug_messages);

        $debug_message = reset($debug_messages);
        $this->assertSame(
            "The custom data for the task does not have key 'components'",
            $debug_message->message
        );

        $this->resetDebugging();
    }
}