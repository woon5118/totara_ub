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
 * @package totara
 * @subpackage totara_core
 */

function xmldb_totara_core_install() {
    global $CFG, $DB, $SITE;
    require_once(__DIR__ . '/upgradelib.php');

    // switch to new default theme in totara 9.0
    set_config('theme', 'basis');

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
    $systemcontext = context_system::instance();
    // add coursetype and icon fields to course table

    $table = new xmldb_table('course');

    $field = new xmldb_field('coursetype');
    if (!$dbman->field_exists($table, $field)) {
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', null);
        $dbman->add_field($table, $field);
    }

    $field = new xmldb_field('icon');
    if (!$dbman->field_exists($table, $field)) {
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $dbman->add_field($table, $field);
    }

    // Create totara roles.
    $staffmanagerrole    = create_role('', 'staffmanager', '', 'staffmanager');
    // Following roles are not created any more since Totara 8.0 - assessor, regionalmanager, regionaltrainer.

    $newroles = array($staffmanagerrole);

    foreach ($DB->get_records('role') as $role) {
        // Add allow* defaults related to all new roles.
        foreach (array('assign', 'override', 'switch') as $type) {
            $function = 'allow_'.$type;
            $allows = get_default_role_archetype_allows($type, $role->archetype);
            foreach ($allows as $allowid) {
                if (!in_array($allowid, $newroles) and !in_array($role->id, $newroles)) {
                    // Add only entries related to new roles!
                    continue;
                }
                $function($role->id, $allowid);
            }
        }

        if (in_array($role->id, $newroles)) {
            // Set context levels for all new roles.
            set_role_contextlevels($role->id, get_default_contextlevels($role->shortname));

            // Reset existing permissions for all new roles.
            $defaultcaps = get_default_capabilities($role->archetype);
            foreach($defaultcaps as $cap => $permission) {
                assign_capability($cap, $permission, $role->id, $systemcontext->id);
            }
        }
    }

    // Reset legacy custom roles names for standard roles,
    // we want to use the lang strings from now on.
    $resetnames = array('manager', 'coursecreator', 'editingteacher', 'teacher', 'student', 'guest', 'user');
    foreach ($resetnames as $shortname) {
        if ($old_role = $DB->get_record('role', array('shortname' => $shortname))) {
            $new_role = new stdClass();
            $new_role->id = $old_role->id;
            $new_role->name = '';
            $new_role->description = '';

            $DB->update_record('role', $new_role);
        }
    }

    totara_core_upgrade_fix_role_risks();

    $systemcontext->mark_dirty();

    // Turn completion on in Totara when upgrading from Moodle.
    set_config('enablecompletion', 1);
    set_config('enablecompletion', 1, 'moodlecourse');
    set_config('completionstartonenrol', 1, 'moodlecourse');

    // Add completionstartonenrol column to course table.
    $table = new xmldb_table('course');

    // Define field completionstartonenrol to be added to course.
    $field = new xmldb_field('completionstartonenrol', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

    // Conditionally launch add field completionstartonenrol.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Add RPL column to course_completions table
    $table = new xmldb_table('course_completions');

    // Define field rpl to be added to course_completions
    $field = new xmldb_field('rpl', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'reaggregate');

    // Conditionally launch add field rpl
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field rplgrade to be added to course_completions
    $field = new xmldb_field('rplgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, 'rpl');

    // Conditionally launch add field rpl
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Add RPL column to course_completion_crit_compl table
    $table = new xmldb_table('course_completion_crit_compl');

    // Define field rpl to be added to course_completion_crit_compl
    $field = new xmldb_field('rpl', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'unenroled');

    // Conditionally launch add field rpl
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define fields status and renewalstatus to be added to course_completions.
    $table = new xmldb_table('course_completions');
    $field = new xmldb_field('status', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');

    // Conditionally launch add field status.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    $index = new xmldb_index('status', XMLDB_INDEX_NOTUNIQUE, array('status'));

    // Conditionally launch add index status.
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    $field = new xmldb_field('renewalstatus', XMLDB_TYPE_INTEGER, '2', null, null, null, '0');

    // Conditionally launch add field renewalstatus.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Add extra foreign keys from TL-15995
    $table = new xmldb_table('user_info_data');
    $key = new xmldb_key('userinfodata_fie_ix', XMLDB_KEY_FOREIGN, array('fieldid'), 'user_info_field', array('id'));
    if (!$dbman->key_exists($table, $key)) {
        $dbman->add_key($table, $key);
    }
    $key = new xmldb_key('userinfodata_use_ix', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
    if (!$dbman->key_exists($table, $key)) {
        $dbman->add_key($table, $key);
    }

    rebuild_course_cache($SITE->id, true);

    // readd totara specific course completion changes for anyone
    // upgrading from moodle 2.2.2+
    require_once($CFG->dirroot . '/totara/core/db/utils.php');
    totara_readd_course_completion_changes();

    // remove any references to "complete on unenrolment" critiera type
    // these could exist in an upgrade from moodle 2.2 but the criteria
    // was never implemented and is no longer in totara
    $DB->delete_records('course_completion_criteria', array('criteriatype' => 3));

    // Disable editing execpaths by default for security.
    set_config('preventexecpath', '1');
    // Then provide default values to prevent them appearing on the upgradesettings page.
    set_config('geoipfile', $CFG->dataroot . 'geoip/GeoLiteCity.dat');
    set_config('location', '', 'enrol_flatfile');
    set_config('filter_tex_pathlatex', '/usr/bin/latex');
    set_config('filter_tex_pathdvips', '/usr/bin/dvips');
    set_config('filter_tex_pathconvert', '/usr/bin/convert');
    set_config('pathtodu', '');
    set_config('pathtoclam', '');
    set_config('aspellpath', '');
    set_config('pathtodot', '');
    set_config('quarantinedir', '');
    set_config('backup_auto_destination', '', 'backup');
    set_config('gspath', '/usr/bin/gs', 'assignfeedback_editpdf');
    set_config('exporttofilesystempath', '', 'reportbuilder');
    set_config('pathlatex', '/usr/bin/latex', 'filter_tex');
    set_config('pathdvips', '/usr/bin/dvips', 'filter_tex');
    set_config('pathconvert', '/usr/bin/convert', 'filter_tex');
    set_config('pathmimetex', '', 'filter_tex');

    // Alter Moodle tables during migration to Totara.

    // Add extra 'user' table fields for totara sync.
    $table = new xmldb_table('user');
    $field = new xmldb_field('totarasync');
    $field->set_attributes(XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', null);
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }
    $index = new xmldb_index('totarasync');
    $index->set_attributes(XMLDB_INDEX_NOTUNIQUE, array('totarasync'));
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    // Define field completionprogressonview to be added to course.
    $table = new xmldb_table('course');
    $field = new xmldb_field('completionprogressonview', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0, 'enablecompletion');

    // Conditionally launch add field completionprogressonview.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    $field = new xmldb_field('audiencevisible', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, 2);

    // Conditionally launch add field audiencevisible to course table.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field invalidatecache to be added to course_completions.
    $table = new xmldb_table('course_completions');
    $field = new xmldb_field('invalidatecache', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');

    // Conditionally launch add field invalidatecache.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Add timecompleted for module completion.
    $table = new xmldb_table('course_modules_completion');
    $field = new xmldb_field('timecompleted', XMLDB_TYPE_INTEGER, '10');

    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Define field reaggregate to be added to course_modules_completion.
    $table = new xmldb_table('course_modules_completion');
    $field = new xmldb_field('reaggregate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecompleted');

    // Conditionally launch add field reaggregate.
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // Make sure we run the MSSQL fixes when upgrading from Moodle.
    if (!during_initial_install()) {
        totara_core_fix_old_upgraded_mssql();
    }

    // Remove private token column because all tokens were always supposed to be private.
    $table = new xmldb_table('external_tokens');
    $field = new xmldb_field('privatetoken', XMLDB_TYPE_CHAR, '64', null, null, null, null);
    if ($dbman->field_exists($table, $field)) {
        $dbman->drop_field($table, $field);
    }

    totara_core_upgrade_delete_moodle_plugins();

    // Remove settings for deleted Totara features.
    unset_config('allowedemaildomains');

    // Tweak backup/restore stuff.
    totara_core_migrate_bogus_course_backup_areas();
    set_config('backup_auto_shortname', get_config('backup', 'backup_shortname'), 'backup');
    set_config('backup_shortname', null, 'backup');

    // Increase course fullname field to 1333 characters.
    $table = new xmldb_table('course');
    $field = new xmldb_field('fullname', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null);
    $dbman->change_field_precision($table, $field);

    // Disable linking of admin categories introduced in new Moodle admin interface.
    if (get_config('linkadmincategories')) {
        set_config('linkadmincategories', 0);
    }

    // Increase course_request fullname column to match the fullname column in the "course" table.
    $table = new xmldb_table('course_request');
    $field = new xmldb_field('fullname', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null);
    $dbman->change_field_precision($table, $field);

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

    // Upgrade the old frontpage block bits when upgrading from Moodle.
    totara_core_migrate_frontpage_display();

    // Add course navigation blocks when upgrading from Moodle.
    totara_core_add_course_navigation();

    // Removing deprecated table post Totara 12 release.
    // We don't have Moodle registration code any more.
    $table = new xmldb_table('registration_hubs');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    // This code moved here from lib/db/upgrade.php because it was excluded from
    // Totara 12 during the merge from Moodle 3.3.9. This code and comment should
    // be removed from here if a merge from a Moodle version higher than 3.6.4
    // were to occur, effectively moving this back into Moodle core upgrade.php

    // Conditionally add field requireconfirmation to oauth2_issuer.
    $table = new xmldb_table('oauth2_issuer');
    $field = new xmldb_field('requireconfirmation', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'sortorder');
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field);
    }

    // When upgrading from Moodle change execution of the context cleanup task to once a day only by default.
    totara_upgrade_context_task_timing();

    // Add indexes that benefit has_capapability_sql on role_capabilities
    $table = new xmldb_table('role_capabilities');
    $index = new xmldb_index('roleid-capability-permission', XMLDB_INDEX_NOTUNIQUE, array('roleid', 'capability', 'permission'));
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    // Add indexes that benefit has_capapability_sql on course
    $table = new xmldb_table('course');
    $index = new xmldb_index('audiencevisible', XMLDB_INDEX_NOTUNIQUE, array('audiencevisible'));
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    // Add indexes that benefit category management pages
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

    // Add indexes that benefit all pages
    $table = new xmldb_table('cache_flags');
    // First up drop the existing index on flagtype.
    $index = new xmldb_index('flagtype', XMLDB_INDEX_NOTUNIQUE, array('flagtype'));
    if ($dbman->index_exists($table, $index)) {
        $dbman->drop_index($table, $index);
    }
    // And create the new multi column index on flagtype, expiry, and timemodified.
    $index = new xmldb_index('flagtype-expiry-timemodified', XMLDB_INDEX_NOTUNIQUE, array('flagtype', 'expiry', 'timemodified'));
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

    // Add index that benefit all pages.
    $table = new xmldb_table('block_instances');
    $index = new xmldb_index('blockname', XMLDB_INDEX_NOTUNIQUE, array('blockname'));
    if (!$dbman->index_exists($table, $index)) {
        $dbman->add_index($table, $index);
    }

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

    // Define table role_sortorder to be dropped - this table is not used anywhere in code.
    $table = new xmldb_table('role_sortorder');
    // Conditionally launch drop table for role_sortorder.
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

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

    // Migrate obsolete user fields.
    totara_core_upgrade_migrate_removed_user_fields();

    return true;
}
