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

use core\collection;
use core\data_providers\cohorts;
use core\entities\cohort as cohort_entity;
use core\pagination\cursor;

// Needed for the cohort type enums.
global $CFG;
require_once($CFG->dirroot.'/totara/cohort/lib.php');

/**
 * @coversDefaultClass cohort.
 *
 * @group core_cohort
 */
class core_cohort_data_provider_testcase extends advanced_testcase {
    /**
     * @covers ::fetch_paginated
     */
    public function test_default_params(): void {
        $no_of_cohorts = cohorts::DEFAULT_PAGE_SIZE;
        [$test_cohorts, $sys_context, ] = $this->create_cohorts($no_of_cohorts);

        $cohorts = new cohorts($sys_context);
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts->fetch_paginated();

        $this->assertEquals($no_of_cohorts, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');

        $retrieved = [];
        foreach ($items as $item) {
            $retrieved[] = $item->name;
        }
        $this->assertCount($no_of_cohorts, $retrieved, 'wrong current page count');

        $expected = $test_cohorts->pluck('name');
        $this->assertEqualsCanonicalizing($expected, $retrieved, 'wrong retrievals');
    }

    /**
     * @covers ::fetch_paginated
     */
    public function test_default_params_specific_cat_context(): void {
        [$test_cohorts,, $cat_ctx_1, $cat_ctx_2] = $this->create_cohorts(
            cohorts::DEFAULT_PAGE_SIZE
        );

        $expected = $test_cohorts
            ->filter(
                function (cohort_entity $entity) use ($cat_ctx_2): bool {
                    return $entity->contextid !== $cat_ctx_2->id;
                }
            )
            ->pluck('name');

        $cohorts = new cohorts($cat_ctx_1);
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts->fetch_paginated();

        $this->assertEquals(count($expected), $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');

        $retrieved = [];
        foreach ($items as $item) {
            $retrieved[] = $item->name;
        }
        $this->assertCount(count($expected), $retrieved, 'wrong current page count');
        $this->assertEqualsCanonicalizing($expected, $retrieved, 'wrong retrievals');
    }

    /**
     * @covers ::set_filters
     * @covers ::fetch_paginated
     */
    public function test_filters(): void {
        [$test_cohorts, ] = $this->create_cohorts();

        // Filter by single id value.
        $id = (int)$test_cohorts->first()->id;
        $cohorts = new cohorts();

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_filters(['ids' => $id])
            ->fetch_paginated();

        $this->assertEquals(1, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(1, $items, 'wrong item count');
        $this->assertEquals($id, $items[0]->id, 'wrong item retrieved');

        // Filter by multiple id value.
        $ids = [$id, (int)$test_cohorts->last()->id];

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_filters(['ids' => $ids])
            ->fetch_paginated();

        $this->assertEquals(count($ids), $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(count($ids), $items, 'wrong item count');

        $retrieved_ids = collection::new($items)->pluck('id');
        $this->assertEqualsCanonicalizing($ids, $retrieved_ids, 'wrong items retrieved');

        // Filter by name.
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_filters(['name' => 'cohort'])
            ->fetch_paginated();

        $this->assertEquals(count($test_cohorts), $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(count($test_cohorts), $items, 'wrong item count');

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_filters(['name' => '#00'])
            ->fetch_paginated();

        $this->assertEquals(1, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(1, $items, 'wrong item count');

        // Filter combination.
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_filters([
                'ids' => $id,
                'name' => 'cohort'
            ])
            ->fetch_paginated();

        $this->assertEquals(1, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(1, $items, 'wrong item count');

        // Filter no result combination.
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_filters(['ids' => $id, 'name' => '#01'])
            ->fetch_paginated();

        $this->assertEquals(0, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(0, $items, 'wrong item count');

        // Unknown filter.
        $key = 'unknown';
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageRegExp("/$key/");
        $cohorts
            ->set_filters([$key => '#00'])
            ->fetch_paginated();
    }

    /**
     * @covers ::set_filters
     * @covers ::set_page_size
     * @covers ::fetch_paginated
     */
    public function test_filters_empty_values(): void {
        $no_of_cohorts = 5;
        [$test_cohorts, ] = $this->create_cohorts($no_of_cohorts);

        $cohorts = new cohorts();
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_page_size($no_of_cohorts)
            ->set_filters([
                'ids' => [],    // This is a valid filter to filter everything out.
                'name' => '  ', // This filter is ignored.
                'type' => null  // As is this.
            ])
            ->fetch_paginated();

        $this->assertEquals(0, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount(0, $items, 'wrong item count');

        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_page_size($no_of_cohorts)
            ->set_filters([
                'ids' => null,  // Now this filter will be ignored.
                'name' => null,
                'type' => null
            ])
            ->fetch_paginated();

        $this->assertEquals($no_of_cohorts, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount($no_of_cohorts, $items, 'wrong item count');

        $inactive_cohort_count = $test_cohorts->filter('active', false)->count();
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts
            ->set_page_size($no_of_cohorts)
            ->set_filters([
                'ids' => null,
                'name' => '  ',
                'type' => null,
                'active' => false // Although "empty", it is a valid filter.
            ])
            ->fetch_paginated();

        $this->assertEquals($inactive_cohort_count, $total, 'wrong total count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');
        $this->assertCount($inactive_cohort_count, $items, 'wrong item count');
    }

    /**
     * @covers ::set_order
     * @covers ::set_page_size
     * @covers ::fetch_paginated
     */
    public function test_sorted_pagination(): void {
        $no_of_cohorts = 10;
        [$test_cohorts, ] = $this->create_cohorts($no_of_cohorts);

        $order_direction = 'desc';
        $cohort_ids = $test_cohorts
            ->sort('id', $order_direction)
            ->pluck('id');

        $page_size = $no_of_cohorts - 1;
        $cohorts = (new cohorts())
            ->set_page_size($page_size)
            ->set_order('id', $order_direction);

        // 1st round.
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts->fetch_paginated();

        $this->assertEquals($no_of_cohorts, $total, 'wrong total count');
        $this->assertCount($page_size, $items, 'wrong current page count');
        $this->assertNotEmpty($enc_cursor, 'empty cursor');

        $retrieved = [];
        foreach ($items as $item) {
            $retrieved[] = $item->id;
        }

        // 2nd round.
        $cursor = cursor::decode($enc_cursor);
        [
            "items" => $items,
            "total" => $total,
            "next_cursor" => $enc_cursor
        ] = $cohorts->fetch_paginated($cursor);

        $this->assertEquals($no_of_cohorts, $total, 'wrong total count');
        $this->assertCount(1, $items, 'wrong current page count');
        $this->assertEmpty($enc_cursor, 'non empty cursor');

        foreach ($items as $item) {
            $retrieved[] = $item->id;
        }

        // See if items were retrieved in the correct order.
        $this->assertEquals($cohort_ids, $retrieved, 'retrieved in wrong order');
    }

    /**
     * Generates test cohorts. Note: 1/3 of these will be in the system context
     * 1/3 in one course category and 1/3 in another course category.
     *
     * @param int $count no of cohorts to generate.
     *
     * @return array [collection of generated cohort entities, system context,
     *         category context#1, category context#2] tuple.
     */
    private function create_cohorts(int $count = 10): array {
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $cat_context_1 = context_coursecat::instance($generator->create_category()->id);
        $cat_context_2 = context_coursecat::instance($generator->create_category()->id);

        $system_context = context_system::instance();

        $cohorts = collection::new([]);
        for ($i = 0; $i < $count; $i++) {
            $base = sprintf('Test cohort #%02d', $i);
            $active = $i % 3 === 0 ? false : true;
            $type = $i % 4 === 0 ? cohort::TYPE_DYNAMIC : cohort::TYPE_STATIC;

            $context_id = null;
            switch ($i % 3) {
                case 0:
                    $context_id = $system_context->id;
                    break;

                case 1:
                    $context_id = $cat_context_1->id;
                    break;

                case 2:
                    $context_id = $cat_context_2->id;
                    break;
            }

            $entity = new cohort_entity([
                'active' => $active,
                'component' => '',
                'contextid' => $context_id,
                'description' => "$base description",
                'descriptionformat' => FORMAT_MOODLE,
                'idnumber' => "$base idnumber",
                'name' => $base,
                'cohorttype' => $type,
                'visible' => true
            ]);

            $entity->save();

            $cohorts->append($entity);
        }

        return [$cohorts, $system_context, $cat_context_1, $cat_context_2];
    }
}