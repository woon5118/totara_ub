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

/*
 * This file is not intended to be used directly from Javascript code,
 * all requests must be done via public API in core/webapi AMD module.
 *
 * This endpoint is not a public API, the parameters or data structure
 * may change even in stable branches.
 *
 * The batching support is intended only for fast, non-recursive, read-only
 * queries. Batching is not suitable for mutations because execution is not
 * interrupted by errors and order of execution may not be guaranteed in future.
 */

use totara_webapi\graphql;
use totara_webapi\local\util;
use core\webapi\execution_context;

// ==== START OF NASTY HACK ==================================================
//
// Find out if we should start session, we do this to allow
// concurrent requests for resources like strings, templates and flex icons.

ini_set('display_errors', '0');
ini_set('log_errors', '1');

if (defined('NO_MOODLE_COOKIES')) {
    // This should not happen, dev must be trying to include this page from elsewhere.
    die;
}

require(__DIR__ . '/classes/local/util.php');

if (!file_exists(__DIR__ . '/../../config.php')) {
    util::send_error('AJAX cannot be used from Totara web installer', 500);
}

if (empty($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'POST') {
    util::send_error('Invalid Ajax request, use core/webapi AMD module for ajax requests', 400);
}

$ajaxrequestraw = file_get_contents('php://input');
if (!$ajaxrequestraw) {
    util::send_error('Invalid Ajax request, use core/webapi AMD module for ajax requests', 400);
}
$ajaxrequest = json_decode($ajaxrequestraw, true);
unset($ajaxrequestraw);
if (json_last_error() !== JSON_ERROR_NONE or $ajaxrequest === null) {
    util::send_error('Invalid Ajax request, use core/webapi AMD module for ajax requests', 400);
}

if (!empty($ajaxrequest['operationName'])) {
    $ajaxrequest = [$ajaxrequest];
    $batched = false;
} else {
    if (!$ajaxrequest or !is_array($ajaxrequest)) {
        // Must be non-empty array.
        util::send_error('Invalid HTTP request, use core_webapi AMD module for ajax request', 400);
    }
    $batched = true;
}

foreach ($ajaxrequest as $op) {
    if (empty($op['operationName']) or !isset($op['variables']) or !is_array($op['variables'])) {
        util::send_error('Invalid HTTP request, use core_webapi AMD module for ajax request', 400);
    }
    if (!preg_match('/^[a-z][a-z0-9_]+$/D', $op['operationName'])) {
        util::send_error('Invalid Ajax request, use core/webapi AMD module for ajax requests', 400);
    }
    if (substr($op['operationName'], - strlen('_nosession')) !== '_nosession') {
        if (!defined('NO_MOODLE_COOKIES')) {
            define('NO_MOODLE_COOKIES', false);
            if (empty($_SERVER['HTTP_X_TOTARA_SESSKEY'])) {
                util::send_error('Invalid Ajax request, use core/webapi AMD module for ajax requests', 400);
            }
        }
    }
}
if (!defined('NO_MOODLE_COOKIES')) {
    // All operations have nosession suffix, this means we do nto have to wait for session lock.
    define('NO_MOODLE_COOKIES', true);
}

// ==== END OF NASTY HACK ====================================================


define('AJAX_SCRIPT', true);
define('NO_DEBUG_DISPLAY', true);

try {
    require(__DIR__ . '/../../config.php');
    set_exception_handler([util::class, 'exception_handler']);
    set_error_handler([util::class, 'error_handler'], E_ALL | E_STRICT);
} catch (Throwable $e) {
    error_log('AJAX API error: exception during set up stage - ' . $e->getMessage());
    util::send_error('Unknown internal error', 500);
}

if (!NO_MOODLE_COOKIES and !confirm_sesskey($_SERVER['HTTP_X_TOTARA_SESSKEY'])) {
    util::send_error('Invalid sesskey, page reload required', 401);
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/totara/webapi/ajax.php');

$ajaxrequest = fix_utf8($ajaxrequest);
$return = [];

foreach ($ajaxrequest as $op) {
    try {
        $result = graphql::execute_operation(execution_context::create('ajax', $op['operationName']), $op['variables']);
        $result->setErrorsHandler([util::class, 'graphql_error_handler']);
        $return[] = $result->toArray((bool)$CFG->debugdeveloper);
    } catch (Throwable $ex) {
        $return[] = [
            'errors' => [\GraphQL\Error\FormattedError::createFromException($ex, (bool)$CFG->debugdeveloper)],
        ];
    }
}

if ($batched) {
    util::send_response($return, 200);
} else {
    util::send_response($return[0], 200);
}
