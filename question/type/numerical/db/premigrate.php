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
 * @package qtype_numerical
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_qtype_numerical_premigrate() {
    global $DB;
    $dbman = $DB->get_manager();

    $version = premigrate_get_plugin_version('qtype', 'numerical');

    if ($version > 2019111800) {
        throw new coding_exception("Invalid plugin (qtype_numerical) version ($version) for pre-migration");
    }

    // Moodle 3.8 pre-migration line.

    // Moodle 3.7 pre-migration line.

    // Moodle 3.6 pre-migration line.

    if ($version >= 2017121000) {
        $table = new xmldb_table('question_numerical_units');
        $field = new xmldb_field('multiplier', XMLDB_TYPE_NUMBER, '40, 20', null, XMLDB_NOTNULL, null, "1.00000000000000000000", 'question');
        $dbman->change_field_type($table, $field);

        $version = premigrate_plugin_savepoint(2017120900, 'qtype', 'numerical');
    }

    // Plugin is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111300) {
        $version = premigrate_plugin_savepoint(2017111300, 'qtype', 'numerical');
    }
}