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

namespace totara_webapi;

use coding_exception;
use core\webapi\execution_context;
use Exception;
use GraphQL\Error\Debug;
use GraphQL\Error\Error;
use GraphQL\Executor\ExecutionResult;
use GraphQL\Server\OperationParams;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Schema;
use Throwable;
use totara_webapi\local\util;

/**
 * This class handles the request (queries, mutations, etc.) and returns and / or sends the result.
 *
 * @package totara_webapi
 */
class server {

    /**
     * @var bool|int
     */
    protected $debug = false;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var execution_context
     */
    protected $execution_context;

    /**
     * @param execution_context $execution_context
     * @param mixed|null $debug
     */
    public function __construct(execution_context $execution_context, $debug = null) {
        global $CFG;

        if (!in_array($execution_context->get_type(), graphql::get_available_types())) {
            throw new coding_exception('Invalid webapi type given');
        }

        $this->type = $execution_context->get_type();
        $this->execution_context = $execution_context;

        if ($debug !== null) {
            $this->debug = $debug;
        } else if ((bool)$CFG->debugdeveloper) {
            // If debugging is enabled let's set flags to include message & trace
            $this->debug = Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE;
        }
    }

    /**
     * Set debug, check graphql library for available options
     *
     * @param bool|int $debug|false
     * @return $this
     */
    public function set_debug($debug): self {
        $this->debug = $debug;
        return $this;
    }

    /**
     * This can be used to override the execution context
     *
     * @param execution_context $ec
     * @return server
     */
    public function set_execution_context(execution_context $ec): self {
        $this->execution_context = $ec;
        return $this;
    }

    /**
     * Prepares, validates the request and executes it returning the result.
     * If a batched operation got requested it will return an array of results.
     *
     * @param request|null $request if not passed, the request will be taken from the http post data
     * @return ExecutionResult|ExecutionResult[]
     */
    public function handle_request(request $request = null) {
        try {
            if (!$request) {
                $request = new request($this->type);
            }

            $request->validate();

            $operations = $this->prepare_operations($request);

            $schema = $this->prepare_schema($request);

            $server = new StandardServer([
                'persistentQueryLoader' => new persistent_operations_loader(),
                'queryBatching' => true,
                'debug' => $this->debug,
                'schema' => $schema,
                'fieldResolver' => new default_resolver(),
                'rootValue' => graphql::get_server_root($schema),
                'context' => $this->execution_context,
                'errorsHandler' => [util::class, 'graphql_error_handler'],
            ]);
            $result = $server->executeRequest($operations);
        } catch (Throwable $e) {
            $result = new ExecutionResult(null, [$e]);
            $result->setErrorsHandler([util::class, 'graphql_error_handler']);
        }
        
        return $result;
    }

    /**
     * Build and validate the schema (on developer mode)
     *
     * @param request $request
     * @return Schema
     */
    protected function prepare_schema(request $request): Schema {
        $schema_file_loader = new schema_file_loader();
        $schema_builder = new schema_builder($schema_file_loader);
        $schema = $schema_builder->build();

        if ($this->type == graphql::TYPE_DEV) {
            $schema->assertValid();
        }

        return $schema;
    }

    /**
     * Convert the request into OperationParams instances which the GraphQL library
     * needs for executing the request
     *
     * @param request $request
     * @return OperationParams|OperationParams[]
     */
    protected function prepare_operations(request $request) {
        if ($request->is_batched()) {
            // Operation name in the execution context should be null
            // as the execution context is used for all queries
            if ($this->execution_context->get_operationname() !== null) {
                throw new coding_exception('Expected operation name in execution context to be null for batched queries');
            }
            return array_map(function ($operation) {
                return $this->create_operation($operation);
            }, $request->get_params());
        } else {
            $params = $request->get_params();
            // We want to be sure that the operation name in the execution context matches the one in the request
            if ($this->execution_context->get_operationname() !== null
                && $this->execution_context->get_operationname() !== $params['operationName']
            ) {
                throw new coding_exception('Operation name mismatch, request has different value as the execution_context.');
            }
            $this->execution_context->set_operationname($params['operationName']);
            return $this->create_operation($params);
        }
    }

    protected function create_operation(array $params) {
        // To be able to use the persistent query support built into
        // the GraphQL library we use the operation name for the queryId
        if ($this->type !== graphql::TYPE_DEV) {
            $params['queryId'] = $params['operationName'];
        }

        $params['webapi_type'] = $this->type;
        $params = fix_utf8($params);
        return OperationParams::create($params);
    }

    /**
     * Convert the result or the array of results and send the back via the appropriate headers
     *
     * @param ExecutionResult|ExecutionResult[] $result
     * @param bool $stop_execution
     */
    public function send_response($result, bool $stop_execution = true) {
        $status_code = 200;
        if (is_array($result)) {
            $result = array_map(
                function ($execution_result) use (& $errors, & $status_code) {
                    if (!$execution_result instanceof ExecutionResult) {
                        util::send_error('Invalid result', 500);
                    }
                    if (!empty($execution_result->errors)) {
                        $status_code = $this->has_internal_errors($execution_result) ? 500 : 400;
                    }
                    return $execution_result->toArray($this->debug);
                }, $result
            );
        } else {
            if (!empty($result->errors)) {
                $status_code = $this->has_internal_errors($result) ? 500 : 400;
            }
            $result = $result->toArray($this->debug);
        }

        util::send_response($result, $status_code, $stop_execution);
    }

    /**
     * Check for internal errors in the result, everything not explicitly
     * a webapi or graphql exception counts as internal
     *
     * @param ExecutionResult $result
     * @return bool
     */
    private function has_internal_errors(ExecutionResult $result): bool {
        if (empty($result->errors)) {
            return false;
        }
        foreach ($result->errors as $error) {
            // If it's an error not happening in the GraphQL server or as part of the request,
            // i.e. in the resolver we can give it a 500
            if ((!$error instanceof Error || $error->getPrevious()) && !$error instanceof webapi_request_exception) {
                return true;
            }
        }
        return false;
    }

}