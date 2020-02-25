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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_webapi
 */

namespace totara_webapi;

class request {

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $batched = false;

    /**
     * @var array|null
     */
    protected $params;

    public function __construct(string $type, array $params = null) {
        $this->type = $type;
        if ($params === null) {
            $params = $this->parse_http_request();
        }
        $this->params = $params;
    }

    /**
     * @return array|null
     */
    public function get_params(): ?array {
        return $this->params;
    }

    /**
     * Take the POST raw data and decode it as JSON.
     *
     * @return mixed|null
     */
    protected function parse_http_request() {
        $params = file_get_contents('php://input');
        if (!$params) {
            return null;
        }
        $params = json_decode($params, true);
        if (json_last_error() !== JSON_ERROR_NONE or $params === null) {
            return null;
        }

        return $params;
    }

    /**
     * Validate the request, making sure that we have all the mandatory fields in it.
     *
     * @throws webapi_request_exception
     */
    public function validate() {
        if (empty($this->params)) {
            throw new webapi_request_exception('Invalid request, request cannot be empty');
        }

        if (!array_key_exists('operationName', $this->params) && !array_key_exists('query', $this->params)) {
            $this->batched = true;
            $params = $this->params;
        } else {
            $params = [$this->params];
        }

        foreach ($params as $op) {
            if (!empty($op['queryId']) || !empty($op['id']) || !empty($op['documentid'])) {
                throw new webapi_request_exception('Invalid request, we do not support standard persistent queries');
            }

            if ($this->type !== graphql::TYPE_DEV) {
                if (!empty($op['query'])) {
                    throw new webapi_request_exception('Direct GraphQL queries are not supported, only persistent queries.');
                }

                if (empty($op['operationName']) || !isset($op['variables']) || !is_array($op['variables'])) {
                    throw new webapi_request_exception('Invalid request, expecting at least operationName and variables');
                }

                if (substr($op['operationName'], - strlen('_nosession')) !== '_nosession') {
                    if (!defined('NO_MOODLE_COOKIES')) {
                        define('NO_MOODLE_COOKIES', false);
                        if (empty($_SERVER['HTTP_X_TOTARA_SESSKEY'])) {
                            throw new webapi_request_exception('Invalid request, HTTP_X_TOTARA_SESSKEY must be present');
                        }
                    }
                }
            }

            if ($this->type === graphql::TYPE_DEV && empty($op['query'])) {
                throw new webapi_request_exception('Query parameter is missing');
            }

            if (!empty($op['operationName']) && !preg_match('/^[a-z][a-z0-9_]+$/D', $op['operationName'])) {
                throw new webapi_request_exception('Invalid request, validation of operationName failed');
            }
        }

        // TODO does setting all those constants here is the best idea? Not sure if there's a better place
        if (!defined('NO_MOODLE_COOKIES')) {
            // All operations have nosession suffix, this means we don't have to wait for session lock.
            define('NO_MOODLE_COOKIES', true);
        }

        // This is here to make sure there's a session initiated, this should probably be part of the ajax entrypoint
        if ($this->type === graphql::TYPE_AJAX && !NO_MOODLE_COOKIES && !confirm_sesskey($_SERVER['HTTP_X_TOTARA_SESSKEY'])) {
            throw new webapi_request_exception('Invalid sesskey, page reload required');
        }
    }

    /**
     * Is this a batched request?
     *
     * @return bool
     */
    public function is_batched(): bool {
        return $this->batched;
    }

}