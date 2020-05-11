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

use core\orm\query\exceptions\record_not_found_exception;
use mod_perform\models\activity\activity;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_update_activity_workflow_testcase extends advanced_testcase {

    use webapi_phpunit_helper;

    /**
     * Test mutation throws error when user with wrong permissions calls it.
     *
     * @return void
     */
    public function test_permissions(): void {
        self::setAdminUser();
        $activity = $this->create_activity();
        self::setGuestUser();

        $this->expectException(required_capability_exception::class);
        $this->resolve_graphql_mutation(
            'mod_perform_update_activity_workflow',
            [
                'input' => [
                    'activity_id' => $activity->id,
                    'close_on_completion' => true,
                ]
            ]
        );
    }

    /**
     * Test mutation fails on invalid activity.
     *
     * @return void
     */
    public function test_invalid_activity(): void {
        self::setGuestUser();
        $this->expectException(record_not_found_exception::class);
        $this->resolve_graphql_mutation('mod_perform_update_activity_workflow', [
            'input' => [
                'activity_id' => 0,
                'close_on_completion' => true,
            ],
        ]);
    }

    /**
     * Test right user can change close_on_completion settings.
     *
     * @return void
     */
    public function test_change_close_on_completion(): void {
        self::setAdminUser();
        $activity = $this->create_activity();
        $this->assertEquals(false, $activity->close_on_completion);

        $result = $this->execute_graphql_operation(
            'mod_perform_update_activity_workflow',
            [
                'input' => [
                    'activity_id' => $activity->id,
                    'close_on_completion' => false,
                ]
            ]
        );
        $activity->refresh();
        $this->assertEquals($result->data['mod_perform_update_activity_workflow']['close_on_completion'], $activity->close_on_completion);

        $result = $this->execute_graphql_operation(
            'mod_perform_update_activity_workflow',
            [
                'input' => [
                    'activity_id' => $activity->id,
                    'close_on_completion' => false,
                ]
            ]
        );
        $activity->refresh();
        $this->assertEquals($result->data['mod_perform_update_activity_workflow']['close_on_completion'], $activity->close_on_completion);

        $result = $this->execute_graphql_operation(
            'mod_perform_update_activity_workflow',
            [
                'input' => [
                    'activity_id' => $activity->id,
                    'close_on_completion' => false,
                ]
            ]
        );
        $activity->refresh();
        $this->assertEquals($result->data['mod_perform_update_activity_workflow']['close_on_completion'], $activity->close_on_completion);
    }

    /**
     * Create sample activity.
     *
     * @return activity
     */
    private function create_activity(): activity {
        /** @var mod_perform_generator|component_generator_base $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');

        return $generator->create_activity_in_container();
    }
}
