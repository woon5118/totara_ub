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
 * @package qtype_essay
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_qtype_essay_premigrate() {
    global $DB;
    $dbman = $DB->get_manager();

    $version = premigrate_get_plugin_version('qtype', 'essay');

    if ($version > 2018051400) {
        throw new coding_exception("Invalid plugin (qtype_essay) version ($version) for pre-migration");
    }

    if ($version >= 2018021800) {
        $table = new xmldb_table('qtype_essay_options');
        $field = new xmldb_field('filetypeslist', XMLDB_TYPE_TEXT, null, null, null, null, null, 'responsetemplateformat');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $version = premigrate_plugin_savepoint(2018021700, 'qtype', 'essay');
    }

    // Plugin is ready for migration from Moodle 3.4.9 to Totara 13.
    if ($version > 2017111300) {
        $version = premigrate_plugin_savepoint(2017111300, 'qtype', 'essay');
    }
}