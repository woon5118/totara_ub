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

use core\webapi\execution_context;
use totara_webapi\graphql;
use totara_webapi\local\util;

define('NO_DEBUG_DISPLAY', true);
define('AJAX_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/filelib.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/totara/webapi/dev_graphql_executor.php');

// ========================================================
// NOTE: this MUST NOT be enabled on production servers!
// ========================================================

if (!defined('GRAPHQL_DEVELOPMENT_MODE') or !GRAPHQL_DEVELOPMENT_MODE) {
    die;
}

try {
    $schema = graphql::get_schema();
    $schema->assertValid();

    $server = new \GraphQL\Server\StandardServer([
        'debug' => true,
        'schema' => $schema,
        'fieldResolver' => [graphql::class, 'default_resolver'],
        'rootValue' => graphql::get_server_root($schema),
        'context' => execution_context::create('dev', null),
        'errorsHandler' => [util::class, 'graphql_error_handler'],
    ]);
    $server->handleRequest();
} catch (Throwable $e) {
    \GraphQL\Server\StandardServer::send500Error($e, true, true);
}
