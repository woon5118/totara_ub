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
 * @package core_cohort
 * @category test
 */

defined('MOODLE_INTERNAL') || die();

use core\collection;
use core\entities\cohort as cohort_entity;
use core\webapi\execution_context;
use core\webapi\resolver\query\cohorts;
use core\webapi\resolver\type\cohort as cohort_type;
use totara_webapi\graphql;

/**
 * @coversDefaultClass cohorts.
 *
 * @group core_cohort
 */
class core_webapi_query_cohorts_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_find_default_params(): void {
        $no_of_cohorts = 2;
        $expected = $this->setup_env($no_of_cohorts)->pluck('name');
        $context = $this->get_webapi_context();

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = cohorts::resolve([], $context);
        $this->assertEquals($no_of_cohorts, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');

        $retrieved = [];
        foreach ($items as $item) {
            $retrieved[] = $item->name;
        }
        $this->assertCount($no_of_cohorts, $retrieved, 'wrong current page count');
        $this->assertEqualsCanonicalizing($expected, $retrieved, 'wrong retrievals');
    }

    /**
     * @covers ::resolve
     */
    public function test_ajax_default_params(): void {
        $no_of_cohorts = 2;
        $cohorts_by_ids = $this->setup_env($no_of_cohorts)->key_by('id');
        $context = $this->get_webapi_context();

        $args = ['query' => null];
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $this->exec_graphql($context, $args);
        $this->assertCount($no_of_cohorts, $items, 'wrong item count');
        $this->assertEquals($no_of_cohorts, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');

        foreach ($items as $item) {
            $item_id = $item['id'] ?? null;
            $this->assertNotNull($item_id, 'no retrieved item id');

            $expected_item = $cohorts_by_ids->item($item_id);
            $this->assertNotNull($expected_item, 'no retrieved item id');

            $raw = $this->graphql_return($expected_item, $context);
            $this->assertEquals($raw, $item, 'wrong graphql return');
        }
    }

    /**
     * @covers ::resolve
     */
    public function test_ajax_sorted_pagination(): void {
        $no_of_cohorts = 10;
        $order_direction = 'DESC';
        $cohort_ids = $this->setup_env($no_of_cohorts)
            ->sort('id', $order_direction)
            ->pluck('id');

        $page_size = $no_of_cohorts - 1;
        $context = $this->get_webapi_context();

        $args = [
            'query' => [
                'filters' => [],
                'order_by' => 'id',
                'order_dir' => $order_direction,
                'result_size' => $page_size,
                'cursor' => null
            ]
        ];

        // 1st round.
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $this->exec_graphql($context, $args);

        $this->assertEquals($no_of_cohorts, $total, 'wrong total count');
        $this->assertCount($page_size, $items, 'wrong current page count');
        $this->assertNotEmpty($enc_cursor, 'empty cursor');

        $retrieved = [];
        foreach ($items as $item) {
            $retrieved[] = $item['id'];
        }

        // 2nd round.
        $args = [
            'query' => [
                'filters' => [],
                'order_by' => 'id',
                'order_dir' => $order_direction,
                'result_size' => $page_size,
                'cursor' => $enc_cursor
            ]
        ];

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $this->exec_graphql($context, $args);

        $this->assertEquals($no_of_cohorts, $total, 'wrong total count');
        $this->assertCount(1, $items, 'wrong current page count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');

        foreach ($items as $item) {
            $retrieved[] = $item['id'];
        }

        // See if items were retrieved in the correct order.
        $this->assertEquals($cohort_ids, $retrieved, 'retrieved in wrong order');
    }

    /**
     * @covers ::resolve
     */
    public function test_ajax_filters(): void {
        $all_cohorts = $this->setup_env();

        // Filter by single value.
        $context = $this->get_webapi_context();
        $args = [
            'query' => [
                'filters' => [
                    'type' => 'DYNAMIC'
                ]
            ]
        ];

        $expected = $all_cohorts->filter('cohorttype', cohort::TYPE_DYNAMIC)
            ->pluck('id');

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $this->exec_graphql($context, $args);
        $this->assertEquals(count($expected), $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(count($expected), $items, 'wrong item count');

        foreach ($items as $item) {
            $this->assertContains($item['id'], $expected, 'wrong item retrieved');
        }

        // Filter combination.
        $cohort = $all_cohorts->last();
        $args = [
            'query' => [
                'filters' => [
                    'name' => 'cohort',
                    'ids' => (int)$cohort->id
                ]
            ]
        ];

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $this->exec_graphql($context, $args);
        $this->assertEquals(1, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(1, $items, 'wrong item count');

        // Filter no result combination.
        $args = [
            'query' => [
                'filters' => [
                    'name' => '#00',
                    'ids' => (int)$cohort->id
                ]
            ]
        ];

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $this->exec_graphql($context, $args);
        $this->assertEquals(0, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(0, $items, 'wrong item count');
    }

    /**
     * @covers ::resolve
    */
    public function test_failed_ajax_query(): void {
        // No input.
        $args = [
            'query' => ['type' => "UNKNOWN"]
        ];

        $context = $this->get_webapi_context();
        $actual = $this->exec_graphql($context, $args);
        $this->assertIsString($actual, 'wrong type');
        $this->assertStringContainsString('type', $actual, 'wrong error');
    }

    /**
     * Generates test data.
     *
     * @param int $count no of cohorts to generate.
     *
     * @return collection a list of cohort_entity objects.
     */
    private function setup_env(int $count = 10): collection {
        $this->setAdminUser();

        $cohorts = [];
        foreach (range(0, $count - 1) as $i) {
            $cohort_type = $i % 3 === 0 ? cohort::TYPE_DYNAMIC : cohort::TYPE_STATIC;
            $base = sprintf('Test cohort #%02d', $i);

            $entity = new cohort_entity([
                'active' => true,
                'component' => '',
                'contextid' => context_system::instance()->id,
                'description' => "$base description",
                'descriptionformat' => FORMAT_MOODLE,
                'idnumber' => "$base idnumber",
                'name' => $base,
                'cohorttype' => $cohort_type,
                'visible' => true
            ]);

            $entity->save();

            $cohorts[] = $entity;
        }

        return collection::new($cohorts);
    }

    /**
     * Given the input cohort, returns the data the graphql call is supposed to
     * return.
     *
     * @param cohort_entity $cohort source datea.
     * @param execution_context $context graphql execution context.
     *
     * @return array the expected graphql data values.
     */
    private function graphql_return(cohort_entity $cohort, execution_context $context): array {
        $resolve = function (string $field) use ($cohort, $context) {
            return cohort_type::resolve($field, $cohort, [], $context);
        };

        return [
            'active' => $resolve('active'),
            'description' => $resolve('description'),
            'id' => $resolve('id'),
            'idnumber' => $resolve('idnumber'),
            'name' => $resolve('name'),
            'type' => $resolve('type')
        ];
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
     * Creates an graphql execution context.
     *
     * @return execution_context the context.
     */
    private function get_webapi_context(): execution_context {
        return execution_context::create('ajax', 'core_cohorts');
    }
}
