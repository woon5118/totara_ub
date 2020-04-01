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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

/**
 * @group perform
 */
use core\webapi\execution_context;
use mod_perform\webapi\resolver\query\section_admin;

class mod_perform_webapi_resolver_query_section_testcase extends advanced_testcase {

    /**
     * Helper to get execution context
     *
     * @param string $type
     * @param string|null $operation
     * @return execution_context
     */
    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return execution_context::create($type, $operation);
    }

    public function test_get_section() {
        $this->setAdminUser();
        $data = $this->get_test_data();

        $section = section_admin::resolve(['section_id' => $data->section1->id], $this->get_execution_context());
        $this->assertSame($section->title, $data->section1->title);
    }

    private function get_test_data() {
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');

        $activity = $perform_generator->create_activity_in_container();
        $section = $perform_generator->create_section($activity, ['title' => 'Top Section']);

        $data = new \stdClass();
        $data->activity1 = $activity;
        $data->section1 = $section;

        return $data;
    }

}