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
 * @package totara_core
 */

use totara_core\entities\relationship as relationship_entity;
use totara_core\entities\relationship_resolver;
use totara_core\relationship\relationship;
use totara_core\relationship\resolvers\subject;

require_once(__DIR__ . '/relationship_resolver_test.php');

/**
 * @group totara_core_relationship
 * @covers \totara_core\relationship\relationship
 */
class totara_core_relationship_testcase extends \advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        relationship_entity::repository()->delete();
    }

    public function test_get_name(): void {
        // For now, get_name() simply just gets the name of the first resolver that is associated with the relationship in the DB.
        $relationship_one = relationship::create([test_resolver_one::class]);
        $relationship_two = relationship::create([test_resolver_two::class]);
        $this->assertEquals(test_resolver_one::get_name(), $relationship_one->get_name());
        $this->assertEquals(test_resolver_two::get_name(), $relationship_two->get_name());

        $subject = relationship::create([subject::class]);
        $this->assertEquals('Subject', $subject->get_name());
    }

    public function test_get_name_plural(): void {
        // For now, get_name() simply just gets the name of the first resolver that is associated with the relationship in the DB.
        $relationship_one = relationship::create([test_resolver_one::class]);
        $relationship_two = relationship::create([test_resolver_two::class]);
        $this->assertEquals(test_resolver_one::get_name_plural(), $relationship_one->get_name_plural());
        $this->assertEquals(test_resolver_two::get_name_plural(), $relationship_two->get_name_plural());

        $subject = relationship::create([subject::class]);
        $this->assertEquals('Subjects', $subject->get_name_plural());
    }

    public function test_create(): void {
        $this->assertEquals(0, relationship_entity::repository()->count());
        $this->assertEquals(0, relationship_resolver::repository()->count());

        $relationship_single = relationship::create([test_resolver_one::class]);

        $this->assertEquals(1, relationship_entity::repository()->count());
        $this->assertEquals(1, relationship_resolver::repository()->count());

        /** @var relationship_entity $relationship_entity */
        $relationship_entity = relationship_entity::repository()->one();

        $this->assertEquals($relationship_single->id, $relationship_entity->id);

        /** @var relationship_resolver $relationship_resolver */
        $relationship_resolver = relationship_resolver::repository()->one();

        $this->assertEquals(test_resolver_one::class, $relationship_resolver->class_name);
        $this->assertEquals($relationship_single->id, $relationship_resolver->relationship_id);

        $relationship_single->delete();

        $relationship_multiple = relationship::create([test_resolver_one::class, test_resolver_three::class]);

        $this->assertEquals(1, relationship_entity::repository()->count());
        $this->assertEquals(2, relationship_resolver::repository()->count());

        /** @var relationship_resolver[] $relationship_resolvers */
        $relationship_resolvers = relationship_resolver::repository()->get()->all();

        $this->assertEquals(test_resolver_one::class, $relationship_resolvers[0]->class_name);
        $this->assertEquals(test_resolver_three::class, $relationship_resolvers[1]->class_name);
        $this->assertEquals($relationship_multiple->id, $relationship_resolvers[0]->relationship_id);
        $this->assertEquals($relationship_multiple->id, $relationship_resolvers[0]->relationship_id);
    }

    public function test_create_without_specifying_any_resolvers(): void {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Must specify at least one relationship resolver!');

        relationship::create([]);
    }

    public function test_create_using_invalid_resolver_class(): void {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage(
            coding_exception::class . ' must be an instance of ' . totara_core\relationship\relationship_resolver::class
        );

        // coding_exception is a class, but it isn't a sub class of relationship resolver
        relationship::create([test_resolver_one::class, coding_exception::class]);
    }

    public function test_create_using_incompatible_resolvers(): void {
        // Will work because they share the same accepted inputs
        relationship::create([test_resolver_one::class, test_resolver_three::class]);
        relationship::create([test_resolver_two::class, test_resolver_five::class]);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage(
            'The specified resolvers do not share at least one common input and are therefore incompatible.'
        );

        // These two resolvers do not share the same accepted inputs
        relationship::create([test_resolver_one::class, test_resolver_two::class]);
    }

    public function test_delete(): void {
        $this->assertEquals(0, relationship_entity::repository()->count());
        $this->assertEquals(0, relationship_resolver::repository()->count());

        $relationship1 = relationship::create([test_resolver_one::class, test_resolver_three::class]);

        $this->assertEquals(1, relationship_entity::repository()->count());
        $this->assertEquals(2, relationship_resolver::repository()->count());

        $relationship2 = relationship::create([test_resolver_one::class, test_resolver_three::class]);

        $relationship1->delete();

        $this->assertEquals(1, relationship_entity::repository()->count());
        $this->assertEquals(2, relationship_resolver::repository()->count());

        $this->assertFalse(relationship_entity::repository()->where('id', $relationship1->id)->exists());
        $this->assertTrue(relationship_entity::repository()->where('id', $relationship2->id)->exists());
    }

    public function test_is_acceptable_input(): void {
        // test_resolver_two & test_resolver_five only have the 'input_field_two' field in common.
        $relationship1 = relationship::create([test_resolver_two::class]);
        $this->assertFalse($relationship1->is_acceptable_input(['input_field_one']));
        $this->assertTrue($relationship1->is_acceptable_input(['input_field_two']));
        $this->assertFalse($relationship1->is_acceptable_input(['input_field_three', 'input_field_one']));

        // test_resolver_one & test_resolver_three only have the 'input_field_one' field in common.
        $relationship2 = relationship::create([test_resolver_one::class, test_resolver_three::class]);
        $this->assertTrue($relationship2->is_acceptable_input(['input_field_one']));
        $this->assertFalse($relationship2->is_acceptable_input(['input_field_two']));
        $this->assertTrue($relationship2->is_acceptable_input(['input_field_three', 'input_field_one']));

        // test_resolver_five accepts either ['input_field_one', 'input_field_three'] OR ['input_field_two']
        $relationship3 = relationship::create([test_resolver_five::class]);
        $this->assertFalse($relationship3->is_acceptable_input(['input_field_one']));
        $this->assertTrue($relationship3->is_acceptable_input(['input_field_two']));
        $this->assertFalse($relationship3->is_acceptable_input(['input_field_three']));
        $this->assertTrue($relationship3->is_acceptable_input(['input_field_one', 'input_field_three']));
        $this->assertFalse($relationship3->is_acceptable_input(['input_field_four', 'input_field_three']));
        $this->assertTrue($relationship3->is_acceptable_input(['input_field_two', 'input_field_three']));

        // There isn't any point in specifying an empty set of fields.
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Must specify at least one field to relationship::is_acceptable_input()');
        $relationship1->is_acceptable_input([]);
    }

    /**
     * Sanity check to make sure it collects unique user ids.
     */
    public function test_get_users(): void {
        $relationship = relationship::create([test_resolver_two::class, test_resolver_five::class]);

        $dummy_id = 5;
        $input = [
            'input_field_two' => $dummy_id,
        ];

        $returned_users = $relationship->get_users($input);

        $this->assertEquals([$dummy_id], $returned_users);
    }

}
