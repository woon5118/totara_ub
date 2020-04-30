<?php
/*
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

/**
 * @group perform
 */

use core\webapi\execution_context;
use mod_perform\state\activity\draft;
use mod_perform\webapi\resolver\query\activity_users_to_assign_count;
use totara_core\relationship\resolvers\subject;

/**
 * @group perform
 * @covers \mod_perform\webapi\resolver\query\activity_users_to_assign_count
 */
class mod_perform_webapi_resolver_query_activity_users_to_assign_count_testcase extends advanced_testcase {

    /**
     * We don't need to thoroughly test permissions as the query simply extends the query activity.
     */
    public function test_query_permissions(): void {
        $this->expectException(moodle_exception::class);
        self::setGuestUser();

        /** @var mod_perform_generator|component_generator_base $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();

        activity_users_to_assign_count::resolve(['activity_id' => $activity->id], $this->get_execution_context());
    }

    public function test_query_successful() {
        self::setAdminUser();

        /** @var mod_perform_generator|component_generator_base $generator */
        $generator = self::getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container(['activity_status' => draft::get_code()]);

        // Must create a section with an element and a relationship in order to allow an activity to be activated
        $section = $generator->create_section($activity);
        $generator->create_section_element(
            $section,
            $generator->create_element()
        );
        $generator->create_section_relationship($section, ['class_name' => subject::class]);

        $track = $generator->create_activity_tracks($activity)->first();
        $user = self::getDataGenerator()->create_user();
        $generator->create_track_assignments_with_existing_groups($track, [], [], [], [$user->id]);

        $result = activity_users_to_assign_count::resolve(['activity_id' => $activity->id], $this->get_execution_context());
        $this->assertEquals(1, $result);
    }

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null): execution_context {
        return execution_context::create($type, $operation);
    }

}
