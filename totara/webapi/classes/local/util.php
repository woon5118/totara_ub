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

namespace totara_webapi\local;

/**
 * Class util
 *
 * NOTE: This is not a public API - do not use in plugins or 3rd party code!
 */
final class util {
    /**
     * Send response and stop execution.
     *
     * @param array $response
     * @param int $statuscode optional status code for transfer protocol related errors
     * @return void - does not return
     */
    public static function send_response(array $response, $statuscode = null) {
        if (!$statuscode) {
            $statuscode = 200;
        }
        header('Content-type: application/json; charset=utf-8', true, $statuscode);
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Accept-Ranges: none');
        if (!empty($response['errors']) and !isset($response['error'])) {
            // BC for Moodle ajaxexception
            $errors = [];
            foreach ($response['errors'] as $e) {
                $errors[] = $e['message'];
            }
            $response['error'] = implode("\n", $errors);
        }
        echo json_encode($response, JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        die;
    }

    /**
     * Send general error message without any logging.
     *
     * @param string $message
     * @param int $statuscode optional status code for transfer protocol related errors
     */
    public static function send_error(string $message, $statuscode = null) {
        self::send_response(['errors' => [['message' => $message]]], $statuscode);
    }

    /**
     * Default error handler for Web API ajax.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return bool false means use default error handler
     */
    public static function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
        if ($errno == 4096) {
            // Fatal catchable error.
            throw new \coding_exception('PHP catchable fatal error', $errstr);
        }
        return false;
    }

    /**
     * Default exception handler for Web API ajax.
     *
     * @param \Throwable $ex
     * @return void - does not return. Terminates execution!
     */
    public static function exception_handler($ex) {
        global $CFG, $PAGE;

        // Detect active db transactions, rollback and log as error.

        abort_all_db_transactions();

        $PAGE->set_context(null);

        self::log_exception($ex);

        // TODO: decide how to mark exceptions as "client aware"

        $response = [
            'errors' => \GraphQL\Error\FormattedError::createFromException($ex, (bool)$CFG->debugdeveloper),
        ];

        self::send_response($response, 500);
    }

    /**
     * Log exceptions during ajax execution.
     * @param \Throwable $ex
     */
    public static function log_exception($ex) {
        $message = $ex->getMessage();
        $info = get_exception_info($ex);
        error_log("AJAX API error: $message Debug: " . $info->debuginfo
            . "\n" . format_backtrace($info->backtrace, true));

    }

    /**
     * Error handler for graphql-php error logging.
     *
     * @param array $errors
     * @param callable $formatter
     * @return array
     */
    public static function graphql_error_handler(array $errors, callable $formatter) {
        foreach ($errors as $error) {
            /** @var \Throwable $error */
            $prev = $error->getPrevious();
            if (!$prev) {
                continue;
            }
            self::log_exception($prev);
        }
        return array_map($formatter, $errors);
    }
}