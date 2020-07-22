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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use core\format;
use mod_perform\constants;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\notification_recipient as recipient_model;
use mod_perform\models\activity\section_relationship;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass recipient_model.
 *
 * @group perform
 */
class mod_perform_webapi_type_notification_recipient_testcase extends advanced_testcase {

    use webapi_phpunit_helper;

    private const TYPE = 'mod_perform_notification_recipient';

    /**
     * Create an notification for testing.
     *
     * @return array
     */
    private function create_test_data(): array {
        $this->setAdminUser();

        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $subject_id = $perform_generator->get_core_relationship(constants::RELATIONSHIP_SUBJECT)->id;

        $activity = $perform_generator->create_activity_in_container();
        $context = $activity->get_context();

        $section = $perform_generator->create_section($activity);
        $section_relationship = section_relationship::create($section->get_id(), $subject_id, true);

        $notification = notification_model::create($activity, 'instance_created');
        return [$notification, $section_relationship, $context];
    }

    /**
     * @covers ::resolve
     */
    public function test_invalid_input(): void {
        [$notification, $section_relationship, $context] = $this->create_test_data();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageMatches("/notification recipient/");
        $this->resolve_graphql_type(self::TYPE, 'id', new \stdClass());
    }

    /**
     * @covers ::resolve
     */
    public function test_invalid_field(): void {
        $this->markTestIncomplete("Notification recipient not yet implemented");
        [$notification, $section_relationship, $context] = $this->create_test_data();
        $recipient = recipient_model::create($notification, $section_relationship);

        $field = 'unknown';

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageMatches("/$field/");
        $this->resolve_graphql_type(self::TYPE, $field, $recipient, [], $context);
    }

    /**
     * @covers ::run
     */
    public function test_resolve(): void {
        // Note: cannot use dataproviders here because PHPUnit runs these before
        // everything else. Incredibly, if a dataprovider in a random testsuite
        // creates database records or sends messages, etc, those will also be
        // visible to _all_ tests. In other words, with dataproviders, current
        // and yet unborn tests do not start in a clean state!
        $this->markTestIncomplete("Notification recipient not yet implemented");
        [$notification, $section_relationship, $context] = $this->create_test_data();

        $recipient = recipient_model::create($notification, $section_relationship);

        $testcases = [
            'name' => ['name', format::FORMAT_PLAIN, $recipient->name],
            'active' => ['active', null, $recipient->active],
        ];

        foreach ($testcases as $id => $testcase) {
            [$field, $format, $expected] = $testcase;
            $args = $format ? ['format' => $format] : [];

            $value = $this->resolve_graphql_type(self::TYPE, $field, $recipient, $args, $context);
            $this->assertEquals($expected, $value, "[$id] wrong value");
        }
    }
}
