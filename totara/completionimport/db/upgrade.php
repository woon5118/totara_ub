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
 * @author Rusell England <russell.england@catalyst-eu.net>
 * @package totara
 * @subpackage completionimport
 */

/**
 * Local db upgrades for Totara completion import.
 */

require_once($CFG->dirroot.'/totara/core/db/utils.php');

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_totara_completionimport_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2014030400) {
        // Add index to username column to improve query performance.

        $table = new xmldb_table('totara_compl_import_course');
        $index = new xmldb_index('compimpcou_username_ix', XMLDB_INDEX_NOTUNIQUE, array('username'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table('totara_compl_import_cert');
        $index = new xmldb_index('compimpcer_username_ix', XMLDB_INDEX_NOTUNIQUE, array('username'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2014030400, 'totara', 'completionimport');
    }

    // T-14233 Add completiondateparsed columns to course and certification import tables.
    if ($oldversion < 2015030201) {

        $field = new xmldb_field('completiondateparsed', XMLDB_TYPE_INTEGER, '10', null, null, null, null,
            'importevidence');

        $table = new xmldb_table('totara_compl_import_course');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('totara_compl_import_cert');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2015030201, 'totara', 'completionimport');
    }

    // T-14308 Add "Use fixed expiry date" recertification option.
    // This adds field duedate to the certification completion import tool.
    if ($oldversion < 2015030202) {

        // Define field duedate to be added to totara_compl_import_cert.
        $table = new xmldb_table('totara_compl_import_cert');
        $field = new xmldb_field('duedate');
        $field->set_attributes(XMLDB_TYPE_CHAR, '10', null, null, null, null, null);

        // Launch add field duedate.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2015030202, 'totara', 'completionimport');
    }

    // TL-8118 Extend the Completion Import tool to support uploading evidence with custom fields.
    // This adds customfield field to the totara_compl_import_course table.
    if ($oldversion < 2016020800) {

        $table = new xmldb_table('totara_compl_import_course');
        $field = new xmldb_field('customfields', XMLDB_TYPE_TEXT, null, null, null, null, null, 'grade');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2016020800, 'totara', 'completionimport');
    }

    // TL-8118 Extend the Completion Import tool to support uploading evidence with custom fields.
    // This adds evidenceid field to the totara_compl_import_course table.
    if ($oldversion < 2016020801) {

        $table = new xmldb_table('totara_compl_import_course');
        $field = new xmldb_field('evidenceid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'importevidence');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $index = new xmldb_index('totacompimpocour_evi_ix', XMLDB_INDEX_UNIQUE, array('evidenceid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_plugin_savepoint(true, 2016020801, 'totara', 'completionimport');
    }

    // TL-8118 Extend the Completion Import tool to support uploading evidence with custom fields.
    // This adds customfield field to the totara_compl_import_cert table.
    if ($oldversion < 2016020802) {

        $table = new xmldb_table('totara_compl_import_cert');
        $field = new xmldb_field('customfields', XMLDB_TYPE_TEXT, null, null, null, null, null, 'completiondate');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2016020802, 'totara', 'completionimport');
    }

    // TL-8118 Extend the Completion Import tool to support uploading evidence with custom fields.
    // This adds evidenceid field to the totara_compl_import_cert table.
    if ($oldversion < 2016020803) {

        $table = new xmldb_table('totara_compl_import_cert');
        $field = new xmldb_field('evidenceid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'importevidence');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $index = new xmldb_index('totacompimpocert_evi_ix', XMLDB_INDEX_UNIQUE, array('evidenceid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_plugin_savepoint(true, 2016020803, 'totara', 'completionimport');
    }

    return true;
}
