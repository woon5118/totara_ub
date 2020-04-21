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
use mod_perform\models\activity\activity_type as activity_type_model;
use mod_perform\webapi\resolver\type\activity_type;

/**
 * @coversDefaultClass activity_type.
 *
 * @group perform
 */
class mod_perform_webapi_type_activity_type_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_invalid_input(): void {
        $webapi_context = $this->get_webapi_context();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageRegExp("/activity_type/");
        activity_type::resolve('id', new stdClass(), [], $webapi_context);
    }

    /**
     * @covers ::resolve
     */
    public function test_invalid_field(): void {
        $type = activity_type_model::load_by_name('feedback');
        $webapi_context = $this->get_webapi_context();
        $field = 'unknown';

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageRegExp("/$field/");
        activity_type::resolve($field, $type, [], $webapi_context);
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
        $type = activity_type_model::load_by_name('appraisal');
        $webapi_context = $this->get_webapi_context();

        $testcases = [
            'id' => ['id', null, $type->id],
            'name' => ['name', null, $type->name],
            'display_name' => ['display_name', null, $type->display_name]
        ];

        foreach ($testcases as $id => $testcase) {
            [$field, $format, $expected] = $testcase;
            $args = $format ? ['format' => $format] : [];

            $value = activity_type::resolve($field, $type, $args, $webapi_context);
            $this->assertEquals($expected, $value, "[$id] wrong value");
        }
    }

    /**
     * Creates a graphql execution context.
     *
     * @return execution_context the context.
     */
    private function get_webapi_context(): execution_context {
        return execution_context::create('dev', null);
    }
}