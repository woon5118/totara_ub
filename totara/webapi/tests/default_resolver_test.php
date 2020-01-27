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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_webapi
 */

use core\date_format;
use core\webapi\execution_context;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use totara_webapi\default_resolver;
use totara_webapi\graphql;

class totara_webapi_default_resolver_test extends advanced_testcase {

    public function test_query_resolver() {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = 'Query';

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;
        $resolve_info_mock->fieldName = 'totara_webapi_status';

        $execution_context = execution_context::create(graphql::TYPE_AJAX, 'totara_webapi_status');

        $resolver = new default_resolver();
        $result = $resolver(null, [], $execution_context, $resolve_info_mock);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('timestamp', $result);
    }

    public function test_query_resolver_unknown_query() {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = 'Query';

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;
        $resolve_info_mock->fieldName = 'idonot_exist';

        $execution_context = execution_context::create(graphql::TYPE_AJAX, 'idonot_exist');

        $resolver = new default_resolver();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('GraphQL query name is invalid');
        $resolver(null, [], $execution_context, $resolve_info_mock);
    }

    public function test_query_resolver_resolver_class_missing() {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = 'Query';

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;
        $resolve_info_mock->fieldName = 'totara_webapi_idonotexist';

        $execution_context = execution_context::create(graphql::TYPE_AJAX, 'totara_webapi_idonotexist');

        $resolver = new default_resolver();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('GraphQL query resolver class is missing');
        $resolver(null, [], $execution_context, $resolve_info_mock);
    }

    public function test_mutation_resolver() {
        // TODO: Come up with a mutation we can use for this test
        $this->markTestIncomplete('This cannot be completed now without a mutation we can use.');
    }

    public function test_type_resolver() {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = 'totara_webapi_status';

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;

        $execution_context = execution_context::create(graphql::TYPE_AJAX, 'totara_webapi_status');

        $resolver = new default_resolver();

        $timestamp = time();

        $source = [
            'status' => 'green',
            'timestamp' => $timestamp
        ];

        $args = ['format' => date_format::FORMAT_ISO8601];

        // First let's query the status column
        $resolve_info_mock->fieldName = 'status';

        $result = $resolver($source, $args, $execution_context, $resolve_info_mock);
        $this->assertEquals('green', $result);

        // Then the timestamp
        $resolve_info_mock->fieldName = 'timestamp';

        $date = new DateTime('@' . $timestamp);
        $date->setTimezone(core_date::get_user_timezone_object());
        $expected_timestamp_result = $date->format(DateTime::ISO8601);

        $result = $resolver($source, $args, $execution_context, $resolve_info_mock);
        $this->assertEquals($expected_timestamp_result, $result);
    }

    public function test_type_resolver_with_invalid_name() {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = 'totara_idontexist';

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;
        $resolve_info_mock->fieldName = 'status';

        $source = [
            'status' => 'green',
            'timestamp' => 123
        ];

        $execution_context = execution_context::create(graphql::TYPE_AJAX, 'totara_idontexist');

        $resolver = new default_resolver();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Type resolvers must be named as component_name, e.g. totara_job_job');

        $resolver($source, [], $execution_context, $resolve_info_mock);
    }

    public function test_no_existing_type_resolver_class_hits_graphql_default_resolver() {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = 'totara_webapi_mystatus';

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;

        $execution_context = execution_context::create(graphql::TYPE_AJAX, 'totara_webapi_mystatus');

        $resolver = new default_resolver();

        $source = [
            'status' => 'red',
            'timestamp' => 123
        ];

        $args = ['format' => date_format::FORMAT_ISO8601];

        // First let's query the status column
        $resolve_info_mock->fieldName = 'status';

        $result = $resolver($source, $args, $execution_context, $resolve_info_mock);
        $this->assertEquals('red', $result);

        // Then the timestamp. This time the format is ignored as its not going through the normal type resolver
        $resolve_info_mock->fieldName = 'timestamp';

        $result = $resolver($source, $args, $execution_context, $resolve_info_mock);
        $this->assertEquals(123, $result);
    }

    public function test_introspection_type() {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = '__schema';

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;
        $resolve_info_mock->fieldName = 'types';

        $execution_context = execution_context::create(graphql::TYPE_AJAX, '__schema');

        // We make sure the default resolver does not brake on introspection types
        $resolver = new default_resolver();
        $result = $resolver(null, [], $execution_context, $resolve_info_mock);
        // This is empty as it's usually handled internally
        $this->assertNull($result);
    }

}