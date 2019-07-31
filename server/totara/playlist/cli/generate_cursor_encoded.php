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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_playlist
 */
define('CLI_SCRIPT', true);
require_once(__DIR__ . "/../../../config.php");

use totara_playlist\pagination\cursor;

global $CFG;
require_once("{$CFG->dirroot}/lib/clilib.php");

[$options, $un_recognized] = cli_get_params(
    [
        'help'  => false,
        'limit' => 5,
        'page' => 1
    ],
    [
        'h' => 'help',
        'l' => 'limit',
        'p' => 'page'
    ]
);

if ($options['help']) {
    echo "
A script to generate the base_64 encoded string for the cursor of playlist.
Usage:
    php totara/playlist/cli/genrate_cursor_encoded.php -l=50 -p=5

Options:
    -h, --help      Print out this help.
    -l, --limit     The limit that you would want to set your cursor,
    -p, --page      The current page that you would want to set your cursor.
";

    return 0;
}

$page = 1;
$limit = 5;

if (isset($options['page'])) {
    $page = $options['page'];
}

if (isset($options['limit'])) {
    $limit = $options['limit'];
}

$cursor = new cursor();
$cursor->set_limit($limit);
$cursor->set_page($page);

echo "{$cursor->encode()}\n";
return 0;