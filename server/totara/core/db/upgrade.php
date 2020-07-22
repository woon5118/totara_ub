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
 * @author Jonathan Newman <jonathan.newman@catalyst.net.nz>
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @package totara
 * @subpackage totara_core
 */

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_totara_core_upgrade($oldversion) {
    global $CFG, $DB;
    require_once(__DIR__ . '/upgradelib.php');

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    if ($oldversion < 2017030800) {
        require_once($CFG->dirroot . '/totara/program/db/upgradelib.php');
        require_once($CFG->dirroot . '/totara/certification/db/upgradelib.php');

        // Create the timecreated column.
        $table = new xmldb_table('prog_completion');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, 10, false, XMLDB_NOTNULL, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Now clone the timestarted data into the timecreated field.
            $DB->execute("UPDATE {prog_completion} SET timecreated = timestarted");

            // Make sure the non zero upgrade has run prior to fix time started.
            totara_certification_upgrade_non_zero_prog_completions();

            // Attempt to recalculate the timestarted field.
            totara_program_fix_timestarted();
        }

        upgrade_plugin_savepoint(true, 2017030800, 'totara', 'core');
    }

    if ($oldversion < 2017040900) {
        // Remove private token column because all tokens were always supposed to be private.
        $table = new xmldb_table('external_tokens');
        $field = new xmldb_field('privatetoken', XMLDB_TYPE_CHAR, '64', null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        upgrade_plugin_savepoint(true, 2017040900, 'totara', 'core');
    }

    // Set default scheduled tasks correctly.
    if ($oldversion < 2017042801) {

        $task = '\totara_core\task\tool_totara_sync_task';
        // If schecdule is * 0 * * * change to 0 0 * * *
        $incorrectschedule = array(
            'minute' => '*',
            'hour' => '0',
            'day' => '*',
            'month' => '*',
            'dayofweek' => '*'
        );
        $newschedule = $incorrectschedule;
        $newschedule['minute'] = '0';

        totara_upgrade_default_schedule($task, $incorrectschedule, $newschedule);

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2017042801, 'totara', 'core');
    }

    // We removed the gauth plugin in Totara 10, 9.10, 2.9.22, 2.7.30, and 2.6.47.
    // The Google OpenID 2.0 API was deprecated May 2014, and shut down April 2015.
    // https://developers.google.com/identity/sign-in/auth-migration
    if ($oldversion < 2017072000) {

        if (file_exists($CFG->dirroot . '/auth/gauth/version.php')) {
            // This should not happen, this is not a standard distribution!
            // Nothing to do here.
        } else if (!get_config('auth_gauth', 'version')) {
            // Not installed. Weird but fine.
            // Nothing to do here.
        } else if ($DB->record_exists('user', array('auth' => 'gauth', 'deleted' => 0))) {
            // We need to remove the gauth plugin from the list of enabled plugins, if it has been enabled.
            $enabledauth = $DB->get_record('config', ['name' => 'auth'], '*', IGNORE_MISSING);
            if (!empty($enabledauth) && strpos($enabledauth->value, 'gauth')) {
                $auths = explode(',', $enabledauth->value);
                $auths = array_unique($auths);
                if (($key = array_search('gauth', $auths)) !== false) {
                    unset($auths[$key]);
                    set_config('auth', implode(',', $auths));
                }
            }
            // Note that if any users were created via gauth they won't have successfully logged in in the past 2 years.
            // Consequently we are going to leave their auth set to gauth.
            // They won't be able to log in, the admin will need to change their auth to manual.

            // Additionally all settings associated with the gauth plugin have been left in place just
            // in case anyone has fixed this plugin themselves, in which case they can put the files back
            // and simply re-enable the plugin after uprgade and everything will continue to work just fine.
        } else {
            // It is installed, and it is not used.
            // We can run the full uninstall_plugin for this, yay!
            uninstall_plugin('auth', 'gauth');
        }

        upgrade_plugin_savepoint(true, 2017072000, 'totara', 'core');
    }

    if ($oldversion < 2017082302) {

        // Define field totarasync to be added to job_assignment.
        $table = new xmldb_table('job_assignment');
        $field = new xmldb_field('totarasync', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'sortorder');

        // Conditionally launch add field totarasync.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // If we've just added this field, we'll be setting it to 1 for all job assignments
            // belonging to users who have the totarasync field on their user records set to 1.
            $ids = $DB->get_fieldset_select('user', 'id', 'totarasync = 1');
            $idsets = array_chunk($ids, $DB->get_max_in_params());
            foreach ($idsets as $idset) {
                list($insql, $inparams) = $DB->get_in_or_equal($idset);
                $DB->set_field_select('job_assignment', 'totarasync', 1, 'userid '. $insql, $inparams);
            }
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2017082302, 'totara', 'core');
    }

    if ($oldversion < 2017090600) {

        // Define field synctimemodified to be added to job_assignment.
        $table = new xmldb_table('job_assignment');
        $field = new xmldb_field('synctimemodified', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, '0', 'totarasync');

        // Conditionally launch add field synctimemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2017090600, 'totara', 'core');
    }

    if ($oldversion < 2017112700) {
        // Update the indexes on the course_info_data table.
        $table = new xmldb_table('course_info_data');

        // Define new index to be added.
        $index = new xmldb_index('courinfodata_cou_ix', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2017112700, 'totara', 'core');
    }

    if ($oldversion < 2017112701) {
        // Update the indexes on the course_info_data table.
        $table = new xmldb_table('course_info_data');

        // Define new index to be added.
        $index = new xmldb_index('courinfodata_fiecou_uix', XMLDB_INDEX_UNIQUE, array('fieldid', 'courseid'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2017112701, 'totara', 'core');
    }

    if ($oldversion < 2017122201) {
        // Enable registration, only wa to disable it is via config.php,
        // admins will be asked to select the site type during upgrade
        // and they will be briefed about the data sending to Totara server.
        set_config('registrationenabled', 1);

        upgrade_plugin_savepoint(true, 2017122201, 'totara', 'core');
    }

    if ($oldversion < 2018021300) {

        // Define table persistent_login to be created.
        $table = new xmldb_table('persistent_login');

        // Adding fields to table persistent_login.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cookie', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timeautologin', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('useragent', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('sid', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lastaccess', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('lastip', XMLDB_TYPE_CHAR, '45', null, null, null, null);

        // Adding keys to table persistent_login.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Adding indexes to table persistent_login.
        $table->add_index('cookie', XMLDB_INDEX_UNIQUE, array('cookie'));
        $table->add_index('sid', XMLDB_INDEX_UNIQUE, array('sid'));

        // Conditionally launch create table for persistent_login.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018021300, 'totara', 'core');
    }

    if ($oldversion < 2018030501) {
        totara_core_migrate_bogus_course_backup_areas();

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2018030501, 'totara', 'core');
    }

    if ($oldversion < 2018030502) {
        // Migrate renamed setting.
        set_config('backup_auto_shortname', get_config('backup', 'backup_shortname'), 'backup');
        set_config('backup_shortname', null, 'backup');

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2018030502, 'totara', 'core');
    }

    if ($oldversion < 2018030503) {

        // Define table backup_trusted_files to be created.
        $table = new xmldb_table('backup_trusted_files');

        // Adding fields to table backup_trusted_files.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contenthash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filesize', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('backupid', XMLDB_TYPE_CHAR, '32', null, null, null, null);
        $table->add_field('timeadded', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table backup_trusted_files.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Adding indexes to table backup_trusted_files.
        $table->add_index('contenthash', XMLDB_INDEX_UNIQUE, array('contenthash'));

        // Conditionally launch create table for backup_trusted_files.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018030503, 'totara', 'core');
    }


    if ($oldversion < 2018031501) {
        $deletedauths = array('fc', 'imap', 'nntp', 'none', 'pam', 'pop3');
        foreach ($deletedauths as $auth) {
            if ($DB->record_exists('user', array('auth' => $auth, 'deleted' => 0))) {
                // Keep the auth plugin settings,
                // admins will have to uninstall this manually.
                continue;
            }
            uninstall_plugin('auth', $auth);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018031501, 'totara', 'core');
    }

    if ($oldversion < 2018032600) {
        // Increase course fullname field to 1333 characters.
        $table = new xmldb_table('course');
        $field = new xmldb_field('fullname', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null);

        $dbman->change_field_precision($table, $field);

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018032600, 'totara', 'core');
    }

    if ($oldversion < 2018071000) {
        // Remove docroot setting if it matches previous default.
        if (get_config('core', 'docroot') == 'http://docs.moodle.org') {
            set_config('docroot', '');
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018071000, 'totara', 'core');
    }

    if ($oldversion < 2018082000) {
        // Moodle changed their default from http to https so we replace that as well
        if (get_config('core', 'docroot') == 'https://docs.moodle.org') {
            set_config('docroot', '');
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018082000, 'totara', 'core');
    }

    if ($oldversion < 2018082500) {
        // Moodle introduced the settings 'test_password' and 'test_serializer' for the redis cache store.
        // We set it to an empty string if the setting it not yet set
        if (get_config('cachestore_redis', 'test_password') === false) {
            set_config('test_password', '', 'cachestore_redis');
        }
        // We set it to the default php serializer if the setting it not yet set.
        if (get_config('cachestore_redis', 'test_serializer') === false) {
            set_config('test_serializer', 1, 'cachestore_redis');
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018082500, 'totara', 'core');
    }

    if ($oldversion < 2018091100) {
        // Update the indexes on the course_info_data table.
        $table = new xmldb_table('course_completion_criteria');

        // Define new index to be added.
        $index = new xmldb_index('moduleinstance', XMLDB_INDEX_NOTUNIQUE, array('moduleinstance'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018091100, 'totara', 'core');
    }

    if ($oldversion < 2018092100) {
        // Increase course_request fullname column to match the fullname column in the "course" table.
        $table = new xmldb_table('course_request');

        $field = new xmldb_field('fullname', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null);
        $dbman->change_field_precision($table, $field);

        upgrade_plugin_savepoint(true, 2018092100, 'totara', 'core');
    }

    if ($oldversion < 2018092101) {
        // Increase course_request shortname column to match the shortname column in the "course" table.
        $table = new xmldb_table('course_request');
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null);
        $index = new xmldb_index('shortname', XMLDB_INDEX_NOTUNIQUE, array('shortname'));

        // Conditionally launch drop index name to amend the field precision.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        // Change the field precision.
        $dbman->change_field_precision($table, $field);
        // Add back our 'shortname' index after the table has been amended.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2018092101, 'totara', 'core');
    }

    if ($oldversion < 2018100100) {
        // Upgrade the old frontpage block bits.
        totara_core_migrate_frontpage_display();

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018100100, 'totara', 'core');
    }

    if ($oldversion < 2018100101) {
        // Clean up the frontpage settings.
        unset_config('frontpage', 'core');
        unset_config('frontpageloggedin', 'core');
        unset_config('courseprogress', 'core');
        unset_config('maxcategorydepth', 'core');

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018100101, 'totara', 'core');
    }

    if ($oldversion < 2018102600) {
        // Define table quickaccess_preferences to be created.
        $table = new xmldb_table('quickaccess_preferences');

        // Adding fields to table quickaccess_preferences.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table quickaccess_preferences.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table quickaccess_preferences.
        $table->add_index('quickaccesspref_user_uix', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $table->add_index('quickaccesspref_usenam_uix', XMLDB_INDEX_UNIQUE, array('userid', 'name'));

        // Conditionally launch create table for quickaccess_preferences.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018102600, 'totara', 'core');
    }

    if ($oldversion < 2018111200) {
        // Clean up the old coursetagging setting
        unset_config('coursetagging', 'moodlecourse');

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018111200, 'totara', 'core');
    }

    if ($oldversion < 2018112201) {
        // Add 'course_navigation' block to all existing courses.
        totara_core_add_course_navigation();

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018112201, 'totara', 'core');
    }

    if ($oldversion < 2018112202) {
        // Add missing class names for custom main menu items.
        $DB->set_field_select('totara_navigation', 'classname', '\totara_core\totara\menu\item', "custom = 1 AND url <> ''");
        $DB->set_field_select('totara_navigation', 'classname', '\totara_core\totara\menu\container', "custom = 1 AND url = ''");

        // Switch to one show flag for both custom and default items.
        $DB->set_field('totara_navigation', 'visibility', '1', array('visibility' => '2'));

        // Migrate to new item for grid catalog, old mixed class is gone.
        $DB->delete_records('totara_navigation', array('classname' => '\totara_catalog\totara\menu\catalog'));

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018112202, 'totara', 'core');
    }

    if ($oldversion < 2018120701) {
        // Remove community block.
        uninstall_plugin('block', 'community');

        // Remove unused 'errorlog' table.
        $table = new xmldb_table('errorlog');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Remove deprecated 'registration_hubs' table.
        // We don't have Moodle registration code any more.
        $table = new xmldb_table('registration_hubs');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Clean up the deprecated settings.
        unset_config('uselegacybrowselistofusersreport', 'core');

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2018120701, 'totara', 'core');
    }

    if ($oldversion < 2019022201) {
        $duration = get_config('moodlecourse', 'courseduration');
        if ($duration !== false) {
            // adjust the default course duration.
            if ($duration <= 0) {
                // if it is 0, set it back to 365 days, the internal default duration.
                $duration = YEARSECS;
            } else if ($duration < HOURSECS) {
                // if it is less than an hour, set it to an hour.
                $duration = HOURSECS;
            }
            set_config('courseduration', $duration, 'moodlecourse');
        }
    }

    if ($oldversion < 2019030800) {
        if (get_config('moodlecourse', 'courseenddateenabled') === false) {
            set_config('courseenddateenabled', 1, 'moodlecourse');
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2019030800, 'totara', 'core');
    }

    if ($oldversion < 2019040900) {
        totara_core_upgrade_course_defaultimage_config();
        upgrade_plugin_savepoint(true, 2019040900, 'totara', 'core');
    }

    if ($oldversion < 2019040901) {
        totara_core_upgrade_course_images();
        upgrade_plugin_savepoint(true, 2019040901, 'totara', 'core');
    }

    if ($oldversion < 2019043001) {

        // Define index status (not unique) to be added to course_completions.
        $table = new xmldb_table('course_completions');
        $index = new xmldb_index('status', XMLDB_INDEX_NOTUNIQUE, array('status'));

        // Conditionally launch add index status.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2019043001, 'totara', 'core');
    }

    // This code moved here from lib/db/upgrade.php because it was excluded from
    // Totara 12 during the merge from Moodle 3.3.9. This code and comment should
    // be removed from here if a merge from a Moodle version higher than 3.6.4
    // were to occur, effectively moving this back into Moodle core upgrade.php
    if ($oldversion < 2019051700) {
        // Conditionally add field requireconfirmation to oauth2_issuer.
        $table = new xmldb_table('oauth2_issuer');
        $field = new xmldb_field('requireconfirmation', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'sortorder');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2019051700, 'totara', 'core');
    }

    if ($oldversion < 2019061300) {
        // Delete any orphaned course completions records that may exist as a result of course deletion race condition.
        $DB->execute('DELETE FROM {course_completions} WHERE course NOT IN (SELECT id FROM {course})');

        upgrade_plugin_savepoint(true, 2019061300, 'totara', 'core');
    }

    if ($oldversion < 2019061301) {

        // Define key userinfodata_fie_ix (foreign) to be added to user_info_data.
        $table = new xmldb_table('user_info_data');
        $key = new xmldb_key('userinfodata_fie_ix', XMLDB_KEY_FOREIGN, array('fieldid'), 'user_info_field', array('id'));

        // Launch add key userinfodata_fie_ix.
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2019061301, 'totara', 'core');
    }

    if ($oldversion < 2019061302) {

        // Define key userinfodata_use_ix (foreign) to be added to user_info_data.
        $table = new xmldb_table('user_info_data');
        $key = new xmldb_key('userinfodata_use_ix', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        // Launch add key userinfodata_use_ix.
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2019061302, 'totara', 'core');
    }

    if ($oldversion < 2019062100) {
        // Removing the duplicated tags, if there are any.
        totara_core_core_tag_upgrade_tags();

        upgrade_plugin_savepoint(true, 2019062100, 'totara', 'core');
    }

    if ($oldversion < 2019062101) {
        // Remove 'navigation' block instances from the system during upgrade to Totara 12.
        $blockids = $DB->get_fieldset_select('block_instances', 'id', 'blockname = ?', ['navigation']);
        if (!empty($blockids)) {
            foreach ($blockids as $bid) {
                context_helper::delete_instance(CONTEXT_BLOCK, $bid);
                $DB->delete_records('block_positions', ['blockinstanceid' => $bid]);
                $DB->delete_records('block_instances', ['id' => $bid]);
                $DB->delete_records_list('user_preferences', 'name', ['block' . $bid . 'hidden', 'docked_block_instance_' . $bid]);
            }
        }

        upgrade_plugin_savepoint(true, 2019062101, 'totara', 'core');
    }

    if ($oldversion < 2019070300) {
        // Remove Fusion option from gradebook exports.
        if (!empty($CFG->gradeexport)) {
            $expplugins = explode(',', $CFG->gradeexport);
            $expplugins = array_map('trim', $expplugins);
            foreach ($expplugins as $k => $v) {
                if ($v === 'fusion') {
                    unset($expplugins[$k]);
                }
            }
            set_config('gradeexport', implode(',', $expplugins));
        }
        upgrade_plugin_savepoint(true, 2019070300, 'totara', 'core');
    }

    if ($oldversion < 2019072900) {
        // Delete any un-created drag-and-drop SCORM modules (where instance = 0).
        $mod_scorm = $DB->get_record('modules', array('name' => 'scorm'), 'id');
        $DB->delete_records('course_modules', array('module' => $mod_scorm->id, 'instance' => 0));

        upgrade_plugin_savepoint(true, 2019072900, 'totara', 'core');
    }

    if ($oldversion < 2019073100) {
        // Fix staff manager role to be compatible with profile changes,
        // we want them to have access to all users details and allow them to access course profiles
        // of managed users even if they themselves are not enrolled in those courses.
        $systemcontext = \context_system::instance();
        $roles = $DB->get_records('role', ['archetype' => 'staffmanager']);
        foreach ($roles as $role) {
            assign_capability('moodle/user:viewalldetails', CAP_ALLOW, $role->id, $systemcontext, true);
            assign_capability('moodle/user:viewhiddendetails', CAP_ALLOW, $role->id, $systemcontext, true);
            assign_capability('moodle/site:viewfullnames', CAP_ALLOW, $role->id, $systemcontext, true);
            assign_capability('moodle/site:viewuseridentity', CAP_ALLOW, $role->id, $systemcontext, true);
        }
        upgrade_plugin_savepoint(true, 2019073100, 'totara', 'core');
    }

    if ($oldversion < 2019083002) {
        // Delete create_contexts_task and execute the context cleanup task once a day only.
        totara_upgrade_context_task_timing();
        upgrade_plugin_savepoint(true, 2019083002, 'totara', 'core');
    }

    if ($oldversion < 2019092400) {

        // Define index roleid-capability-permission (not unique) to be added to role_capabilities.
        $table = new xmldb_table('role_capabilities');
        $index = new xmldb_index('roleid-capability-permission', XMLDB_INDEX_NOTUNIQUE, array('roleid', 'capability', 'permission'));

        // Conditionally launch add index roleid-capability-permission.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2019092400, 'totara', 'core');
    }

    if ($oldversion < 2019092401) {

        // Define index audiencevisible (not unique) to be added to course.
        $table = new xmldb_table('course');
        $index = new xmldb_index('audiencevisible', XMLDB_INDEX_NOTUNIQUE, array('audiencevisible'));

        // Conditionally launch add index audiencevisible.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2019092401, 'totara', 'core');
    }

    if ($oldversion < 2019093000) {
        // Define index category-sortorder (not unique) to be added to course.
        $table = new xmldb_table('course');

        // First up drop the existing index on category.
        $index = new xmldb_index('category', XMLDB_INDEX_NOTUNIQUE, array('category'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // And create the new multi column index on category and sortorder.
        $index = new xmldb_index('category-sortorder', XMLDB_INDEX_NOTUNIQUE, array('category', 'sortorder'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2019093000, 'totara', 'core');
    }

    if ($oldversion < 2019100200) {
        // Define index flagtype-expiry-timemodified (not unique) to be added to cache_flags.
        $table = new xmldb_table('cache_flags');

        // First up drop the existing index on flagtype.
        $index = new xmldb_index('flagtype', XMLDB_INDEX_NOTUNIQUE, array('flagtype'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Add create the new multi column index on flagtype, expiry, and timemodified.
        $index = new xmldb_index('flagtype-expiry-timemodified', XMLDB_INDEX_NOTUNIQUE, array('flagtype', 'expiry', 'timemodified'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2019100200, 'totara', 'core');
    }

    if ($oldversion < 2019100300) {
        // Define index blockname (not unique) to be added to block_instances.
        $table = new xmldb_table('block_instances');
        $index = new xmldb_index('blockname', XMLDB_INDEX_NOTUNIQUE, array('blockname'));

        // Conditionally launch add index blockname.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2019100300, 'totara', 'core');
    }

    if ($oldversion < 2020012301) {
        // Define table oauth2_access_token to be created.
        $table = new xmldb_table('oauth2_access_token');

        // Adding fields to table oauth2_access_token.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('token', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('scope', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table oauth2_access_token.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('issueridkey', XMLDB_KEY_FOREIGN_UNIQUE, ['issuerid'], 'oauth2_issuer', ['id']);

        // Conditionally launch create table for oauth2_access_token.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2020012301, 'totara', 'core');
    }

    if ($oldversion < 2020012302) {
        // Define table role_sortorder to be dropped - this table is not used anywhere in code.
        $table = new xmldb_table('role_sortorder');

        // Conditionally launch drop table for role_sortorder.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2020012302, 'totara', 'core');
    }

    if ($oldversion < 2020021200) {
        // Initial introduction of RISK_ALLLOWXSS.
        totara_core_upgrade_fix_role_risks();

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2020021200, 'totara', 'core');
    }

    if ($oldversion < 2020021300) {
        // Fix problems from older upgrades.

        // Define key job_userid_fk (foreign) to be added to job_assignment.
        $table = new xmldb_table('job_assignment');
        $key = new xmldb_key('job_userid_fk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020021300, 'totara', 'core');
    }

    if ($oldversion < 2020021700) {
        // Define table totara_core_course_vis_map to be created.
        $table = new xmldb_table('totara_core_course_vis_map');

        // Adding fields to table totara_core_course_vis_map.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table totara_core_course_vis_map.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));

        // Adding indexes to table totara_core_course_vis_map.
        $table->add_index('courseid-roleid', XMLDB_INDEX_UNIQUE, array('courseid', 'roleid'));

        // Conditionally launch create table for totara_core_course_vis_map.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table totara_core_program_vis_map to be created.
        $table = new xmldb_table('totara_core_program_vis_map');

        // Adding fields to table totara_core_program_vis_map.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('programid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table totara_core_program_vis_map.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('programid', XMLDB_KEY_FOREIGN, array('programid'), 'course', array('id'));
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));

        // Adding indexes to table totara_core_program_vis_map.
        $table->add_index('programid-roleid', XMLDB_INDEX_UNIQUE, array('programid', 'roleid'));

        // Conditionally launch create table for totara_core_program_vis_map.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table totara_core_program_vis_map to be created.
        $table = new xmldb_table('totara_core_certification_vis_map');

        // Adding fields to table totara_core_program_vis_map.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('programid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table totara_core_program_vis_map.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('programid', XMLDB_KEY_FOREIGN, array('programid'), 'course', array('id'));
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));

        // Adding indexes to table totara_core_program_vis_map.
        $table->add_index('programid-roleid', XMLDB_INDEX_UNIQUE, array('programid', 'roleid'));

        // Conditionally launch create table for totara_core_program_vis_map.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020021700, 'totara', 'core');
    }

    if ($oldversion < 2020022701) {
        // Backport all table changes to the badges from Moodle 3.8.

        // Define fields to be added to the 'badge' table.
        $tablebadge = new xmldb_table('badge');
        $fieldversion = new xmldb_field('version', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'nextcron');
        $fieldlanguage = new xmldb_field('language', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'version');
        $fieldimageauthorname = new xmldb_field('imageauthorname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'language');
        $fieldimageauthoremail = new xmldb_field('imageauthoremail', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'imageauthorname');
        $fieldimageauthorurl = new xmldb_field('imageauthorurl', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'imageauthoremail');
        $fieldimagecaption = new xmldb_field('imagecaption', XMLDB_TYPE_TEXT, null, null, null, null, null, 'imageauthorurl');

        if (!$dbman->field_exists($tablebadge, $fieldversion)) {
            $dbman->add_field($tablebadge, $fieldversion);
        }
        if (!$dbman->field_exists($tablebadge, $fieldlanguage)) {
            $dbman->add_field($tablebadge, $fieldlanguage);
        }
        if (!$dbman->field_exists($tablebadge, $fieldimageauthorname)) {
            $dbman->add_field($tablebadge, $fieldimageauthorname);
        }
        if (!$dbman->field_exists($tablebadge, $fieldimageauthoremail)) {
            $dbman->add_field($tablebadge, $fieldimageauthoremail);
        }
        if (!$dbman->field_exists($tablebadge, $fieldimageauthorurl)) {
            $dbman->add_field($tablebadge, $fieldimageauthorurl);
        }
        if (!$dbman->field_exists($tablebadge, $fieldimagecaption)) {
            $dbman->add_field($tablebadge, $fieldimagecaption);
        }

        // Define table badge_endorsement to be created.
        $table = new xmldb_table('badge_endorsement');

        // Adding fields to table badge_endorsement.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('issuername', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issueremail', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('claimid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('claimcomment', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('dateissued', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table badge_endorsement.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('endorsementbadge', XMLDB_KEY_FOREIGN, ['badgeid'], 'badge', ['id']);

        // Conditionally launch create table for badge_endorsement.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table badge_related to be created.
        $table = new xmldb_table('badge_related');

        // Adding fields to table badge_related.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badgeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('relatedbadgeid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table badge_related.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('badgeid', XMLDB_KEY_FOREIGN, ['badgeid'], 'badge', ['id']);
        $table->add_key('relatedbadgeid', XMLDB_KEY_FOREIGN, ['relatedbadgeid'], 'badge', ['id']);
        $table->add_key('badgeid-relatedbadgeid', XMLDB_KEY_UNIQUE, ['badgeid', 'relatedbadgeid']);

        // Conditionally launch create table for badge_related.
                if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table badge_external_backpack to be created.
        $table = new xmldb_table('badge_external_backpack');

        // Adding fields to table badge_external_backpack.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('backpackapiurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('backpackweburl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('apiversion', XMLDB_TYPE_CHAR, '12', null, XMLDB_NOTNULL, null, '1.0');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('password', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table badge_external_backpack.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('backpackapiurlkey', XMLDB_KEY_UNIQUE, ['backpackapiurl']);
        $table->add_key('backpackweburlkey', XMLDB_KEY_UNIQUE, ['backpackweburl']);

        // Conditionally launch create table for badge_external_backpack.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field entityid to be added to badge_external.
        $table = new xmldb_table('badge_external');
        $field = new xmldb_field('entityid', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'collectionid');

        // Conditionally launch add field entityid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table badge_external_identifier to be created.
        $table = new xmldb_table('badge_external_identifier');

        // Adding fields to table badge_external_identifier.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sitebackpackid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('internalid', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('externalid', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table badge_external_identifier.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_backpackid', XMLDB_KEY_FOREIGN, ['sitebackpackid'], 'badge_backpack', ['id']);
        $table->add_key('backpack-internal-external', XMLDB_KEY_UNIQUE, ['sitebackpackid', 'internalid', 'externalid', 'type']);

        // Conditionally launch create table for badge_external_identifier.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field externalbackpackid to be added to badge_backpack.
        $table = new xmldb_table('badge_backpack');
        $field = new xmldb_field('externalbackpackid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'password');

        // Conditionally launch add field externalbackpackid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define key externalbackpack (foreign) to be added to badge_backpack.
        $key = new xmldb_key('externalbackpack', XMLDB_KEY_FOREIGN, ['externalbackpackid'], 'badge_external_backpack', ['id']);

        // Launch add key externalbackpack.
        $dbman->add_key($table, $key);

        $field = new xmldb_field('backpackurl');

        // Conditionally launch drop field backpackurl.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Add default backpacks.
        require_once($CFG->dirroot . '/badges/upgradelib.php'); // Core install and upgrade related functions only for badges.
        badges_install_default_backpacks();

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2020022701, 'totara', 'core');
    }

    if ($oldversion < 2020031200) {
        global $DB;

        $instancecount = $DB->count_records('block_instances', array('blockname' => 'mentees'));

        if ($instancecount <= 0) {
            $DB->set_field('block', 'visible', 0, ['name' => 'mentees']);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020031200, 'totara', 'core');
    }

    if ($oldversion < 2020031700) {
        $recordcount = $DB->count_records('survey');

        if ($recordcount <= 0) {
            $DB->set_field('modules', 'visible', '0', ['name' => 'survey']);
        }

        upgrade_plugin_savepoint(true, 2020031700, 'totara', 'core');
    }

    if ($oldversion < 2020032000) {
        totara_core_upgrade_migrate_removed_user_fields();
        upgrade_plugin_savepoint(true, 2020032000, 'totara', 'core');
    }

    if ($oldversion < 2020032100) {
        totara_core_upgrade_delete_moodle_plugins();
        upgrade_plugin_savepoint(true, 2020032100, 'totara', 'core');
    }

    if ($oldversion < 2020042902) {
        // Delete log records that may contain sensitive admin setting values.
        $DB->delete_records('logstore_standard_log', ['eventname' => '\core\event\admin_settings_changed']);

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2020042902, 'totara', 'core');
    }

    if ($oldversion < 2020050100) {
        // Remove deprecated Mozilla OpenBadges backpack.
        $url = 'https://backpack.openbadges.org';
        $bp = $DB->get_record('badge_external_backpack', ['backpackapiurl' => $url]);
        if ($bp) {
            // Remove connections for users to this backpack.
            $sql = "SELECT DISTINCT bb.id
                      FROM {badge_backpack} bb
                 LEFT JOIN {badge_external} be ON be. backpackid = bb.externalbackpackid
                     WHERE bb.externalbackpackid = :backpackid";
            $params = ['backpackid' => $bp->id];
            $externalbackpacks = $DB->get_fieldset_sql($sql, $params);
            if ($externalbackpacks) {
                list($sql, $params) = $DB->get_in_or_equal($externalbackpacks);

                // Delete user external collections references to this backpack.
                $DB->execute("DELETE FROM {badge_external} WHERE backpackid " . $sql, $params);
            }
            $DB->delete_records('badge_backpack', ['externalbackpackid' => $bp->id]);

            // Delete deprecated backpack entry.
            $DB->delete_records('badge_external_backpack', ['backpackapiurl' => $url]);
        }

        // Set active external backpack to Badgr.io.
        $url = 'https://api.badgr.io/v2';
        if ($bp = $DB->get_record('badge_external_backpack', ['backpackapiurl' => $url])) {
            set_config('badges_site_backpack', $bp->id);
        } else {
            unset_config('badges_site_backpack');
        }

        upgrade_plugin_savepoint(true, 2020050100, 'totara', 'core');
    }

    if ($oldversion < 2020060200) {
        // Define table totara_core_relationship to be created.
        $table = new xmldb_table('totara_core_relationship');

        // Adding fields to table totara_core_relationship.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');

        // Adding keys to table totara_core_relationship.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for totara_core_relationship.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table totara_core_relationship_resolver to be created.
        $table = new xmldb_table('totara_core_relationship_resolver');

        // Adding fields to table totara_core_relationship_resolver.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('relationship_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('class_name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table totara_core_relationship_resolver.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('relationship_id', XMLDB_KEY_FOREIGN, array('relationship_id'), 'totara_core_relationship', array('id'), 'cascade');

        // Adding indexes to table totara_core_relationship_resolver.
        $table->add_index('relationship_id_class_name', XMLDB_INDEX_UNIQUE, array('relationship_id', 'class_name'));

        // Conditionally launch create table for totara_core_relationship_resolver.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        totara_core_upgrade_create_relationship('totara_core\relationship\resolvers\subject');

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020060200, 'totara', 'core');
    }

    if ($oldversion < 2020062601) {
        // Define field containertype to be added to course.
        $table = new xmldb_table('course');
        $field = new xmldb_field('containertype', XMLDB_TYPE_CHAR, '255', null, false, null, null, 'icon');

        // Conditionally launch add field containertype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2020062601, 'totara', 'core');
    }

    if ($oldversion < 2020062602) {
        // Define field type to be added to course_categories.
        $table = new xmldb_table('course_categories');
        $field = new xmldb_field('issystem', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'certifcount');

        // Conditionally launch add field type.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2020062602, 'totara', 'core');
    }

    if ($oldversion < 2020071700) {
        // Define table badge_backpack_oauth2 to be created.
        $table = new xmldb_table('badge_backpack_oauth2');

        // Adding fields to table badge_backpack_oauth2.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('issuerid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('externalbackpackid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('token', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('refreshtoken', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('expires', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('scope', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table badge_backpack_oauth2.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('issuerid', XMLDB_KEY_FOREIGN, ['issuerid'], 'oauth2_issuer', ['id']);
        $table->add_key('externalbackpackid', XMLDB_KEY_FOREIGN, ['externalbackpackid'], 'badge_external_backpack', ['id']);
        // Conditionally launch create table for badge_backpack_oauth2.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field oauth2_issuerid to be added to badge_external_backpack.
        $tablebadgeexternalbackpack = new xmldb_table('badge_external_backpack');
        $fieldoauth2issuerid = new xmldb_field('oauth2_issuerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'password');
        $keybackpackoauth2key = new xmldb_key('backpackoauth2key', XMLDB_KEY_FOREIGN, ['oauth2_issuerid'], 'oauth2_issuer', ['id']);

        // Conditionally launch add field oauth2_issuerid.
        if (!$dbman->field_exists($tablebadgeexternalbackpack, $fieldoauth2issuerid)) {
            $dbman->add_field($tablebadgeexternalbackpack, $fieldoauth2issuerid);

            // Launch add key backpackoauth2key.
            $dbman->add_key($tablebadgeexternalbackpack, $keybackpackoauth2key);
        }

        // Define field assertion to be added to badge_external.
        $tablebadgeexternal = new xmldb_table('badge_external');
        $fieldassertion = new xmldb_field('assertion', XMLDB_TYPE_TEXT, null, null, null, null, null, 'entityid');

        // Conditionally launch add field assertion.
        if (!$dbman->field_exists($tablebadgeexternal, $fieldassertion)) {
            $dbman->add_field($tablebadgeexternal, $fieldassertion);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020071700, 'totara', 'core');
    }

    if ($oldversion < 2020072700) {
        $totara_themes = ['basis', 'roots'];

        // Set new Ventura theme. Only if default theme is a Totara one.
        if (in_array($CFG->theme, $totara_themes)) {
            // Load ventura theme.
            $theme = theme_config::load('ventura');

            // Get the config argument for the default device.
            $themename = core_useragent::get_device_type_cfg_var_name(core_useragent::DEVICETYPE_DEFAULT);

            // Set the theme in config.
            set_config($themename, $theme->name);
        }

        upgrade_plugin_savepoint(true, 2020072700, 'totara', 'core');
    }

    if ($oldversion < 2020072704) {

        // Define field type to be added to totara_core_relationship.
        $table = new xmldb_table('totara_core_relationship');

        // Conditionally add field type.
        $field = new xmldb_field('type', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'created_at');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally add field component.
        $field = new xmldb_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'type');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally add field sort_order.
        $field = new xmldb_field('sort_order', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1', 'created_at');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditionally add field idnumber.
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Update existing relationships with idnumber
        // Only subject, appraiser & manager should exist at this point and have 1 resolver each.
        $existing_resolvers = $DB->get_records('totara_core_relationship_resolver');

        foreach ($existing_resolvers as $existing_resolver) {
            $data = explode('\\', $existing_resolver->class_name);
            $idnumber = end($data);
            $relationship_data = [
                'id' => $existing_resolver->relationship_id,
                'idnumber' => $idnumber
            ];
            $DB->update_record('totara_core_relationship', $relationship_data);
        }

        // Conditionally add index sort_order.
        $index = new xmldb_index('sort_order', XMLDB_INDEX_NOTUNIQUE, array('sort_order'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Conditionally add index idnumber.
        $index = new xmldb_index('idnumber', XMLDB_INDEX_UNIQUE, array('idnumber'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        totara_core_upgrade_create_relationship(['totara_core\relationship\resolvers\subject'], 'subject', 1);

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2020072704, 'totara', 'core');
    }

    return true;
}
