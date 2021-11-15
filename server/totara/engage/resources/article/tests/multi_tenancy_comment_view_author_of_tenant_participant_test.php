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
 * @package engage_article
 */
defined('MOODLE_INTERNAL') || die();

use engage_article\totara_engage\resource\article;
use totara_comment\interactor\comment_interactor;

class engage_article_multi_tenancy_comment_view_author_of_tenant_participant_testcase extends advanced_testcase {
    /**
     * @var stdClass|null
     */
    private $tenant_user;

    /**
     * The main actor of this test case
     * @var stdClass|null
     */
    private $tenant_participant;

    /**
     * @var stdClass|null
     */
    private $system_user;

    /**
     * @return void
     */
    protected function setUp(): void {
        $generator = self::getDataGenerator();

        /** @var totara_tenant_generator $tenant_generator */
        $tenant_generator = $generator->get_plugin_generator('totara_tenant');
        $tenant_generator->enable_tenants();

        $tenant = $tenant_generator->create_tenant();

        $this->tenant_user = $generator->create_user([
            'firstname' => 'tenant_user',
            'lastname' => 'tenant_user',
            'tenantid' => $tenant->id
        ]);

        $this->system_user = $generator->create_user([
            'firstname' => 'system_user',
            'lastname' => 'system_user'
        ]);

        $this->tenant_participant = $generator->create_user([
            'firstname' => 'tenant_participant',
            'lastname' => 'tenant_participant'
        ]);

        $tenant_generator->set_user_participation($this->tenant_participant->id, [$tenant->id]);
    }

    /**
     * @return void
     */
    protected function tearDown(): void {
        $this->tenant_user = null;
        $this->tenant_participant = null;
        $this->system_user = null;
    }

    /**
     * @return engage_article_generator
     */
    private function get_article_generator(): engage_article_generator {
        $generator = self::getDataGenerator();

        /** @var engage_article_generator $article_generator */
        $article_generator = $generator->get_plugin_generator('engage_article');
        return $article_generator;
    }

    /**
     * @return totara_comment_generator
     */
    private function get_comment_generator(): totara_comment_generator {
        $generator = self::getDataGenerator();

        /** @var totara_comment_generator $comment_generator */
        $comment_generator = $generator->get_plugin_generator('totara_comment');
        return $comment_generator;
    }

    /**
     * @return void
     */
    public function test_check_can_view_author_of_tenant_member_without_isolation(): void {
        $article_generator = $this->get_article_generator();
        $article = $article_generator->create_public_article(['userid' => $this->tenant_participant->id]);

        // Create a comment as of tenant member.
        $this->setUser($this->tenant_user);
        $comment_generator = $this->get_comment_generator();

        $comment = $comment_generator->create_comment(
            $article->get_id(),
            article::get_resource_type(),
            article::COMMENT_AREA
        );

        $interactor = new comment_interactor($comment, $this->tenant_participant->id);
        self::assertTrue($interactor->can_view_author());
    }

    /**
     * @return void
     */
    public function test_check_can_view_author_of_tenant_member_with_isolation(): void {
        $article_generator = $this->get_article_generator();
        $article = $article_generator->create_public_article([
            'userid' => $this->tenant_participant->id
        ]);

        // Create a comment as of tenant member.
        $this->setUser($this->tenant_user);
        $comment_generator = $this->get_comment_generator();

        $comment = $comment_generator->create_comment(
            $article->get_id(),
            article::get_resource_type(),
            article::COMMENT_AREA
        );

        set_config('tenantsisolated', 1);

        $interactor = new comment_interactor($comment, $this->tenant_participant->id);
        self::assertTrue($interactor->can_view_author());
    }

    /**
     * @return void
     */
    public function test_check_can_view_author_of_system_user_without_isolation(): void {
        $article_generator = $this->get_article_generator();
        $article = $article_generator->create_public_article(['userid' => $this->tenant_participant->id]);

        // Create a comment as of tenant member.
        $this->setUser($this->system_user);
        $comment_generator = $this->get_comment_generator();

        $comment = $comment_generator->create_comment(
            $article->get_id(),
            article::get_resource_type(),
            article::COMMENT_AREA
        );

        $interactor = new comment_interactor($comment, $this->tenant_participant->id);
        self::assertTrue($interactor->can_view_author());
    }

    /**
     * @return void
     */
    public function test_check_can_view_author_of_system_user_with_isolation(): void {
        set_config('tenantsisolated', 1);

        $article_generator = $this->get_article_generator();
        $article = $article_generator->create_public_article(['userid' => $this->tenant_participant->id]);

        // Create a comment as of tenant member.
        $this->setUser($this->system_user);
        $comment_generator = $this->get_comment_generator();

        $comment = $comment_generator->create_comment(
            $article->get_id(),
            article::get_resource_type(),
            article::COMMENT_AREA
        );

        $interactor = new comment_interactor($comment, $this->tenant_participant->id);
        self::assertTrue($interactor->can_view_author());
    }
}