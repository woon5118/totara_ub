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
 * @package auth_oauth2
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_auth_oauth2_premigrate() {
    global $DB;
    $dbman = $DB->get_manager();

    $version = premigrate_get_plugin_version('auth', 'oauth2');

    if ($version > 2020061500) {
        throw new coding_exception("Invalid plugin (auth_oauth2) version ($version) for pre-migration");
    }

    // Moodle 3.9 pre-migration line.

    // OAuth was backported from Moodle 3.8.1
    if ($version > 2019111800) {
        $version = premigrate_plugin_savepoint(2019111800, 'auth', 'oauth2');
    }
}
