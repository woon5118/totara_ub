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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package engage_survey
 */
defined('MOODLE_INTERNAL') || die();

use totara_engage\resource\resource_completion;
use totara_userdata\userdata\target_user;

class engage_survey_userdata_survey_completion_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_purge_survey_completion(): void {
        global $DB;

        $gen = $this->getDataGenerator();
        $user_one = $gen->create_user();
        $user_two = $gen->create_user();

        $this->setUser($user_one);

        /** @var engage_survey_generator $surveygen */
        $surveygen = $gen->get_plugin_generator('engage_survey');
        $survey1 = $surveygen->create_survey();
        $survey2 = $surveygen->create_survey();

        $this->setUser($user_two);
        $survey3 = $surveygen->create_survey();

        $resource_completion1 = resource_completion::instance($survey1->get_id(), $survey1->get_userid());
        $resource_completion1->create($user_two->id);
        $resource_completion2 = resource_completion::instance($survey2->get_id(), $survey2->get_userid());
        $resource_completion2->create($user_two->id);
        $resource_completion3 = resource_completion::instance($survey3->get_id(), $survey3->get_userid());
        $resource_completion3->create($user_one->id);

        $this->assertTrue(
            $DB->record_exists('engage_resource_completion', ['userid' => $user_one->id])
        );

        $this->assertTrue(
            $DB->record_exists('engage_resource_completion', ['userid' => $user_two->id])
        );

        $user_one->deleted = 1;
        $DB->update_record('user', $user_one);

        $target_user = new target_user($user_one);
        $context = context_system::instance();

        $result = \engage_survey\userdata\survey_completion::execute_purge($target_user, $context);
        $this->assertEquals(\engage_survey\userdata\survey_completion::RESULT_STATUS_SUCCESS, $result);


        $this->assertFalse(
            $DB->record_exists('engage_resource_completion', ['userid' => $user_one->id])
        );
        $this->assertTrue(
            $DB->record_exists('engage_resource_completion', ['userid' => $user_two->id])
        );
    }

    /**
     * @return void
     */
    public function test_export_survey_completion(): void {
        $gen = $this->getDataGenerator();
        $user_one = $gen->create_user();
        $user_two = $gen->create_user();

        $this->setUser($user_one);

        /** @var engage_survey_generator $surveygen */
        $surveygen = $gen->get_plugin_generator('engage_survey');
        $survey1 = $surveygen->create_survey();
        $survey2 = $surveygen->create_survey();

        $resource_completion1 = resource_completion::instance($survey1->get_id(), $survey1->get_userid());
        $resource_completion1->create($user_two->id);
        $resource_completion2 = resource_completion::instance($survey2->get_id(), $survey2->get_userid());
        $resource_completion2->create($user_two->id);

        $target_user = new target_user($user_two);
        $context = context_system::instance();

        $export = \engage_survey\userdata\survey_completion::execute_export($target_user, $context);

        $this->assertNotEmpty($export->data);
        $this->assertCount(2, $export->data);

        foreach ($export->data as $record) {
            $this->assertIsArray($record);
            $this->assertArrayHasKey('name', $record);
            $this->assertArrayHasKey('options', $record);
        }
    }
}