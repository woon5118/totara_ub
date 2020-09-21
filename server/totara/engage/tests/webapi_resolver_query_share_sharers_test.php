<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_engage
 */

use core_user\totara_engage\share\recipient\user as user_recipient;
use totara_engage\access\access;
use totara_engage\access\access_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests the share_sharers query within totara_engage.
 */
class totara_engage_webapi_resolver_query_share_sharers_testcase extends advanced_testcase {

    use \totara_webapi\phpunit\webapi_phpunit_helper;

    private function execute_query(array $args) {
        return $this->resolve_graphql_query('totara_engage_share_sharers', $args);
    }

    private function setup_user() {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        return $user;
    }

    private function create_article($name, $userid, $content = null): \engage_article\totara_engage\resource\article {
        /** @var engage_article_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('engage_article');
        $params = [
            'name' => $name,
            'userid' => $userid,
        ];
        if ($content !== null) {
            $params['content'] = $content;
        }
        return $generator->create_article($params);
    }

    private function create_share(\totara_engage\share\shareable $item, int $fromuserid, array $recipients, $ownerid = null) {
        /** @var totara_engage_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_engage');
        return $generator->share_item($item, $fromuserid, $recipients, $ownerid);
    }

    public function test_happy_path_no_shares() {
        $user = $this->setup_user();
        $article = $this->create_article('test', $user->id);
        $result = $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_owner_share() {
        $user1 = $this->setup_user();
        $user2 = $this->getDataGenerator()->create_user();
        $article = $this->create_article('test', $user1->id);
        $recipient = new user_recipient($user2->id);
        $this->create_share(
            $article,
            $user1->id,
            [$recipient]
        );
        $result = $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_happy_path_one_sharer() {
        $user1 = $this->setup_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $article = $this->create_article('test', $user1->id);
        $recipient = new user_recipient($user3->id);
        $this->create_share(
            $article,
            $user2->id,
            [$recipient]
        );
        $result = $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals($user2->id, $result[0]->id);
    }

    public function test_happy_path_two_sharers() {
        $user1 = $this->setup_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $article = $this->create_article('test', $user1->id);
        $this->create_share(
            $article,
            $user2->id,
            [
                new user_recipient($user3->id),
            ]
        );
        $this->create_share(
            $article,
            $user3->id,
            [
                new user_recipient($user4->id),
            ]
        );
        $result = $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals(2, sizeof($result));
        $this->assertEquals($user2->id, $result[0]->id);
        $this->assertEquals($user3->id, $result[1]->id);
    }

    public function test_itemid_is_required() {
        $this->setup_user();
        self::expectException(coding_exception::class);
        self::expectExceptionMessage('ItemID is a required field.');
        $this->execute_query(['component' => 'engage_article']);
    }

    public function test_component_is_required() {
        $this->setup_user();
        self::expectException(coding_exception::class);
        self::expectExceptionMessage('Component is a required field.');
        $this->execute_query(['itemid' => 1]);
    }

    public function test_no_arguments() {
        $this->setup_user();
        self::expectException(coding_exception::class);
        $this->execute_query([]);
    }

    public function test_invalid_component_not_accepted() {
        $this->setup_user();
        $course = $this->getDataGenerator()->create_course();
        self::expectException(coding_exception::class);
        $this->execute_query(['component' => 'core_course', 'itemid' => $course->id]);
    }

    public function test_invalid_itemid_not_accepted() {
        $this->setup_user();
        self::expectException(moodle_exception::class);
        self::expectExceptionMessage('Permission denied');
        $this->execute_query(['component' => 'engage_article', 'itemid' => -1]);
    }

    public function test_non_existent_itemid_not_accepted() {
        $this->setup_user();
        self::expectException(moodle_exception::class);
        self::expectExceptionMessage('Permission denied');
        $this->execute_query(['component' => 'engage_article', 'itemid' => 42]);
    }

    public function test_query_checks_access() {
        $user1 = $this->setup_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $article = $this->create_article('test', $user1->id);
        $article->update(['access' => access::RESTRICTED]);

        self::assertFalse(user_recipient::is_user_permitted($article, $user1->id));
        self::assertFalse(user_recipient::is_user_permitted($article, $user2->id));
        self::assertFalse(user_recipient::is_user_permitted($article, $user3->id));
        self::assertTrue(access_manager::can_access($article, $user1->id));
        self::assertFalse(access_manager::can_access($article, $user2->id));
        self::assertFalse(access_manager::can_access($article, $user3->id));

        // Owner needs to share with another person but owner does not
        // count towards sharers.
        $recipient = new user_recipient($user2->id);
        $this->create_share(
            $article,
            $user1->id,
            [$recipient],
            $user1->id
        );

        // The recipient shares it with another user.
        $recipient = new user_recipient($user3->id);
        $this->create_share(
            $article,
            $user2->id,
            [$recipient],
            $user1->id
        );

        self::assertFalse(user_recipient::is_user_permitted($article, $user1->id));
        self::assertTrue(user_recipient::is_user_permitted($article, $user2->id));
        self::assertTrue(user_recipient::is_user_permitted($article, $user3->id));
        self::assertTrue(access_manager::can_access($article, $user1->id));
        self::assertTrue(access_manager::can_access($article, $user2->id));
        self::assertTrue(access_manager::can_access($article, $user3->id));

        // I am currently user 1, and own the article, I can see the sharers
        $result = $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals($user2->id, $result[0]->id);

        // I am now user 2, the article was shared with me, so I can see its sharers
        $this->setUser($user2);
        $result = $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals($user2->id, $result[0]->id);

        // I am now user 4, I have no access to the article, I cannot access the sharers.
        $this->setUser($user4);
        try {
            $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
            $this->fail('Exception expected.');
        } catch (moodle_exception $ex) {
            self::assertInstanceOf(moodle_exception::class, $ex);
            self::assertStringContainsString('Permission denied', $ex->getMessage());
        }

        // I am now the guest user, I have no access o the article, I cannot access the sharers.
        $this->setGuestUser();
        try {
            $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
            $this->fail('Exception expected.');
        } catch (moodle_exception $ex) {
            self::assertInstanceOf(moodle_exception::class, $ex);
            self::assertStringContainsString('Permission denied', $ex->getMessage());
        }

        // Finally, I am the admin user, a diety of the TXP world. I can see all sharers.
        $this->setAdminUser();
        $result = $this->execute_query(['component' => 'engage_article', 'itemid' => $article->get_id()]);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals(1, sizeof($result));
        $this->assertEquals($user2->id, $result[0]->id);
    }

}