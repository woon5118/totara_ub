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
 * @package mod_quiz
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_mod_quiz_premigrate() {
    global $DB;
    $dbman = $DB->get_manager();

    $version = premigrate_get_plugin_version('mod', 'quiz');

    if ($version > 2020061500) {
        throw new coding_exception("Invalid plugin (mod_quiz) version ($version) for pre-migration");
    }

    // Moodle 3.9 pre-migration line.

    // Moodle 3.8 pre-migration line.

    // Moodle 3.7 pre-migration line.

    // Moodle 3.6 pre-migration line.

    if ($version >= 2018040800) {
        $table = new xmldb_table('quiz_slot_tags');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $version = premigrate_plugin_savepoint(2018040700, 'mod', 'quiz');
    }

    if ($version >= 2018020700) {
        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('includingsubcategories', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'questioncategoryid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('quiz_slots');
        $key = new xmldb_key('questioncategoryid', XMLDB_KEY_FOREIGN, array('questioncategoryid'), 'question_categories', ['id']);
        $dbman->drop_key($table, $key);

        $table = new xmldb_table('quiz_slots');
        $field = new xmldb_field('questioncategoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'questionid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2018020600, 'mod', 'quiz');
    }

    // Plugin is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111300) {
        $version = premigrate_plugin_savepoint(2017111300, 'mod', 'quiz');
    }
}