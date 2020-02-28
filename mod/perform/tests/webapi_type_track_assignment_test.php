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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use core\webapi\execution_context;

use mod_perform\models\activity\track_assignment as track_assignment_model;

use mod_perform\webapi\resolver\type\track_assignment;

/**
 * @coversDefaultClass track_assignment.
 *
 * @group perform
 */
class mod_perform_webapi_type_track_assignment_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_invalid_input(): void {
        [, $context] = $this->create_assignment();
        $webapi_context = $this->get_webapi_context($context);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageRegExp("/track assignment model/");
        track_assignment::resolve('id', new \stdClass(), [], $webapi_context);
    }

    /**
     * @covers ::resolve
     */
    public function test_invalid_field(): void {
        [$assignment, $context] = $this->create_assignment();
        $webapi_context = $this->get_webapi_context($context);
        $field = 'unknown';

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageRegExp("/$field/");
        track_assignment::resolve($field, $assignment, [], $webapi_context);
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
        [$source, $context] = $this->create_assignment();
        $webapi_context = $this->get_webapi_context($context);

        $testcases = [
            'track id' => ['track_id', null, $source->track_id],
            'type' => ['type', null, $source->type],
            'group' => ['group', null, $source->group]
        ];

        foreach ($testcases as $id => $testcase) {
            [$field, $format, $expected] = $testcase;
            $args = $format ? ['format' => $format] : [];

            $value = track_assignment::resolve($field, $source, $args, $webapi_context);
            $this->assertEquals($expected, $value, "[$id] wrong value");
        }
    }

    /**
     * Generates a test track assignment.
     *
     * @return array (generated assignment, context) tuple.
     */
    private function create_assignment(): array {
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
        $activity = $generator->create_activity_in_container();
        $context = $activity->get_context();

        $assignment = $generator
            ->create_single_activity_track_and_assignment($activity)
            ->assignments
            ->first();

        return [$assignment, $context];
    }

    /**
     * Creates a graphql execution context.
     *
     * @param \context totara context to pass to the execution context.
     *
     * @return execution_context the context.
     */
    private function get_webapi_context(\context $context): execution_context {
        $ec = execution_context::create('dev', null);
        $ec->set_relevant_context($context);

        return $ec;
    }
}