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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

use totara_core\advanced_feature;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_activity_workflow_testcase extends advanced_testcase {
    private const MUTATION = 'mod_perform_update_activity_workflow';

    use webapi_phpunit_helper;

    /**
     * Test mutation throws error when user with wrong permissions calls it.
     *
     * @return void
     */
    public function test_permissions(): void {
        [, $args] = $this->create_activity();

        self::setGuestUser();
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'accessible');
    }

    /**
     * Test mutation fails on invalid activity.
     *
     * @return void
     */
    public function test_invalid_activity(): void {
        [, $args] = $this->create_activity();
        $args['input']['activity_id'] = 0;

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'activity id');
    }

    /**
     * Test right user can change close_on_completion settings.
     *
     * @return void
     */
    public function test_change_close_on_completion(): void {
        [$activity, $args] = $this->create_activity();
        $this->assertEquals(false, $activity->close_on_completion);

        $args['input']['close_on_completion'] = false;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $activity->refresh();
        $this->assertEquals($result['close_on_completion'], $activity->close_on_completion);

        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $activity->refresh();
        $this->assertEquals($result['close_on_completion'], $activity->close_on_completion);

        $args['input']['close_on_completion'] = true;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_successful($result);

        $result = $this->get_webapi_operation_data($result);
        $activity->refresh();
        $this->assertEquals($result['close_on_completion'], $activity->close_on_completion);
    }

    public function test_failed_ajax_query(): void {
        [, $args] = $this->create_activity();

        $feature = 'performance_activities';
        advanced_feature::disable($feature);
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
        advanced_feature::enable($feature);

        $result = $this->parsed_graphql_operation(self::MUTATION, []);
        $this->assert_webapi_operation_failed($result, 'Variable "$input" of required type "update_activity_workflow_input!" was not provided.');

        $activity_id = 999;
        $args['input']['activity_id'] = $activity_id;
        $result = $this->parsed_graphql_operation(self::MUTATION, $args);
        $this->assert_webapi_operation_failed($result, "Invalid activity");
    }

    /**
     * Create sample activity.
     *
     * @param bool $close_on_completion the close on completion value to put into
     *        the returned graphql arguments.
     *
     * @return array an (activity, graphql args) tuple.
     */
    private function create_activity(bool $close_on_completion = true): array {
        self::setAdminUser();

        /** @var mod_perform_generator|component_generator_base $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();

        $args = [
            'input' => [
                'activity_id' => $activity->id,
                'close_on_completion' => $close_on_completion
            ]
        ];

        return [$activity, $args];
    }
}
