<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 * @author Andrew Bell <andrewb@learningpool.com>
 * @author Ryan Lynch <ryanlynch@learningpool.com>
 * @author Barry McKay <barry@learningpool.com>
 *
 * @package auth_approved
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_auth_approved_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019060400) {

        // Define field extradata to be added to auth_approved_request.
        $table = new xmldb_table('auth_approved_request');
        $field = new xmldb_field('extradata', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timeresolved');

        // Conditionally launch add field extradata.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Approved savepoint reached.
        upgrade_plugin_savepoint(true, 2019060400, 'auth', 'approved');
    }

    if ($oldversion < 2020090102) {
        // Replace unlimited passwords with disabled expiry.
        $expirytime = get_config('auth_approved', 'expirationtime');
        if ($expirytime !== false && $expirytime <= 0) {
            set_config('expiration', 0, 'auth_approved');
            set_config('expirationtime', 30, 'auth_approved');
        }
        upgrade_plugin_savepoint(true, 2020090102, 'auth', 'approved');
    }

    return true;
}
