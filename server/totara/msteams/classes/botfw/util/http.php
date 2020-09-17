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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package totara_msteams
 */

namespace totara_msteams\botfw\util;

/**
 * A helper class for http.
 */
final class http {
    /** 200 OK */
    const OK = 200;
    /** 400 Bad Request */
    const BAD_REQUEST = 400;
    /** 401 Unauthorized */
    const UNAUTHORIZED = 401;
    /** 403 Forbidden */
    const FORBIDDEN = 403;
    /** 404 Not Found */
    const NOT_FOUND = 404;
    /** 500 Internal Server Error */
    const INTERNAL_SERVER_ERROR = 500;
    /** 503 Service Unavailable */
    const SERVICE_UNAVAILABLE = 503;

    /** @var string[] */
    static private $mapping = [
        'Host' => 'HTTP_HOST',
        'User-Agent' => 'HTTP_USER_AGENT',
        'Authorization' => 'HTTP_AUTHORIZATION',
        'Content-Type' => 'CONTENT_TYPE',
        'Content-Length' => 'CONTENT_LENGTH',
    ];

    /**
     * Get the HTTP request headers.
     *
     * The function uses getallheaders() if available.
     * Otherwise the polyfill fills only the following headers.
     * - Host
     * - User-Agent
     * - Authorization
     * - Content-Type
     * - Content-Length
     *
     * @return array|false
     */
    public static function get_request_headers() {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        return self::getallheaders_downlevel();
    }

    /**
     * Simulate getallheaders().
     *
     * @return array|false
     */
    private static function getallheaders_downlevel() {
        $headers = [];
        foreach (static::$mapping as $header => $index) {
            if (isset($_SERVER[$index])) {
                $headers[$header] = $_SERVER[$index];
            }
        }
        if (empty($headers)) {
            return false;
        }
        return $headers;
    }

    /**
     * Send HTTP status code to inform an error.
     *
     * @param int $code HTTP status code
     * @return true
     */
    public static function send_error(int $code): bool {
        header('Content-type: application/json', true, $code);
        return true;
    }
}
