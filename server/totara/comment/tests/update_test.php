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
 * @package totara_comment
 */
defined('MOODLE_INTERNAL') || die();

use totara_webapi\graphql;
use core\webapi\execution_context;
use totara_comment\comment_helper;
use core\json_editor\node\attachments;
use core\json_editor\node\paragraph;

/**
 * Tests to check if user is able to update comments/replies or not.
 */
class totara_comment_update_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_update_comment_via_graphql(): void {
        global $DB;

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();

        $this->setUser($user);

        /** @var totara_comment_generator $comment_generator */
        $comment_generator = $generator->get_plugin_generator('totara_comment');
        $comment = $comment_generator->create_comment(
            42,
            'dota_windrunner',
            'arrow_shot',
            'A wind of change is blowing.'
        );

        $this->assertTrue(
            $DB->record_exists('totara_comment', ['id' => $comment->get_id()])
        );

        $ec = execution_context::create('ajax', 'totara_comment_update_comment');
        $result = graphql::execute_operation(
            $ec,
            [
                'id' => $comment->get_id(),
                'content' => 'The markswoman of the wood.',
                'format' => FORMAT_PLAIN
            ]
        );

        $this->assertEmpty($result->errors);

        // Check if the comment has actually been updated.
        $record = $DB->get_record('totara_comment', ['id' => $comment->get_id()]);
        $this->assertEquals('The markswoman of the wood.', $record->content);
    }

    /**
     * @return void
     */
    public function test_update_comment_with_file(): void {
        global $CFG;

        $generator = $this->getDataGenerator();
        $user_one = $generator->create_user();

        $this->setUser($user_one);
        // Create a comment with empty content.
        $document = [
            'type' => 'doc',
            'content' => []
        ];

        $comment = comment_helper::create_comment(
            'totara_comment',
            'comment',
            42,
            json_encode($document),
            FORMAT_JSON_EDITOR
        );

        $this->assertEmpty($comment->get_content_text());

        // Now update the content with files.
        require_once("{$CFG->dirroot}/lib/filelib.php");
        $draft_id = file_get_unused_draft_itemid();

        $context_user = context_user::instance($user_one->id);
        $fs = get_file_storage();
        $files = [];

        for ($i = 0; $i < 5; $i++) {
            $file_record = new stdClass();
            $file_record->itemid = $draft_id;
            $file_record->component = 'user';
            $file_record->filearea = 'draft';
            $file_record->filepath = '/';
            $file_record->filename = uniqid() . '.png';
            $file_record->contextid = $context_user->id;

            $files[] = $fs->create_file_from_string($file_record, 'File content ' . $i);
        }

        $document['content'][] = paragraph::create_json_node_from_text("Check out the list of attachments");
        $document['content'][] = attachments::create_raw_node_from_list($files);

        $document_json = json_encode($document);
        $updated_comment = comment_helper::update_content($comment->get_id(), $document_json, $draft_id);

        $this->assertEquals($updated_comment->get_id(), $comment->get_id());

        $content_text = content_to_text($document_json, FORMAT_JSON_EDITOR);
        $content_text = file_rewrite_urls_to_pluginfile($content_text, $draft_id);

        $this->assertEquals($content_text, $updated_comment->get_content_text());
    }
}