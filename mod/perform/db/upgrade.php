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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

/**
 * Database upgrade script
 *
 * @param integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return bool
 *
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_perform_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020063000) {
        throw new upgrade_exception('mod_perform', '2020063000', 'Cannot upgrade from an earlier version - do a fresh install instead');
    }

    if ($oldversion < 2020063001) {

        // Changing type of field identifier on table perform_element to char.
        $table = new xmldb_table('perform_element');
        $field = new xmldb_field('identifier', XMLDB_TYPE_CHAR, '1024', null, true, null, null, 'title');

        // Launch change of type for field identifier.
        $dbman->change_field_type($table, $field);

        // Perform savepoint reached.
        upgrade_mod_savepoint(true, 2020063001, 'perform');
    }

    return true;
}
