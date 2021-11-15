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
 * @package container_workspace
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/totara/core/tests/language_pack_faker_trait.php');

use container_workspace\task\notify_added_to_workspace_task;
use container_workspace\member\member;
use container_workspace\notification\workspace_notification;
use container_workspace\output\added_to_workspace_notification;

class container_workspace_notify_added_to_workspace_testcase extends advanced_testcase {
    use language_pack_faker_trait;
    /**
     * @throws coding_exception
     */
    public function test_execute_task_with_missing_data(): void {
        $task = new notify_added_to_workspace_task();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("There was no user's id or workspace's id was set");

        $task->execute();
    }

    /**
     * @return void
     */
    public function test_execute_task_with_missing_workspace_id(): void {
        $task = new notify_added_to_workspace_task();
        $task->set_custom_data(['user_id' => 42]);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("There was no user's id or workspace's id was set");

        $task->execute();
    }

    /**
     * @return void
     */
    public function test_execute_task_with_missing_user_id(): void {
        $task = new notify_added_to_workspace_task();
        $task->set_custom_data(['workspace_id' => 42]);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("There was no user's id or workspace's id was set");

        $task->execute();
    }

    /**
     * @return void
     */
    public function test_notify_user_added_to_workspace(): void {
        global $OUTPUT;

        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Clear the adhoc tasks.
        $this->executeAdhocTasks();

        // Create user two and add the user to the workspace.
        $user_two = $generator->create_user();
        $member = member::added_to_workspace($workspace, $user_two->id);

        // Start the sinks and execute the adhoc tasks.
        $message_sink = $this->redirectMessages();
        $this->executeAdhocTasks();

        $messages = $message_sink->get_messages();
        $this->assertCount(1, $messages);

        $message = reset($messages);

        $this->assertObjectHasAttribute('subject', $message);
        $this->assertEquals(get_string('member_added_title', 'container_workspace'), $message->subject);

        $this->assertObjectHasAttribute('useridto', $message);
        $this->assertEquals($user_two->id, $message->useridto);

        $this->assertObjectHasAttribute('fullmessage', $message);
        $this->assertObjectHasAttribute('fullmessagehtml', $message);

        $template = added_to_workspace_notification::create($member);
        $rendered_content = $OUTPUT->render($template);

        $this->assertEquals($rendered_content, $message->fullmessagehtml);
        $this->assertEquals(html_to_text($rendered_content), $message->fullmessage);
    }

    public function test_recipients_language_setting_is_observed(): void {
        $generator = self::getDataGenerator();
        $fake_language = 'xo_ox';
        $this->add_fake_language_pack(
            $fake_language,
            [
                'container_workspace' => [
                    'member_added_title' => 'Fake language subject string'
                ]
            ]
        );

        $user_one = $generator->create_user();
        self::setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Clear the adhoc tasks.
        $this->executeAdhocTasks();

        // Create user two and add the user to the workspace.
        $user_two = $generator->create_user(['lang' => $fake_language]);
        member::added_to_workspace($workspace, $user_two->id);

        // Start the sinks and execute the adhoc tasks.
        $message_sink = $this->redirectMessages();
        $this->executeAdhocTasks();

        $messages = $message_sink->get_messages();
        self::assertCount(1, $messages);
        $message = reset($messages);

        self::assertEquals('Fake language subject string', $message->subject);
    }

    /**
     * @return void
     */
    public function test_notify_user_added_to_workspace_does_not_respect_notification_settings(): void {
        global $OUTPUT;

        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Clear the adhoc tasks.
        $this->executeAdhocTasks();

        // Create user two and add the user to the workspace.
        $user_two = $generator->create_user();
        $member = member::added_to_workspace($workspace, $user_two->id);

        // Turn off notification for user two within the workspace.
        $workspace_id = $workspace->get_id();
        workspace_notification::off($workspace_id, $user_two->id);

        // Make sure that workspace notification is off for this user two.
        $this->assertTrue(workspace_notification::is_off($workspace_id, $user_two->id));

        // Start the sinks and execute the adhoc tasks.
        $message_sink = $this->redirectMessages();
        $this->executeAdhocTasks();

        $messages = $message_sink->get_messages();
        $this->assertCount(1, $messages);

        $message = reset($messages);

        $this->assertObjectHasAttribute('subject', $message);
        $this->assertEquals(get_string('member_added_title', 'container_workspace'), $message->subject);

        $this->assertObjectHasAttribute('useridto', $message);
        $this->assertEquals($user_two->id, $message->useridto);

        $this->assertObjectHasAttribute('fullmessage', $message);
        $this->assertObjectHasAttribute('fullmessagehtml', $message);

        $template = added_to_workspace_notification::create($member);
        $rendered_content = $OUTPUT->render($template);

        $this->assertEquals($rendered_content, $message->fullmessagehtml);
        $this->assertEquals(html_to_text($rendered_content), $message->fullmessage);
    }

    /**
     * @return void
     */
    public function test_notify_user_added_to_workspace_and_removed(): void {
        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);

        /** @var \container_workspace\testing\generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace = $workspace_generator->create_workspace();

        // Clear the adhoc tasks.
        $this->executeAdhocTasks();

        // Create user two and add the user to the workspace.
        $user_two = $generator->create_user();
        member::added_to_workspace($workspace, $user_two->id);

        // Delete user two to force error.
        delete_user($user_two);

        // Start the sink and execute the adhoc tasks.
        $message_sink = $this->redirectMessages();
        $this->executeAdhocTasks();

        // Member not found by notify_added_to_workspace_task should fire a debug message.
        $this->assertDebuggingCalled('Skipped sending notification to non-existent member with id ' . $user_two->id);

        // No message should have been sent to user.
        $messages = $message_sink->get_messages();
        $this->assertCount(0, $messages);
    }
}