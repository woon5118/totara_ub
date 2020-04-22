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

defined('MOODLE_INTERNAL') || die();

use core\webapi\execution_context;

use mod_perform\entities\activity\activity_type as activity_type_entity;
use mod_perform\models\activity\activity_type as activity_type_model;
use mod_perform\webapi\resolver\query\activity_types;
use mod_perform\webapi\resolver\type\activity_type;

use totara_webapi\graphql;

/**
 * @coversDefaultClass activity_types.
 *
 * @group perform
 */
class core_webapi_query_activity_types_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_find(): void {
        $expected_types = $this->setup_env();

        $context = $this->get_webapi_context();
        $actual_types = activity_types::resolve([], $context)
            ->all();

        $this->assertCount(count($expected_types), $actual_types, 'wrong count');
        $this->assertEqualsCanonicalizing($expected_types, $actual_types, 'wrong types');
    }

    /**
     * @covers ::resolve
     */
    public function test_successful_ajax_call(): void {
        $expected_types = $this->setup_env();

        $context = $this->get_webapi_context();
        $actual_types = $this->exec_graphql($context, []);
        $this->assertCount(count($expected_types), $actual_types, 'wrong count');

        foreach ($actual_types as $type) {
            $type_id = $type['id'] ?? null;
            $this->assertNotNull($type_id, 'no retrieved type id');
            $this->assertArrayHasKey($type_id, $expected_types, 'unknown type');

            $expected = $this->graphql_return($expected_types[$type_id], $context);
            $this->assertEquals($expected, $type, 'wrong graphql return');
        }
    }

    /**
     * Generates test data.
     *
     * @param int $no_of_custom no of custom activity types to create.
     *
     * @return activity_type_model[] the available activity types. Includes the
     *         system (predefined types) as well as the custom ones.
     */
    private function setup_env(int $no_of_custom = 2): array {
        self::setAdminUser();

        for ($i = 0; $i < $no_of_custom; $i++) {
            activity_type_model::create("my custom type #$i");
        }

        return activity_type_entity::repository()
            ->get()
            ->reduce(
                function (array $map, activity_type_entity $entity): array {
                    $map[$entity->id] = activity_type_model::load_by_entity($entity);
                    return $map;
                },
                []
            );
    }

    /**
     * Creates an graphql execution context.
     *
     * @return execution_context the context.
     */
    private function get_webapi_context(): execution_context {
        return execution_context::create('ajax', 'mod_perform_activity_types');
    }

    /**
     * Executes the test query via AJAX.
     *
     * @param execution_context $context graphql execution context.
     * @param array $args ajax arguments if any.
     *
     * @return array|string either the retrieved items or the error string for
     *         failures.
     */
    private function exec_graphql(execution_context $context, array $args=[]) {
        $result = graphql::execute_operation($context, $args)->toArray(true);

        $op = $context->get_operationname();
        $errors = $result['errors'] ?? null;
        if ($errors) {
            $error = $errors[0];
            $msg = $error['debugMessage'] ?? $error['message'];

            return sprintf(
                "invocation of %s://%s failed: %s",
                $context->get_type(),
                $op,
                $msg
            );
        }

        return $result['data'][$op];
    }

    /**
     * Given the input type, returns data the graphql call is supposed to
     * return.
     *
     * @param activity_type_model $type source type.
     * @param execution_context $context graphql execution context.
     *
     * @return array the expected graphql data values.
     */
    private function graphql_return(activity_type_model $type, execution_context $context): array {
        $type_resolve = function (string $field) use ($type, $context) {
            return activity_type::resolve($field, $type, [], $context);
        };

        return [
            'id' => $type_resolve('id'),
            'name' => $type_resolve('name'),
            'display_name' => $type_resolve('display_name')
        ];
    }
}
