<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_webapi
 */

use GraphQL\Error\Debug;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Server\Helper;
use GraphQL\Server\StandardServer;
use \totara_webapi\graphql;
use core\webapi\execution_context;
use totara_webapi\local\util;

class totara_webapi_graphql_testcase extends advanced_testcase {
    public function test_get_schema_file_contents() {
        $schema = graphql::get_schema_file_contents();

        $this->assertIsArray($schema);
        foreach ($schema as $i => $content) {
            $this->assertIsInt($i);
            $this->assertIsString($content);
        }
    }

    public function test_get_schema() {
        $schema = graphql::get_schema();
        $this->assertInstanceOf('GraphQL\Type\Schema', $schema);

        $schema->assertValid();
    }

    /**
     * @return array
     */
    public function standard_operation_types() {
        return [['ajax'], ['external'], ['mobile']];
    }

    /**
     * @dataProvider standard_operation_types
     * @param $type
     */
    public function test_get_persisted_operations($type) {
        $operations = graphql::get_persisted_operations($type);
        $this->assertIsArray($operations);
        foreach ($operations as $name => $file) {
            $this->assertRegExp('/^[a-z]+_[a-z0-9_]+$/D', $name);
            $this->assertFileExists($file);
            $this->assertRegExp('/\.graphql$/D', $file);
            // Make sure the operation name matches the file name.
            $filename = basename($file, '.graphql');
            $this->assertStringEndsWith($filename, $name);
            $contents = file_get_contents($file);
            $this->assertRegExp("/(mutation|query)\s+{$name}\s*[\{\(]/", $contents);
        }
    }

    /**
     * @dataProvider standard_operation_types
     * @param $type
     */
    public function test_get_role_capabilities($type) {
        $oeprationcapabilities = graphql::get_role_capabilities($type);
        $this->assertIsArray($oeprationcapabilities);
        foreach ($oeprationcapabilities as $operationname => $capabilities) {
            $this->assertRegExp('/^[a-z]+_[a-z0-9_]+$/D', $operationname);
            $this->assertIsArray($capabilities);
        }
    }

    public function test_get_server_root() {
        $schema = graphql::get_schema();
        $root = graphql::get_server_root($schema);
        $this->assertSame([], $root);
    }

    public function test_execute_operation() {
        // Any operation will do here.
        $result = graphql::execute_operation(
            execution_context::create('ajax', 'core_lang_strings_nosession'),
            [
                'lang' => 'en',
                'ids' => [
                    'edit,core'
                ]
            ]
        );
        $this->assertInstanceOf('GraphQL\Executor\ExecutionResult', $result);
        $result = $result->toArray(true);
        $this->assertSame([
            'data' => [
                'lang_strings' => [
                    [
                        'lang' => 'en',
                        'identifier' => 'edit',
                        'component' => 'core',
                        'string' => 'Edit'
                    ]
                ]
            ]
        ], $result);
    }

    public function test_execute_operation_with_error_result() {
        $result = graphql::execute_operation(
            execution_context::create('ajax', 'core_lang_strings_nosession'),
            [
                'xlang' => 'en',
                'ids' => [
                    'edit,core'
                ]
            ]
        );
        $this->assertInstanceOf('GraphQL\Executor\ExecutionResult', $result);
        $result = $result->toArray();
        $this->assertArrayNotHasKey('data', $result);
        $this->assertArrayHasKey('errors', $result);
    }

    public function test_execute_operation_with_invalid_name() {
        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid Web API operation name');

        graphql::execute_operation(
            execution_context::create('ajax', 'xxxcore_lang_strings_nosession'),
            [
                'lang' => 'en',
                'ids' => [
                    'edit,core'
                ]
            ]
        );
    }

    public function test_execute_introspection_query() {
        $schema = graphql::get_schema();

        $server = new StandardServer([
            'debug' => Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE,
            'schema' => $schema,
            'fieldResolver' => [graphql::class, 'default_resolver'],
            'rootValue' => graphql::get_server_root($schema),
            'context' => execution_context::create('dev', null),
            'errorsHandler' => [util::class, 'graphql_error_handler'],
        ]);

        $helper = new Helper();
        $request = $helper->parseRequestParams('POST', self::get_introspection_query_params(), []);

        $response = $server->executeRequest($request);
        $this->assertInstanceOf(ExecutionResult::class, $response);
        $this->assertEmpty($response->errors, 'Unexpected errors found in request');
    }

    private static function get_introspection_query_params(): array {
        return [
            'query' => self::get_introspection_query(),
            'variables' => [],
            'operationName' => null
        ];
    }

    private static function get_introspection_query(): string {
        // Not getting the types to keep performance impact of this as low as possible.
        // It should still be enough to test that introspection works.
        return '
            query IntrospectionQuery {
                __schema {
                    queryType { name }
                    mutationType { name }
                    subscriptionType { name }
                    directives {
                        name
                        description
                        locations
                        args {
                            ...InputValue
                        }
                    }
                }
            }
        
            fragment InputValue on __InputValue {
                name
                description
                type { ...TypeRef }
                defaultValue
            }
        
            fragment TypeRef on __Type {
                kind
                name
            }
        ';
    }

}
