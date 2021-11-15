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
 * @author  Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package engage_survey
 */
defined('MOODLE_INTERNAL') || die();

use core_user\totara_engage\share\recipient\user;
use engage_survey\totara_engage\card\survey_card;
use totara_engage\card\card_loader;
use totara_engage\query\query;

class engage_survey_multi_tenancy_contribution_loader_of_tenant_participant_testcase extends advanced_testcase {
    /***
     * @var stdClass|null
     */
    private $system_user;

    /**
     * @var stdClass|null
     */
    private $tenant_user;

    /**
     * The main actor of this test case.
     * @var stdClass|null
     */
    private $tenant_participant;

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
            'tenantid' => $tenant->id,
        ]);

        $this->system_user = $generator->create_user([
            'firstname' => 'system_user',
            'lastname' => 'system_user',
        ]);

        $this->tenant_participant = $generator->create_user([
            'firstname' => 'tenant_participant',
            'lastname' => 'tenant_participant',
        ]);

        $tenant_generator->set_user_participation($this->tenant_participant->id, [$tenant->id]);
    }

    /**
     * @return void
     */
    protected function tearDown(): void {
        $this->system_user = null;
        $this->tenant_user = null;
        $this->tenant_participant = null;
    }

    /**
     * @return engage_survey_generator
     */
    private function get_survey_generator(): engage_survey_generator {
        $generator = self::getDataGenerator();

        /** @var engage_survey_generator $survey_generator */
        $survey_generator = $generator->get_plugin_generator('engage_survey');
        return $survey_generator;
    }

    /**
     * @return void
     */
    public function test_loading_shared_surveys_by_system_user_without_isolation(): void {
        $survey_generator = $this->get_survey_generator();

        $this->setUser($this->system_user);
        $public_survey = $survey_generator->create_public_survey();

        $survey_generator->share_survey($public_survey, [new user($this->tenant_participant->id)]);

        $query = new query();
        $query->set_component('totara_engage');
        $query->set_area('shared');

        $this->setUser($this->tenant_participant);
        $loader = new card_loader($query);
        $result = $loader->fetch();

        self::assertEquals(1, $result->get_total());
        $cards = $result->get_items();

        self::assertEquals(1, $cards->count());

        /** @var survey_card $first_card */
        $first_card = $cards->first();

        self::assertInstanceOf(survey_card::class, $first_card);
        self::assertEquals($first_card->get_instanceid(), $public_survey->get_id());
    }

    /**
     * @return void
     */
    public function test_loading_shared_surveys_by_system_user_with_isolation(): void {
        $survey_generator = $this->get_survey_generator();

        $this->setUser($this->system_user);
        $public_survey = $survey_generator->create_public_survey();

        $survey_generator->share_survey($public_survey, [new user($this->tenant_participant->id)]);
        $this->setUser($this->tenant_participant);

        set_config('tenantsisolated', 1);

        $query = new query();
        $query->set_component('totara_engage');
        $query->set_area('shared');

        $loader = new card_loader($query);
        $result = $loader->fetch();

        self::assertEquals(1, $result->get_total());
        $cards = $result->get_items();

        self::assertEquals(1, $cards->count());

        /** @var survey_card $first_card */
        $first_card = $cards->first();

        self::assertInstanceOf(survey_card::class, $first_card);
        self::assertEquals($first_card->get_instanceid(), $public_survey->get_id());
    }

    /**
     * @return void
     */
    public function test_loading_shared_surveys_by_tenant_member_without_isolation(): void {
        $survey_generator = $this->get_survey_generator();

        $this->setUser($this->tenant_user);
        $public_survey = $survey_generator->create_public_survey();

        $survey_generator->share_survey($public_survey, [new user($this->tenant_participant->id)]);
        $this->setUser($this->tenant_participant);

        $query = new query();
        $query->set_component('totara_engage');
        $query->set_area('shared');

        $loader = new card_loader($query);
        $result = $loader->fetch();

        self::assertEquals(1, $result->get_total());
        $cards = $result->get_items();

        self::assertEquals(1, $cards->count());

        /** @var survey_card $first_card */
        $first_card = $cards->first();
        self::assertEquals($public_survey->get_id(), $first_card->get_instanceid());
    }

    /**
     * @return void
     */
    public function test_loading_shared_surveys_by_tenant_member_with_isolation(): void {
        $survey_generator = $this->get_survey_generator();

        $this->setUser($this->tenant_user);
        $public_survey = $survey_generator->create_public_survey();

        $survey_generator->share_survey($public_survey, [new user($this->tenant_participant->id)]);
        $this->setUser($this->tenant_participant);

        set_config('tenantsisolated', 1);

        $query = new query();
        $query->set_component('totara_engage');
        $query->set_area('shared');

        $loader = new card_loader($query);
        $result = $loader->fetch();

        self::assertEquals(1, $result->get_total());
        $cards = $result->get_items();

        self::assertEquals(1, $cards->count());

        /** @var survey_card $first_card */
        $first_card = $cards->first();
        self::assertEquals($public_survey->get_id(), $first_card->get_instanceid());
    }
}