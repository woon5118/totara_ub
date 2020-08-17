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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package totara_reportedcontent
 */
defined('MOODLE_INTERNAL') || die();

use core\webapi\execution_context;
use totara_comment\comment;
use totara_reportedcontent\entity\review as review_entity;
use totara_reportedcontent\hook\get_review_context;
use totara_webapi\graphql;
use totara_core\hook\manager;

class totara_reportedcontent_create_review_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_create_review(): void {
        global $DB, $CFG;

        require_once("{$CFG->dirroot}/totara/reportedcontent/tests/fixtures/review_content_watcher.php");

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $target_user = $this->getDataGenerator()->create_user();

        // Add a fake comment
        $comment = comment::create(
            22,
            'Some fake content',
            'comment',
            'test_component',
            FORMAT_PLAIN,
            $target_user->id
        );

        $create_variables = [
            'component' => 'test_component',
            'area' => 'comment',
            'item_id' => $comment->get_id(),
            'url' => 'https://example.com'
        ];

        // Use a fake watcher instead
        $watchers = [
            [
                'hookname' => get_review_context::class,
                'callback' => [review_content_watcher::class, 'get_content']
            ]
        ];
        manager::phpunit_replace_watchers($watchers);

        $ec = execution_context::create('ajax', 'totara_reportedcontent_create_review');
        $result = graphql::execute_operation($ec, $create_variables);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        // Make sure we return what's expected
        $this->assertArrayHasKey('review', $result->data);

        $review = $result->data['review'];

        $this->assertArrayHasKey('id', $review);
        $this->assertArrayHasKey('success', $review);

        $this->assertNotEmpty($review['id']);
        $this->assertTrue($review['success']);

        // Now check the stored review is what we expect
        $id = $review['id'];

        $this->assertTrue($DB->record_exists(review_entity::TABLE, ['id' => $id]));
        $record = $DB->get_record(review_entity::TABLE, ['id' => $id]);

        $this->assertSame('test_component', $record->component);
        $this->assertSame('comment', $record->area);
        $this->assertSame($comment->get_id(), (int) $record->item_id);
        $this->assertSame('https://example.com', $record->url);
        $this->assertSame($user->id, $record->complainer_id);
        $this->assertSame($target_user->id, $record->target_user_id);

        // Now do it again! We want to check that a second report will returna  false / the ID
        $ec = execution_context::create('ajax', 'totara_reportedcontent_create_review');
        $result = graphql::execute_operation($ec, $create_variables);

        $this->assertEmpty($result->errors);
        $this->assertNotEmpty($result->data);

        $review = $result->data['review'];

        $this->assertArrayHasKey('id', $review);
        $this->assertArrayHasKey('success', $review);

        $this->assertNotEmpty($review['id']);
        $this->assertEquals($id, $review['id']);
        $this->assertFalse($review['success']);
    }
}