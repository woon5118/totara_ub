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
 * @author Alastair Munro <alastair.munro@totaralearning.com>
 * @package compatibiliy
 */

/**
 * This class adds PHP 7.2 compatibility functions and can be removed once
 * PHP 7.2 support is dropped
 */

/*
 * Add getallheader which doesn't work if you are using FastCGI (php-fpm)
 *
 * Poyfill function from: https://github.com/ralouphie/getallheaders/blob/develop/src/getallheaders.php
 * Copyright (c) 2014 Ralph Khattar
 * The MIT License (MIT)
 */
if (!function_exists('getallheaders') && php_sapi_name() !== 'cli') {

    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return string[string] The HTTP header key/value pairs.
     */
    function getallheaders() {
        $headers = array();

        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }
}

if (!function_exists('array_key_first')) {
    /**
     * Gets the first key of an array
     *
     * Get the first key of the given array without affecting the internal array pointer.
     *
     * @link https://secure.php.net/array_key_first
     * @param array $array An array
     * @return mixed Returns the first key of array if the array is not empty; NULL otherwise.
     * @since Totara 13.0
     */
    function array_key_first(array $array) {
        foreach ($array as $key => $unused) {
            return $key;
        }
        return null;
    }
}

if (!function_exists('array_key_last')) {
    /**
     * Gets the last key of an array
     *
     * Get the last key of the given array without affecting the internal array pointer.
     *
     * @link https://secure.php.net/array_key_last
     * @param array $array An array
     * @return mixed Returns the last key of array if the array is not empty; NULL otherwise.
     * @since Totara 13.0
     */
    function array_key_last(array $array) {
        $return = null;
        foreach ($array as $key => $unused) {
            $return = $key;
        }
        return $return;
    }
}
