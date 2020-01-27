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

use core\webapi\execution_context;
use GraphQL\Error\Debug;
use GraphQL\Executor\ExecutionResult;
use totara_webapi\graphql;
use totara_webapi\request;
use totara_webapi\server;

class totara_webapi_server_test extends advanced_testcase {

    public function test_handle_successful_request() {
        $server = new server(execution_context::create(graphql::TYPE_AJAX, 'totara_webapi_status_nosession'));

        $request_params = [
            'operationName' => 'totara_webapi_status_nosession',
            'variables' => []
        ];
        $request = new request(graphql::TYPE_AJAX, $request_params);

        $result = $server->handle_request($request);
        $this->assertInstanceOf(ExecutionResult::class, $result);

        $result = $result->toArray(Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE);
        $this->assertArrayHasKey('data', $result);
        $data = $result['data'];
        $this->assertArrayHasKey('totara_webapi_status',  $data);
        $data = $data['totara_webapi_status'];
        $this->assertEquals('ok',  $data['status']);
        $this->assertArrayHasKey('status',  $data);
        $this->assertEquals('ok',  $data['status']);
        $this->assertArrayHasKey('timestamp',  $data);
        $this->assertGreaterThan(0,  $data['timestamp']);
    }

    public function test_handle_invalid_request() {
        $server = new server(execution_context::create(graphql::TYPE_AJAX));

        $request_params = [
            'variables' => []
        ];
        $request = new request(graphql::TYPE_AJAX, $request_params);

        $result = $server->handle_request($request);
        $this->assertInstanceOf(ExecutionResult::class, $result);

        $result = $result->toArray(Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE);
        $this->assertArrayHasKey('errors', $result);

        $errors = $result['errors'];
        $this->assertIsArray($errors);
        $this->assertCount(1, $errors);
        $error = $errors[0];
        $this->assertArrayHasKey('debugMessage',  $error);
        $this->assertEquals('Invalid request, expecting at least operationName and variables',  $error['debugMessage']);
        $this->assertArrayHasKey('message',  $error);
        $this->assertEquals('Internal server error',  $error['message']);
    }

    public function test_batched_queries() {
        $server = new server(execution_context::create(graphql::TYPE_AJAX));

        $request_params = [
            [
                'operationName' => 'totara_webapi_status_nosession',
                'variables' => []
            ],
            [
                'operationName' => 'totara_webapi_status_nosession',
                'variables' => []
            ],
            [
                'operationName' => 'totara_webapi_status_nosession',
                'variables' => []
            ],
        ];
        $request = new request(graphql::TYPE_AJAX, $request_params);

        $results = $server->handle_request($request);
        $this->assertIsArray($results);
        $this->assertContainsOnlyInstancesOf(ExecutionResult::class, $results);
        $this->assertCount(3, $results);

        foreach ($results as $result) {
            $result = $result->toArray(Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE);
            $this->assertArrayHasKey('data', $result);
            $data = $result['data'];
            $this->assertArrayHasKey('totara_webapi_status', $data);
            $data = $data['totara_webapi_status'];
            $this->assertEquals('ok',  $data['status']);
            $this->assertArrayHasKey('status',  $data);
            $this->assertEquals('ok',  $data['status']);
            $this->assertArrayHasKey('timestamp',  $data);
            $this->assertGreaterThan(0,  $data['timestamp']);
        }
    }

    public function test_invalid_type() {
        $types = graphql::get_available_types();

        foreach ($types as $type) {
            // This should not throw an exception
            $server = new server(execution_context::create($type));
        }

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid webapi type given');
        $server = new server(execution_context::create('foobar'));
    }

    public function test_debug() {
        // First run without debug
        $server = new server(execution_context::create(graphql::TYPE_AJAX));
        $server->set_debug(false);

        $request_params = [
            'variables' => []
        ];
        $request = new request(graphql::TYPE_AJAX, $request_params);

        $result = $server->handle_request($request);
        $this->assertInstanceOf(ExecutionResult::class, $result);

        // Then run with debug
        $server = new server(execution_context::create(graphql::TYPE_AJAX));
        $server->set_debug(Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE);

        $request_params = [
            'variables' => []
        ];
        $request = new request(graphql::TYPE_AJAX, $request_params);

        $result = $server->handle_request($request);
        $this->assertInstanceOf(ExecutionResult::class, $result);
    }

    public function test_send_response() {
        $server = new server(execution_context::create(graphql::TYPE_AJAX, 'totara_webapi_status_nosession'));

        $request_params = [
            'operationName' => 'totara_webapi_status_nosession',
            'variables' => []
        ];
        $request = new request(graphql::TYPE_AJAX, $request_params);

        $result = $server->handle_request($request);

        $expected_result = "/\\{\"data\"\:\{\"totara_webapi_status\"\:\{\"status\"\:\"ok\"\,\"timestamp\"\:\"[0-9]+\"\}\}\}/";

        $this->expectOutputRegex($expected_result);
        $server->send_response($result, false);
    }

    public function test_execute_introspection_query() {
        $server = new server(execution_context::create(graphql::TYPE_DEV));

        $request_params = [
            'query' => self::get_introspection_query(),
            'variables' => [],
            'operationName' => null
        ];
        $request = new request(graphql::TYPE_DEV, $request_params);

        $result = $server->handle_request($request);
        $this->assertInstanceOf(ExecutionResult::class, $result);
        $this->assertEmpty($result->errors, 'Unexpected errors found in request');
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