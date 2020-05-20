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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_webapi
 */

namespace totara_webapi\phpunit;

use core\webapi\execution_context;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use totara_webapi\default_resolver;
use totara_webapi\graphql;

trait webapi_phpunit_helper {
    /**
     * Helper function to create an execution context.
     *
     * @param string $operation_name name of the operation.
     * @param string $type execution mode.
     *
     * @return execution_context
     */
    protected function create_webapi_context(
        string $operation_name,
        string $type = graphql::TYPE_AJAX
    ): execution_context {
        return execution_context::create($type, $operation_name);
    }

    /**
     * Helper function to execute a graphql operation (with the entire graphql
     * stack) and return the operation result as a (result, error) tuple.
     *
     * Note 'error' does NOT refer to domain specific errors which could be
     * returned as part of the result. In this case, the graphql call actually
     * succeeds.
     *
     * @param string $operation_name name of the operation.
     * @param array $variables operation arguments.
     * @param string $type execution mode.
     *
     * @return array a (result, error) tuple. Note even if there is an error, the
     *         result may not be null.
     */
    protected function parsed_graphql_operation(
        string $operation_name,
        array $variables = [],
        string $type = graphql::TYPE_AJAX
    ): array {
        $raw = $this->execute_graphql_operation($operation_name, $variables, $type)
            ->toArray(true);

        $result = $raw['data'][$operation_name] ?? null;

        $errors = $raw['errors'][0] ?? [];
        $error = $errors['debugMessage'] ?? $errors['message'] ?? null;

        return [$result, $error];
    }

    /**
     * Given a result from a parsed_graphql_operation() call, gets the domain
     * specific data returned by the call.
     *
     * @param array $result_from_parsed_graphql_operation the result from the
     *        call.
     *
     * @return mixed the execution result.
     */
    protected function get_webapi_operation_data(array $result_from_parsed_graphql_operation) {
        [$result, ] = $result_from_parsed_graphql_operation;
        return $result;
    }

    /**
     * Given a result from a parsed_graphql_operation() call, validates the call
     * executed "successfully". Throws a PHPUnit assertion error otherwise.
     *
     * Note "successful" here means the graphql framework did not detect any
     * execution errors; there can still be domain specific errors but returned
     * as call return data.
     *
     * @param array $result_from_parsed_graphql_operation the result from the
     *        call.
     */
    protected function assert_webapi_operation_successful(array $result_from_parsed_graphql_operation) {
        [, $error] = $result_from_parsed_graphql_operation;
        $this->assertEmpty($error, "got execution errors: '$error'");
    }

    /**
     * Given a result from a parsed_graphql_operation() call, validates the call
     * executed with errors. Throws a PHPUnit assertion error otherwise.
     *
     * Note "errors" here refers to errors caught by the graphql framework; domain
     * specific errors returned as part of the execution data is not checked.
     *
     * @param array $result_from_parsed_graphql_operation the result from the
     *        call.
     * @param string $expected_error if specified, also checks the text of the
     *        error.
     */
    protected function assert_webapi_operation_failed(
        array $result_from_parsed_graphql_operation,
        ?string $expected_error = null
    ) {
        [, $error] = $result_from_parsed_graphql_operation;
        $this->assertNotEmpty($error, "expected execution errors but got none");

        if ($expected_error) {
            $this->assertStringContainsString($expected_error, $error, 'wrong error');
        }
    }

    /**
     * Helper function to execute a graphql operation which executes it using the whole
     * GraphQL server.
     *
     * @param string $operation_name name of the operation, not to confuse with the query name
     * @param array $variables
     * @param string $type optional, defaults to AJAX
     * @return ExecutionResult
     */
    protected function execute_graphql_operation(
        string $operation_name,
        array $variables,
        string $type = graphql::TYPE_AJAX
    ): ExecutionResult {
        return graphql::execute_operation(
            $this->create_webapi_context($operation_name, $type),
            $variables
        );
    }

    /**
     * This resolves a query using the default resolver to not add too much overhead by using the whole schema.
     *
     * @param string $query_name
     * @param array $variables
     * @return mixed|null
     */
    protected function resolve_graphql_query(string $query_name, array $variables = []) {
        return $this->resolve_query_mutation('Query', $query_name, $variables);
    }

    /**
     * This resolves a mutation using the default resolver to not add too much overhead by using the whole schema
     *
     * @param string $mutation_name
     * @param array $variables
     * @return mixed|null
     */
    protected function resolve_graphql_mutation(string $mutation_name, array $variables = []) {
        return $this->resolve_query_mutation('Mutation', $mutation_name, $variables);
    }

    /**
     * Resolve a query or mutation using the default resolver
     *
     * @param string $type
     * @param string $name
     * @param array $variables
     * @return mixed|null
     */
    private function resolve_query_mutation(string $type, string $name, array $variables = []) {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = $type;

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;
        $resolve_info_mock->fieldName = $name;

        $execution_context = execution_context::create(graphql::TYPE_AJAX, $name);

        $resolver = new default_resolver();
        return $resolver(null, $variables, $execution_context, $resolve_info_mock);
    }

    /**
     * Resolve a type using default resolver to not add too much overhead by using the whole schema
     *
     * @param string $type_name
     * @param string $field_name
     * @param $source
     * @param array $variables
     * @return mixed|null
     */
    protected function resolve_graphql_type(string $type_name, string $field_name, $source, array $variables = []) {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = $type_name;

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;
        $resolve_info_mock->fieldName = $field_name;

        $execution_context = execution_context::create(graphql::TYPE_AJAX, null);

        $resolver = new default_resolver();
        return $resolver($source, $variables, $execution_context, $resolve_info_mock);
    }
}