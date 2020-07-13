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
 */

use mod_perform\models\activity\notification as notification_model;
use \mod_perform\webapi\resolver\query\notification;
use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @coversDefaultClass notification
 *
 * @group perform
 */
class mod_perform_webapi_resolver_query_notification_testcase extends advanced_testcase {

    private const QUERY = 'mod_perform_notification';

    use webapi_phpunit_helper;

    private function create_test_data(): notification_model {
        self::setAdminUser();

        $data_generator = self::getDataGenerator();
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        $activity = $perform_generator->create_activity_in_container();

        $notification = notification_model::create($activity, 'instance_created');

        return $notification;
    }

    public function test_get_notification(): void {
        $notification = $this->create_test_data();

        $args = ['notification_id' => $notification->id];

        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $this->assertEquals($notification->id, $result['id']);
        $this->assertSame($notification->name, $result['name']);
        $this->assertSame($notification->active, $result['active']);
    }

    public function test_failed_ajax_query(): void {
        $notification = $this->create_test_data();
        $args = ['notification_id' => $notification->id];

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::QUERY, []);
        $this->assert_webapi_operation_failed($result, 'notification_id');

        $result = $this->parsed_graphql_operation(self::QUERY, ['notification_id' => 0]);
        $this->assert_webapi_operation_failed($result, 'notification id');

        $result = $this->parsed_graphql_operation(self::QUERY, ['notification_id' => 1293]);
        $this->assert_webapi_operation_failed($result, "Invalid activity");

        $this->setUser();
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'not logged in');

        self::setGuestUser();
        $result = $this->parsed_graphql_operation(self::QUERY, $args);
        $this->assert_webapi_operation_failed($result, 'Invalid activity');
    }
}