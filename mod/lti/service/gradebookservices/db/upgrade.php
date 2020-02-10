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
 * @author Simon Player <simon.player@totaralearning.com>
 * @package mod_lti
 */

defined('MOODLE_INTERNAL') || die;

/**
 * xmldb_ltiservice_gradebookservices_upgrade is the function that upgrades
 * the ltiservice_gradebookservices database when is needed
 *
 * This function is automaticly called when version number in
 * version.php changes.
 *
 * @param int $oldversion New old version number.
 *
 * @return boolean
 *
 */
function xmldb_ltiservice_gradebookservices_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019031304) {
        $table = new xmldb_table('ltiservice_gradebookservices');
        $key = new xmldb_key('itemnumbercourse', XMLDB_KEY_FOREIGN, ['gradeitemid', 'courseid'], 'grade_items', ['id']);
        if ($dbman->key_exists($table, $key)) {
            $dbman->drop_key($table, $key);
        }

        // Lti savepoint reached.
        upgrade_mod_savepoint(true, 2019031304, 'ltiservice_gradebookservices');
    }

    return true;
}
