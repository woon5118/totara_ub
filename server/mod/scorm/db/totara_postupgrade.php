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
 * @package    mod_scorm
 * @author     Petr Skoda <petr.skoda@totaralms.com>
 */

/**
 * Function for Totara specific DB changes to core Moodle plugins.
 *
 * Put code here rather than in db/upgrade.php if you need to change core
 * Moodle database schema for Totara-specific changes.
 *
 * This is executed during EVERY upgrade. Make sure your code can be
 * re-executed EVERY upgrade without problems.
 *
 * You need to increment the upstream plugin version by .01 to get
 * this code executed!
 *
 * Do not use savepoints in this code!
 *
 * @param string $version the plugin version
 */
function xmldb_scorm_totara_postupgrade($version) {
    global $DB;

    $dbman = $DB->get_manager();

    // TL-6829 Add mastery override option.
    $table = new xmldb_table('scorm');
    $field = new xmldb_field('masteryoverride', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'lastattemptlock');
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // MDL-64237: Remove un-used/large index on element field.
    $table = new xmldb_table('scorm_scoes_track');
    $index = new xmldb_index('element', XMLDB_INDEX_UNIQUE, ['element']);
    if ($dbman->index_exists($table, $index)) {
        $dbman->drop_index($table, $index);
    }

    // TL-20799 tracking of known trusted SCORM packages
    $table = new xmldb_table('scorm_trusted_packages');
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('contenthash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
    $table->add_field('uploadedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('uploadedby', XMLDB_KEY_FOREIGN, array('uploadedby'), 'user', array('id'));
    $table->add_index('contenthash', XMLDB_INDEX_UNIQUE, array('contenthash'));
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);

        // Mark all existing local packages as trusted, ignore synced packages though.
        $sql = 'INSERT INTO "ttr_scorm_trusted_packages" (contenthash, timecreated)

                SELECT f.contenthash, MIN(f.timecreated) AS timecreated
                  FROM "ttr_files" f
                  JOIN "ttr_context" ctx ON ctx.id = f.contextid AND ctx.contextlevel = 70
                  JOIN "ttr_course_modules" cm ON cm.id = ctx.instanceid
                  JOIN "ttr_modules" md ON md.id = cm.module AND md.name = \'scorm\'
                  JOIN "ttr_scorm" s ON s.id = cm.instance
                 WHERE f.component = \'mod_scorm\' AND f.filearea = \'package\' AND f.itemid = 0 AND f.filepath = \'/\' AND LOWER(f.filename) <> \'imsmanifest.xml\'
                       AND s.scormtype = \'local\' AND s.reference = f.filename AND f.referencefileid IS NULL
              GROUP BY f.contenthash';
        $DB->execute($sql);
    }

    // Totara mobile support
    $table = new xmldb_table('scorm');
    $field = new xmldb_field('allowmobileoffline', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'autocommit');
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
}
