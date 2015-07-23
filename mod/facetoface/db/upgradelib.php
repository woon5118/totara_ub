<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @package mod_facetoface
 */

/**
 * Facetoface module upgrade related helper functions
 *
 * @package    mod_facetoface
 * @author     Sam Hemelryk <sam.hemelryk@totaralms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Helper function to aid in the migration of signup custom field data.
 *
 * @param moodle_database $db
 * @param database_manager $dbman
 * @param xmldb_table $table
 * @param string $temptablename The name to use for the temporary table.
 * @param string $field The name of the field that is used as the id reference on the table.
 * @param string $where Where claus to limit the signup status selection if need be.
 */
function mod_facetoface_migrate_session_signup_customdata(moodle_database $db, database_manager $dbman, xmldb_table $table, $temptablename, $field, $where) {
    // Here we need to change the reference id for all facetoface custom field data records in a given table.
    // Because this involves all records we are going to use the following process to make this change.
    //
    //  1. Rename the data table to a temporary name
    //  2. Create a new data table
    //  3. Copy data from the temp table to the new data table fixing it along the way.
    //  4. Delete temp table
    //
    // This essentially only takes the data we want and drops the redundant data from the system as we no longer
    // want to keep it.

    $dbman->rename_table($table, $temptablename);
    // We don't care to check if the table exists, if it does then the above failed and the upgrade has failed.
    // They will need to reset and try again.
    $dbman->create_table($table);

    // It may take the database some time to execute this next step.
    upgrade_set_timeout();

    $sql = 'INSERT INTO {'.$table->getName().'}
                            (data, fieldid, '.$field.')
                     SELECT fsit.data, fsit.fieldid, fss.signupid
                       FROM {'.$temptablename.'} fsit
                       JOIN (
                          SELECT MAX(id) AS statusid, signupid
                            FROM {facetoface_signups_status}
                           WHERE '.$where.'
                        GROUP BY signupid
                       ) fss ON fss.statusid = fsit.'.$field;

    /** @var moodle_database $DB */
    $db->execute($sql);

    $temptable = new xmldb_table($temptablename);
    $dbman->drop_table($temptable);
}