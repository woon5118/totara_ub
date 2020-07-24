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
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @package totara_core
 */

/*
 * This file is executed before migration from vanilla Moodle installation.
 */

defined('MOODLE_INTERNAL') || die();
global $DB, $CFG;
require_once(__DIR__ . '/upgradelib.php');

//NOTE: do not use any APIs here, this is strictly for low level DB modifications that are required
//      to get through the core upgrade steps.

$dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

// Always update all language packs if we can, because they are used in Totara upgrades/install.
totara_upgrade_installed_languages();

// Add parentid to context table and create context_map table.
totara_core_upgrade_context_tables();

// Add custom Totara completion field to prevent fatal problems during upgrade.
$table = new xmldb_table('course_completions');
$field = new xmldb_field('invalidatecache', XMLDB_TYPE_INTEGER, '1', null, null, null, '0', 'reaggregate');
if (!$dbman->field_exists($table, $field)) {
    $dbman->add_field($table, $field);
}

// Update the indexes on the course_info_data table.
$table = new xmldb_table('course_completion_criteria');
$index = new xmldb_index('moduleinstance', XMLDB_INDEX_NOTUNIQUE, array('moduleinstance'));
if (!$dbman->index_exists($table, $index)) {
    $dbman->add_index($table, $index);
}

// Migrate old block titles to the new common config storage.
totara_core_migrate_old_block_titles();

// One-off fix for incorrect default setting from Moodle.
if (!get_config('scorm', 'protectpackagedownloads')) {
    unset_config('protectpackagedownloads', 'scorm');
}

// Remove all analytics tables.
$tables = [
    'analytics_models', 'analytics_models_log', 'analytics_predictions', 'analytics_train_samples',
    'analytics_predict_samples', 'analytics_used_files', 'analytics_indicator_calc',
    'analytics_prediction_actions', 'analytics_used_analysables'
];
foreach ($tables as $tablename) {
    $table = new xmldb_table($tablename);
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }
}

// Undo calendar index changes.
$table = new xmldb_table('event');
$field = new xmldb_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'format');
$index = new xmldb_index('groupid-courseid-categoryid-visible-userid', XMLDB_INDEX_NOTUNIQUE, array('groupid', 'courseid', 'categoryid', 'visible', 'userid'));
if ($dbman->index_exists($table, $index)) {
    $dbman->drop_index($table, $index);
}
$index = new xmldb_index('groupid-courseid-visible-userid', XMLDB_INDEX_NOTUNIQUE, array('groupid', 'courseid', 'visible', 'userid'));
if (!$dbman->index_exists($table, $index)) {
    $dbman->add_index($table, $index);
}

// Delete category subscritpions.
$DB->delete_records_select('event_subscriptions', "categoryid <> 0");

// Remove 'navigation' block instances from the system.
$blockids = $DB->get_fieldset_select('block_instances', 'id', 'blockname = ?', ['navigation']);
if (!empty($blockids)) {
    foreach ($blockids as $bid) {
        context_helper::delete_instance(CONTEXT_BLOCK, $bid);
        $DB->delete_records('block_positions', ['blockinstanceid' => $bid]);
        $DB->delete_records('block_instances', ['id' => $bid]);
        $DB->delete_records_list('user_preferences', 'name', ['block' . $bid . 'hidden', 'docked_block_instance_' . $bid]);
    }
}

// If the site was upgraded from Moodle 3.3.1+ the numsections format option does not exist as Moodle removed it.
// This method finds all courses in 'weeks' and 'topics' format that don't have the 'numsections' course format option
// and recreates it by using the actual number of sections.
$sql = "SELECT c.id, count(cs.section) AS sectionsactual
          FROM {course} c
          JOIN {course_sections} cs ON cs.course = c.id
     LEFT JOIN {course_format_options} n ON n.courseid = c.id AND
               n.format = c.format AND
               n.name = 'numsections' AND
               n.sectionid = 0
         WHERE c.format = :format AND cs.section > 0 AND n.id IS NULL
      GROUP BY c.id";
foreach (['weeks', 'topics'] as $format) {
    $params = ['format' => $format];
    $actuals = $DB->get_records_sql_menu($sql, $params);
    foreach ($actuals as $courseid => $sectionsactual) {
        $record = (object)[
            'courseid' => $courseid,
            'format' => $format,
            'sectionid' => 0,
            'name' => 'numsections',
            'value' => $sectionsactual
        ];
        $DB->insert_record('course_format_options', $record);
    }
}

// Delete unused completion column.
$table = new xmldb_table('course_modules_completion');
$field = new xmldb_field('overrideby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'viewed');
if ($dbman->field_exists($table, $field)) {
    $dbman->drop_field($table, $field);
}