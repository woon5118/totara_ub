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
            execution_context::create($type, $operation_name),
            $variables
        );
    }

    /**
     * This resolves a query using the default resolver to not add too much overhead by using the whole schema.
     *
     * This also does apply any middleware defined in the resolvers
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
    protected function resolve_grapqhl_mutation(string $mutation_name, array $variables = []) {
        return $this->resolve_query_mutation('Mutation', $mutation_name, $variables);
    }

    /**
     * Resolve a query or mutation using the default resolver
     *
     * @param string $type
     * @param string $nanme
     * @param array $variables
     * @return mixed|null
     */
    private function resolve_query_mutation(string $type, string $nanme, array $variables = []) {
        $object_type_mock = $this->getMockBuilder(ObjectType::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object_type_mock->name = $type;

        $resolve_info_mock = $this->getMockBuilder(ResolveInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resolve_info_mock->parentType = $object_type_mock;
        $resolve_info_mock->fieldName = $nanme;

        $execution_context = execution_context::create(graphql::TYPE_AJAX, $nanme);

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