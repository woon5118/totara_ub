<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file keeps track of upgrades to Moodle.
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package   core_install
 * @category  upgrade
 * @copyright 2006 onwards Martin Dougiamas  http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Main upgrade tasks to be executed on Moodle version bump
 *
 * This function is automatically executed after one bump in the Moodle core
 * version is detected. It's in charge of performing the required tasks
 * to raise core from the previous version to the next one.
 *
 * It's a collection of ordered blocks of code, named "upgrade steps",
 * each one performing one isolated (from the rest of steps) task. Usually
 * tasks involve creating new DB objects or performing manipulation of the
 * information for cleanup/fixup purposes.
 *
 * Each upgrade step has a fixed structure, that can be summarised as follows:
 *
 * if ($oldversion < XXXXXXXXXX.XX) {
 *     // Explanation of the update step, linking to issue in the Tracker if necessary
 *     upgrade_set_timeout(XX); // Optional for big tasks
 *     // Code to execute goes here, usually the XMLDB Editor will
 *     // help you here. See {@link http://docs.moodle.org/dev/XMLDB_editor}.
 *     upgrade_main_savepoint(true, XXXXXXXXXX.XX);
 * }
 *
 * All plugins within Moodle (modules, blocks, reports...) support the existence of
 * their own upgrade.php file, using the "Frankenstyle" component name as
 * defined at {@link http://docs.moodle.org/dev/Frankenstyle}, for example:
 *     - {@link xmldb_page_upgrade($oldversion)}. (modules don't require the plugintype ("mod_") to be used.
 *     - {@link xmldb_auth_manual_upgrade($oldversion)}.
 *     - {@link xmldb_workshopform_accumulative_upgrade($oldversion)}.
 *     - ....
 *
 * In order to keep the contents of this file reduced, it's allowed to create some helper
 * functions to be used here in the {@link upgradelib.php} file at the same directory. Note
 * that such a file must be manually included from upgrade.php, and there are some restrictions
 * about what can be used within it.
 *
 * For more information, take a look to the documentation available:
 *     - Data definition API: {@link http://docs.moodle.org/dev/Data_definition_API}
 *     - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_main_upgrade($oldversion) {
    global $CFG, $DB;
    require_once(__DIR__ .'/upgradelib.php');

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    if ($oldversion < 2015111606.02) {

        // Define index attemptstepid-name (unique) to be dropped from question_attempt_step_data.
        $table = new xmldb_table('question_attempt_step_data');
        $index = new xmldb_index('attemptstepid-name', XMLDB_INDEX_UNIQUE, array('attemptstepid', 'name'));

        // Conditionally launch drop index attemptstepid-name.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2015111606.02);
    }

    if ($oldversion < 2016011200.00) {

        // Force uninstall of deleted tool.
        if (!file_exists("$CFG->dirroot/webservice/amf")) {
            // Remove capabilities.
            capabilities_cleanup('webservice_amf');
            // Remove all other associated config.
            unset_all_config_for_plugin('webservice_amf');
        }
        upgrade_main_savepoint(true, 2016011200.00);
    }

    if ($oldversion < 2016011300.01) {

        // This is a big upgrade script. We create new table tag_coll and the field
        // tag.tagcollid pointing to it.

        // Define table tag_coll to be created.
        $table = new xmldb_table('tag_coll');

        // Adding fields to table tagcloud.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('isdefault', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('searchable', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('customurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table tagcloud.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for tagcloud.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Table {tag}.
        // Define index name (unique) to be dropped form tag - we will replace it with index on (tagcollid,name) later.
        $table = new xmldb_table('tag');
        $index = new xmldb_index('name', XMLDB_INDEX_UNIQUE, array('name'));

        // Conditionally launch drop index name.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define field tagcollid to be added to tag, we create it as null first and will change to notnull later.
        $table = new xmldb_table('tag');
        $field = new xmldb_field('tagcollid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'userid');

        // Conditionally launch add field tagcloudid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.01);
    }

    if ($oldversion < 2016011300.02) {
        // Create a default tag collection if not exists and update the field tag.tagcollid to point to it.
        if (!$tcid = $DB->get_field_sql('SELECT id FROM {tag_coll} ORDER BY isdefault DESC, sortorder, id', null,
                IGNORE_MULTIPLE)) {
            $tcid = $DB->insert_record('tag_coll', array('isdefault' => 1, 'sortorder' => 0));
        }
        $DB->execute('UPDATE {tag} SET tagcollid = ? WHERE tagcollid IS NULL', array($tcid));

        // Define index tagcollname (unique) to be added to tag.
        $table = new xmldb_table('tag');
        $index = new xmldb_index('tagcollname', XMLDB_INDEX_UNIQUE, array('tagcollid', 'name'));
        $field = new xmldb_field('tagcollid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'userid');

        // Conditionally launch add index tagcollname.
        if (!$dbman->index_exists($table, $index)) {
            // Launch change of nullability for field tagcollid.
            $dbman->change_field_notnull($table, $field);
            $dbman->add_index($table, $index);
        }

        // Define key tagcollid (foreign) to be added to tag.
        $table = new xmldb_table('tag');
        $key = new xmldb_key('tagcollid', XMLDB_KEY_FOREIGN, array('tagcollid'), 'tag_coll', array('id'));

        // Launch add key tagcloudid.
        $dbman->add_key($table, $key);

        // Define index tagcolltype (not unique) to be added to tag.
        $table = new xmldb_table('tag');
        $index = new xmldb_index('tagcolltype', XMLDB_INDEX_NOTUNIQUE, array('tagcollid', 'tagtype'));

        // Conditionally launch add index tagcolltype.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.02);
    }

    if ($oldversion < 2016011300.03) {

        // Define table tag_area to be created.
        $table = new xmldb_table('tag_area');

        // Adding fields to table tag_area.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemtype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('tagcollid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('callback', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('callbackfile', XMLDB_TYPE_CHAR, '100', null, null, null, null);

        // Adding keys to table tag_area.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('tagcollid', XMLDB_KEY_FOREIGN, array('tagcollid'), 'tag_coll', array('id'));

        // Adding indexes to table tag_area.
        $table->add_index('compitemtype', XMLDB_INDEX_UNIQUE, array('component', 'itemtype'));

        // Conditionally launch create table for tag_area.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.03);
    }

    if ($oldversion < 2016011300.04) {

        // Define index itemtype-itemid-tagid-tiuserid (unique) to be dropped form tag_instance.
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('itemtype-itemid-tagid-tiuserid', XMLDB_INDEX_UNIQUE,
                array('itemtype', 'itemid', 'tagid', 'tiuserid'));

        // Conditionally launch drop index itemtype-itemid-tagid-tiuserid.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.04);
    }

    if ($oldversion < 2016011300.05) {

        $DB->execute("UPDATE {tag_instance} SET component = ? WHERE component IS NULL", array(''));

        // Changing nullability of field component on table tag_instance to not null.
        $table = new xmldb_table('tag_instance');
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'tagid');

        // Launch change of nullability for field component.
        $dbman->change_field_notnull($table, $field);

        // Changing type of field itemtype on table tag_instance to char.
        $table = new xmldb_table('tag_instance');
        $field = new xmldb_field('itemtype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'component');

        // Launch change of type for field itemtype.
        $dbman->change_field_type($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.05);
    }

    if ($oldversion < 2016011300.06) {

        // Define index taggeditem (unique) to be added to tag_instance.
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taggeditem', XMLDB_INDEX_UNIQUE, array('component', 'itemtype', 'itemid', 'tiuserid', 'tagid'));

        // Conditionally launch add index taggeditem.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.06);
    }

    if ($oldversion < 2016011300.07) {

        // Define index taglookup (not unique) to be added to tag_instance.
        $table = new xmldb_table('tag_instance');
        $index = new xmldb_index('taglookup', XMLDB_INDEX_NOTUNIQUE, array('itemtype', 'component', 'tagid', 'contextid'));

        // Conditionally launch add index taglookup.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016011300.07);
    }

    if ($oldversion < 2016011301.00) {

        // Force uninstall of deleted tool.
        if (!file_exists("$CFG->dirroot/webservice/amf")) {
            // Remove capabilities.
            capabilities_cleanup('webservice_amf');
            // Remove all other associated config.
            unset_all_config_for_plugin('webservice_amf');
        }
        upgrade_main_savepoint(true, 2016011301.00);
    }

    if ($oldversion < 2016011901.00) {

        // Convert calendar_lookahead to nearest new value.
        $transaction = $DB->start_delegated_transaction();

        // Count all users who curretly have that preference set (for progress bar).
        $total = $DB->count_records_select('user_preferences', "name = 'calendar_lookahead' AND value != '0'");
        $pbar = new progress_bar('upgradecalendarlookahead', 500, true);

        // Get all these users, one at a time.
        $rs = $DB->get_recordset_select('user_preferences', "name = 'calendar_lookahead' AND value != '0'");
        $i = 0;
        foreach ($rs as $userpref) {

            // Calculate and set new lookahead value.
            if ($userpref->value > 90) {
                $newvalue = 120;
            } else if ($userpref->value > 60 and $userpref->value < 90) {
                $newvalue = 90;
            } else if ($userpref->value > 30 and $userpref->value < 60) {
                $newvalue = 60;
            } else if ($userpref->value > 21 and $userpref->value < 30) {
                $newvalue = 30;
            } else if ($userpref->value > 14 and $userpref->value < 21) {
                $newvalue = 21;
            } else if ($userpref->value > 7 and $userpref->value < 14) {
                $newvalue = 14;
            } else {
                $newvalue = $userpref->value;
            }

            $DB->set_field('user_preferences', 'value', $newvalue, array('id' => $userpref->id));

            // Update progress.
            $i++;
            $pbar->update($i, $total, "Upgrading user preference settings - $i/$total.");
        }
        $rs->close();
        $transaction->allow_commit();

        upgrade_main_savepoint(true, 2016011901.00);
    }

    if ($oldversion < 2016020200.00) {

        // Define field isstandard to be added to tag.
        $table = new xmldb_table('tag');
        $field = new xmldb_field('isstandard', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'rawname');

        // Conditionally launch add field isstandard.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define index tagcolltype (not unique) to be dropped form tag.
        $index = new xmldb_index('tagcolltype', XMLDB_INDEX_NOTUNIQUE, array('tagcollid', 'tagtype'));

        // Conditionally launch drop index tagcolltype.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define index tagcolltype (not unique) to be added to tag.
        $index = new xmldb_index('tagcolltype', XMLDB_INDEX_NOTUNIQUE, array('tagcollid', 'isstandard'));

        // Conditionally launch add index tagcolltype.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Define field tagtype to be dropped from tag.
        $field = new xmldb_field('tagtype');

        // Conditionally launch drop field tagtype and update isstandard.
        if ($dbman->field_exists($table, $field)) {
            $DB->execute("UPDATE {tag} SET isstandard=(CASE WHEN (tagtype = ?) THEN 1 ELSE 0 END)", array('official'));
            $dbman->drop_field($table, $field);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016020200.00);
    }

    if ($oldversion < 2016020201.00) {

        // Define field showstandard to be added to tag_area.
        $table = new xmldb_table('tag_area');
        $field = new xmldb_field('showstandard', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'callbackfile');

        // Conditionally launch add field showstandard.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // By default set user area to hide standard tags. 2 = core_tag_tag::HIDE_STANDARD (can not use constant here).
        $DB->execute("UPDATE {tag_area} SET showstandard = ? WHERE itemtype = ? AND component = ?",
            array(2, 'user', 'core'));

        // Changing precision of field enabled on table tag_area to (1).
        $table = new xmldb_table('tag_area');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'itemtype');

        // Launch change of precision for field enabled.
        $dbman->change_field_precision($table, $field);

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016020201.00);
    }

    if ($oldversion < 2016021100.01) {
        // This could take a long time. Unfortunately, no way to know how long, and no way to do progress, so setting for 1 hour.
        upgrade_set_timeout(3600);

        // Define index userid-itemid (not unique) to be added to grade_grades_history.
        $table = new xmldb_table('grade_grades_history');
        $index = new xmldb_index('userid-itemid-timemodified', XMLDB_INDEX_NOTUNIQUE, array('userid', 'itemid', 'timemodified'));

        // Conditionally launch add index userid-itemid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016021100.01);
    }

    if ($oldversion < 2016030103.00) {

        // MDL-50887. Implement plugins infrastructure for antivirus and create ClamAV plugin.
        // This routine moves core ClamAV configuration to plugin level.

        // If clamav was configured and enabled, enable the plugin.
        if (!empty($CFG->runclamonupload) && !empty($CFG->pathtoclam)) {
            set_config('antiviruses', 'clamav');
        } else {
            set_config('antiviruses', '');
        }

        if (isset($CFG->runclamonupload)) {
            // Just unset global configuration, we have already enabled the plugin
            // which implies that ClamAV will be used for scanning uploaded files.
            unset_config('runclamonupload');
        }
        // Move core ClamAV configuration settings to plugin.
        if (isset($CFG->pathtoclam)) {
            set_config('pathtoclam', $CFG->pathtoclam, 'antivirus_clamav');
            unset_config('pathtoclam');
        }
        if (isset($CFG->quarantinedir)) {
            set_config('quarantinedir', $CFG->quarantinedir, 'antivirus_clamav');
            unset_config('quarantinedir');
        }
        if (isset($CFG->clamfailureonupload)) {
            set_config('clamfailureonupload', $CFG->clamfailureonupload, 'antivirus_clamav');
            unset_config('clamfailureonupload');
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016030103.00);
    }

    if ($oldversion < 2016030400.01) {
        // Add the new services field.
        $table = new xmldb_table('external_functions');
        $field = new xmldb_field('services', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'capabilities');

        // Conditionally launch add field services.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016030400.01);
    }

    if ($oldversion < 2016041500.50) {

        // Define table competency to be created.
        $table = new xmldb_table('competency');

        // Adding fields to table competency.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('competencyframeworkid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('parentid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ruletype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('ruleoutcome', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('ruleconfig', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('scaleid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('scaleconfiguration', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency.
        $table->add_index('idnumberframework', XMLDB_INDEX_UNIQUE, array('competencyframeworkid', 'idnumber'));
        $table->add_index('ruleoutcome', XMLDB_INDEX_NOTUNIQUE, array('ruleoutcome'));

        // Conditionally launch create table for competency.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.50);
    }

    if ($oldversion < 2016041500.51) {

        // Define table competency_coursecompsetting to be created.
        $table = new xmldb_table('competency_coursecompsetting');

        // Adding fields to table competency_coursecompsetting.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('pushratingstouserplans', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency_coursecompsetting.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('courseidlink', XMLDB_KEY_FOREIGN_UNIQUE, array('courseid'), 'course', array('id'));

        // Conditionally launch create table for competency_coursecompsetting.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.51);
    }

    if ($oldversion < 2016041500.52) {

        // Define table competency_framework to be created.
        $table = new xmldb_table('competency_framework');

        // Adding fields to table competency_framework.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('idnumber', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('scaleid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('scaleconfiguration', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('taxonomies', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency_framework.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_framework.
        $table->add_index('idnumber', XMLDB_INDEX_UNIQUE, array('idnumber'));

        // Conditionally launch create table for competency_framework.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.52);
    }

    if ($oldversion < 2016041500.53) {

        // Define table competency_coursecomp to be created.
        $table = new xmldb_table('competency_coursecomp');

        // Adding fields to table competency_coursecomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ruleoutcome', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_coursecomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('courseidlink', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->add_key('competencyid', XMLDB_KEY_FOREIGN, array('competencyid'), 'competency_competency', array('id'));

        // Adding indexes to table competency_coursecomp.
        $table->add_index('courseidruleoutcome', XMLDB_INDEX_NOTUNIQUE, array('courseid', 'ruleoutcome'));
        $table->add_index('courseidcompetencyid', XMLDB_INDEX_UNIQUE, array('courseid', 'competencyid'));

        // Conditionally launch create table for competency_coursecomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.53);
    }

    if ($oldversion < 2016041500.54) {

        // Define table competency_plan to be created.
        $table = new xmldb_table('competency_plan');

        // Adding fields to table competency_plan.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('origtemplateid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('duedate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('reviewerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_plan.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_plan.
        $table->add_index('useridstatus', XMLDB_INDEX_NOTUNIQUE, array('userid', 'status'));
        $table->add_index('templateid', XMLDB_INDEX_NOTUNIQUE, array('templateid'));
        $table->add_index('statusduedate', XMLDB_INDEX_NOTUNIQUE, array('status', 'duedate'));

        // Conditionally launch create table for competency_plan.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.54);
    }

    if ($oldversion < 2016041500.55) {

        // Define table competency_template to be created.
        $table = new xmldb_table('competency_template');

        // Adding fields to table competency_template.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('duedate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency_template.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for competency_template.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.55);
    }

    if ($oldversion < 2016041500.56) {

        // Define table competency_templatecomp to be created.
        $table = new xmldb_table('competency_templatecomp');

        // Adding fields to table competency_templatecomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table competency_templatecomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('templateidlink', XMLDB_KEY_FOREIGN, array('templateid'), 'competency_template', array('id'));
        $table->add_key('competencyid', XMLDB_KEY_FOREIGN, array('competencyid'), 'competency_competency', array('id'));

        // Conditionally launch create table for competency_templatecomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.56);
    }

    if ($oldversion < 2016041500.57) {

        // Define table competency_templatecohort to be created.
        $table = new xmldb_table('competency_templatecohort');

        // Adding fields to table competency_templatecohort.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cohortid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_templatecohort.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_templatecohort.
        $table->add_index('templateid', XMLDB_INDEX_NOTUNIQUE, array('templateid'));
        $table->add_index('templatecohortids', XMLDB_INDEX_UNIQUE, array('templateid', 'cohortid'));

        // Conditionally launch create table for competency_templatecohort.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.57);
    }

    if ($oldversion < 2016041500.58) {

        // Define table competency_relatedcomp to be created.
        $table = new xmldb_table('competency_relatedcomp');

        // Adding fields to table competency_relatedcomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('relatedcompetencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_relatedcomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for competency_relatedcomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.58);
    }

    if ($oldversion < 2016041500.59) {

        // Define table competency_usercomp to be created.
        $table = new xmldb_table('competency_usercomp');

        // Adding fields to table competency_usercomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('reviewerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('proficiency', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_usercomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_usercomp.
        $table->add_index('useridcompetency', XMLDB_INDEX_UNIQUE, array('userid', 'competencyid'));

        // Conditionally launch create table for competency_usercomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.59);
    }

    if ($oldversion < 2016041500.60) {

        // Define table competency_usercompcourse to be created.
        $table = new xmldb_table('competency_usercompcourse');

        // Adding fields to table competency_usercompcourse.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('proficiency', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_usercompcourse.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_usercompcourse.
        $table->add_index('useridcoursecomp', XMLDB_INDEX_UNIQUE, array('userid', 'courseid', 'competencyid'));

        // Conditionally launch create table for competency_usercompcourse.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.60);
    }

    if ($oldversion < 2016041500.61) {

        // Define table competency_usercompplan to be created.
        $table = new xmldb_table('competency_usercompplan');

        // Adding fields to table competency_usercompplan.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('proficiency', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_usercompplan.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_usercompplan.
        $table->add_index('usercompetencyplan', XMLDB_INDEX_UNIQUE, array('userid', 'competencyid', 'planid'));

        // Conditionally launch create table for competency_usercompplan.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.61);
    }

    if ($oldversion < 2016041500.62) {

        // Define table competency_plancomp to be created.
        $table = new xmldb_table('competency_plancomp');

        // Adding fields to table competency_plancomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('planid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_plancomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_plancomp.
        $table->add_index('planidcompetencyid', XMLDB_INDEX_UNIQUE, array('planid', 'competencyid'));

        // Conditionally launch create table for competency_plancomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.62);
    }

    if ($oldversion < 2016041500.63) {

        // Define table competency_evidence to be created.
        $table = new xmldb_table('competency_evidence');

        // Adding fields to table competency_evidence.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('usercompetencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('action', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('actionuserid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('descidentifier', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('desccomponent', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('desca', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('note', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_evidence.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_evidence.
        $table->add_index('usercompetencyid', XMLDB_INDEX_NOTUNIQUE, array('usercompetencyid'));

        // Conditionally launch create table for competency_evidence.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.63);
    }

    if ($oldversion < 2016041500.64) {

        // Define table competency_userevidence to be created.
        $table = new xmldb_table('competency_userevidence');

        // Adding fields to table competency_userevidence.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('descriptionformat', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('url', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_userevidence.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_userevidence.
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch create table for competency_userevidence.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.64);
    }

    if ($oldversion < 2016041500.65) {

        // Define table competency_userevidencecomp to be created.
        $table = new xmldb_table('competency_userevidencecomp');

        // Adding fields to table competency_userevidencecomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userevidenceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_userevidencecomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table competency_userevidencecomp.
        $table->add_index('userevidenceid', XMLDB_INDEX_NOTUNIQUE, array('userevidenceid'));
        $table->add_index('userevidencecompids', XMLDB_INDEX_UNIQUE, array('userevidenceid', 'competencyid'));

        // Conditionally launch create table for competency_userevidencecomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.65);
    }

    if ($oldversion < 2016041500.66) {

        // Define table competency_modulecomp to be created.
        $table = new xmldb_table('competency_modulecomp');

        // Adding fields to table competency_modulecomp.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('competencyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('ruleoutcome', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table competency_modulecomp.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('cmidkey', XMLDB_KEY_FOREIGN, array('cmid'), 'course_modules', array('id'));
        $table->add_key('competencyidkey', XMLDB_KEY_FOREIGN, array('competencyid'), 'competency_competency', array('id'));

        // Adding indexes to table competency_modulecomp.
        $table->add_index('cmidruleoutcome', XMLDB_INDEX_NOTUNIQUE, array('cmid', 'ruleoutcome'));
        $table->add_index('cmidcompetencyid', XMLDB_INDEX_UNIQUE, array('cmid', 'competencyid'));

        // Conditionally launch create table for competency_modulecomp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016041500.66);
    }

    if ($oldversion < 2016040700.01) {
        // Update all countries to upper case.
        $DB->execute("UPDATE {user} SET country = UPPER(country)");
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016040700.01);
    }

    if ($oldversion < 2016042600.01) {
        $deprecatedwebservices = [
            'moodle_course_create_courses',
            'moodle_course_get_courses',
            'moodle_enrol_get_enrolled_users',
            'moodle_enrol_get_users_courses',
            'moodle_enrol_manual_enrol_users',
            'moodle_file_get_files',
            'moodle_file_upload',
            'moodle_group_add_groupmembers',
            'moodle_group_create_groups',
            'moodle_group_delete_groupmembers',
            'moodle_group_delete_groups',
            'moodle_group_get_course_groups',
            'moodle_group_get_groupmembers',
            'moodle_group_get_groups',
            'moodle_message_send_instantmessages',
            'moodle_notes_create_notes',
            'moodle_role_assign',
            'moodle_role_unassign',
            'moodle_user_create_users',
            'moodle_user_delete_users',
            'moodle_user_get_course_participants_by_id',
            'moodle_user_get_users_by_courseid',
            'moodle_user_get_users_by_id',
            'moodle_user_update_users',
            'core_grade_get_definitions',
            'core_user_get_users_by_id',
            'moodle_webservice_get_siteinfo',
            'mod_forum_get_forum_discussions'
        ];

        list($insql, $params) = $DB->get_in_or_equal($deprecatedwebservices);
        $DB->delete_records_select('external_functions', "name $insql", $params);
        $DB->delete_records_select('external_services_functions', "functionname $insql", $params);
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016042600.01);
    }

    if ($oldversion < 2016051300.00) {
        // Add a default competency rating scale.
        make_competence_scale();

        // Savepoint reached.
        upgrade_main_savepoint(true, 2016051300.00);
    }
    
    if ($oldversion < 2016051700.01) {
        // This script is included in each major version upgrade process (3.0, 3.1) so make sure we don't run it twice.
        if (empty($CFG->upgrade_letterboundarycourses)) {
            // MDL-45390. If a grade is being displayed with letters and the grade boundaries are not being adhered to properly
            // then this course will also be frozen.
            // If the changes are accepted then the display of some grades may change.
            // This is here to freeze the gradebook in affected courses.
            upgrade_course_letter_boundary();

            // To skip running the same script on the upgrade to the next major version release.
            set_config('upgrade_letterboundarycourses', 1);
        }
        // Main savepoint reached.
        upgrade_main_savepoint(true, 2016051700.01);
    }

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016052301.08) {
        // Default schedule values.
        $hour = 0;
        $minute = 0;

        // Get the old settings.
        if (isset($CFG->statsruntimestarthour)) {
            $hour = $CFG->statsruntimestarthour;
        }
        if (isset($CFG->statsruntimestartminute)) {
            $minute = $CFG->statsruntimestartminute;
        }

        // Retrieve the scheduled task record first.
        $stattask = $DB->get_record('task_scheduled', array('component' => 'moodle', 'classname' => '\core\task\stats_cron_task'));

        // Don't touch customised scheduling.
        if ($stattask && !$stattask->customised) {

            $nextruntime = mktime($hour, $minute, 0, date('m'), date('d'), date('Y'));
            if ($nextruntime < $stattask->lastruntime) {
                // Add 24 hours to the next run time.
                $newtime = new DateTime();
                $newtime->setTimestamp($nextruntime);
                $newtime->add(new DateInterval('P1D'));
                $nextruntime = $newtime->getTimestamp();
            }
            $stattask->nextruntime = $nextruntime;
            $stattask->minute = $minute;
            $stattask->hour = $hour;
            $stattask->customised = 1;
            $DB->update_record('task_scheduled', $stattask);
        }
        // These settings are no longer used.
        unset_config('statsruntimestarthour');
        unset_config('statsruntimestartminute');
        unset_config('statslastexecution');

        upgrade_main_savepoint(true, 2016052301.08);
    }
    return true;
}
