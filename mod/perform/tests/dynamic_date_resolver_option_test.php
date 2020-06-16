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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\dates\resolvers\dynamic\resolver_option;
use mod_perform\dates\resolvers\dynamic\user_creation_date;
use mod_perform\models\activity\track;

/**
 * @coversDefaultClass track.
 *
 * @group perform
 */
class dynamic_date_resolver_option_test_testcase extends advanced_testcase {

    /**
     * Simple sanity test.
     */
    public function test_all_available_factory_method(): void {
        $all_options = resolver_option::all_available();

        self::assertGreaterThan(0, $all_options->count());
        self::assertContainsOnlyInstancesOf(resolver_option::class, $all_options);
    }

    public function test_create_from_json_missing_fields(): void {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('resolver_class_name and option_key fields are mandatory');

        resolver_option::create_from_json([]);
    }

    /**
     * Test that options that may have been valid at one point can still be created.
     */
    public function test_create_from_json_unavailable_option(): void {
        $resolver_option = resolver_option::create_from_json([
            'resolver_class_name' => 'fake_resolver_class_name',
            'option_key' => 'fake_option_key',
            'display_name' => 'Display name fed in',
        ]);

        self::assertFalse($resolver_option->is_available());
        self::assertNull($resolver_option->get_resolver_class_name());
        self::assertEquals('Display name fed in', $resolver_option->get_display_name());
        self::assertEquals('fake_option_key', $resolver_option->get_option_key());
    }

    public function test_create_from_json_invalid_option_key(): void {
        /** @var resolver_option $user_creation_date_option */
        $user_creation_date_option = (new user_creation_date())->get_options()->first();

        $resolver_option = resolver_option::create_from_json([
            'resolver_class_name' => $user_creation_date_option->get_resolver_class_name(),
            'option_key' => 'fake_option_key',
            'display_name' => $user_creation_date_option->get_display_name(),
        ]);

        self::assertFalse($resolver_option->is_available());
        self::assertEquals(user_creation_date::class, $resolver_option->get_resolver_class_name());
        self::assertEquals('User creation date', $resolver_option->get_display_name());
        self::assertEquals('fake_option_key', $resolver_option->get_option_key());
    }

    /**
     * @param string | array $data
     * @param bool $must_be_available
     * @dataProvider create_from_json_available_provider
     */
    public function test_create_from_json_valid_and_available($data, bool $must_be_available): void {
        $resolver_option = resolver_option::create_from_json($data, $must_be_available);

        self::assertTrue($resolver_option->is_available());
        self::assertEquals(user_creation_date::class, $resolver_option->get_resolver_class_name());
        self::assertEquals('User creation date', $resolver_option->get_display_name());
        self::assertEquals(user_creation_date::DEFAULT_KEY, $resolver_option->get_option_key());
    }

    public function create_from_json_available_provider(): array {
        /** @var resolver_option $user_creation_date_option */
        $user_creation_date_option = (new user_creation_date())->get_options()->first();

        $data = [
            'resolver_class_name' => $user_creation_date_option->get_resolver_class_name(),
            'option_key' => $user_creation_date_option->get_option_key(),
            'display_name' => 'the display name will be reloaded from source',
        ];

        return [
            'json string and must be available' => [json_encode($data), true],
            'json string and does not need to be available' => [json_encode($data), false],
            'data array and must be available' => [$data, true],
            'data array and does not need to be available' => [$data, false],
        ];
    }

}
