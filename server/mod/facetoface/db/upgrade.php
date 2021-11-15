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

require_once($CFG->dirroot.'/mod/facetoface/db/upgradelib.php');

// This file keeps track of upgrades to
// the facetoface module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php

/**
 * Local database upgrade script
 *
 * @param   int $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean always true
 */
function xmldb_facetoface_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Totara 10 branching line.

    if ($oldversion < 2016110200) {

        // Remove seminar notifications for removed seminars.
        // Regression T-14050.
        $sql = "DELETE FROM {facetoface_notification} WHERE facetofaceid NOT IN (SELECT id FROM {facetoface})";
        $DB->execute($sql);

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2016110200, 'facetoface');
    }

    if ($oldversion < 2016110900) {

        $table = new xmldb_table('facetoface_notification_tpl');
        $field = new xmldb_field('ccmanager', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'body');
        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $templates = $DB->get_records('facetoface_notification_tpl', null, '', 'id, reference');
        $transaction = $DB->start_delegated_transaction();
        $references = array('confirmation', 'cancellation', 'reminder', 'request', 'adminrequest', 'registrationclosure');
        foreach ($templates as $template) {
            $todb = new stdClass();
            $todb->id = $template->id;
            $todb->ccmanager = (in_array($template->reference, $references) ? 1 : 0);
            $DB->update_record('facetoface_notification_tpl', $todb);
        }
        $transaction->allow_commit();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2016110900, 'facetoface');
    }

    if ($oldversion < 2016122101) {
        // Adding "Below is the message that was sent to learner:" to the end of prefix text for existing notifications.
        // This will upgrade only non-changed text in comparison to original v9 manager prefix.
        facetoface_upgradelib_managerprefix_clarification();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2016122101, 'facetoface');
    }

    if ($oldversion < 2017052200) {
        // Updating registrationtimestart and registrationtimefinish with null values to 0.
        $sql = 'UPDATE {facetoface_sessions}
                SET registrationtimestart = 0
                WHERE registrationtimestart IS NULL';
        $DB->execute($sql);

        $sql = 'UPDATE {facetoface_sessions}
                SET registrationtimefinish = 0
                WHERE registrationtimefinish IS NULL';
        $DB->execute($sql);

        // Changing the default of field registrationtimestart on table facetoface_sessions to 0.
        $table = new xmldb_table('facetoface_sessions');
        $field = new xmldb_field('registrationtimestart', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'sendcapacityemail');

        // Launch change of default for field registrationtimestart.
        $dbman->change_field_default($table, $field);
        $dbman->change_field_notnull($table, $field);

        // Changing the default of field registrationtimefinish on table facetoface_sessions to 0.
        $field = new xmldb_field('registrationtimefinish', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'registrationtimestart');

        // Launch change of default for field registrationtimefinish.
        $dbman->change_field_default($table, $field);
        $dbman->change_field_notnull($table, $field);

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2017052200, 'facetoface');
    }

    if ($oldversion < 2017062900) {
        $table = new xmldb_table('facetoface_sessions_dates');

        // Adding unique indexes to timestart and timefinish. It is required as reports during event grouping rely
        // on timestart and timefinish to get their timezone.
        $index = new xmldb_index('facesessdate_sessta_ix', XMLDB_INDEX_UNIQUE, array('sessionid', 'timestart'));
        // Conditionally launch add index sessionid, timestart
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('facesessdate_sesfin_ix', XMLDB_INDEX_UNIQUE, array('sessionid', 'timefinish'));
        // Conditionally launch add index sessionid, timefinish
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2017062900, 'facetoface');
    }

    if ($oldversion < 2017092501) {
        // Define index mailed (not unique) to be dropped form assign_grades.
        $table = new xmldb_table('facetoface_notification_tpl');
        $index = new xmldb_index('title', XMLDB_INDEX_UNIQUE, array('title'));

        // Conditionally launch drop unique index title.
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2017092501, 'facetoface');
    }

    if ($oldversion < 2017103100) {
        facetoface_upgradelib_calendar_events_for_sessiondates();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2017103100, 'facetoface');
    }

    if ($oldversion < 2017112000) {
        // Update the indexes on the facetoface_asset_info_data table.
        $table = new xmldb_table('facetoface_asset_info_data');

        // Define new index to be added.
        $index = new xmldb_index('faceasseinfodata_fiefac_uix', XMLDB_INDEX_UNIQUE, array('fieldid', 'facetofaceassetid'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2017112000, 'facetoface');
    }

    if ($oldversion < 2017112001) {
        // Update the indexes on the facetoface_cancellation_info_data table.
        $table = new xmldb_table('facetoface_cancellation_info_data');

        // Define new index to be added.
        $index = new xmldb_index('faceasseinfodata_fiefac_uix', XMLDB_INDEX_UNIQUE, array('fieldid', 'facetofacecancellationid'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2017112001, 'facetoface');
    }

    if ($oldversion < 2017112002) {
        // Update the indexes on the facetoface_room_info_data table.
        $table = new xmldb_table('facetoface_room_info_data');

        // Define new index to be added.
        $index = new xmldb_index('faceroominfodata_fiefac_uix', XMLDB_INDEX_UNIQUE, array('fieldid', 'facetofaceroomid'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2017112002, 'facetoface');
    }

    if ($oldversion < 2017112003) {
        // Update the indexes on the facetoface_session_info_data table.
        $table = new xmldb_table('facetoface_session_info_data');

        // Define new index to be added.
        $index = new xmldb_index('facesessinfodata_fiefac_uix', XMLDB_INDEX_UNIQUE, array('fieldid', 'facetofacesessionid'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2017112003, 'facetoface');
    }

    if ($oldversion < 2017112004) {
        // Update the indexes on the facetoface_sessioncancel_info_data table.
        $table = new xmldb_table('facetoface_sessioncancel_info_data');

        // Define new index to be added.
        $index = new xmldb_index('facesecainfodata_fiefac_uix', XMLDB_INDEX_UNIQUE, array('fieldid', 'facetofacesessioncancelid'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2017112004, 'facetoface');
    }

    if ($oldversion < 2017112005) {
        // Update the indexes on the facetoface_signup_info_data table.
        $table = new xmldb_table('facetoface_signup_info_data');

        // Define new index to be added.
        $index = new xmldb_index('facesigninfodata_fiefac_uix', XMLDB_INDEX_UNIQUE, array('fieldid', 'facetofacesignupid'));
        // Conditionally launch to add index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_mod_savepoint(true, 2017112005, 'facetoface');
    }

    if ($oldversion < 2018022600) {
        // Remove invalid plugin version introduced by wrong upgrade steps in TL-15995.
        set_config('version', null, 'totara_facetoface');
        upgrade_mod_savepoint(true, 2018022600, 'facetoface');
    }

    if ($oldversion < 2018101900) {

        $table = new xmldb_table('facetoface_notification_tpl');
        $field = new xmldb_field('title', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_type($table, $field);

        $table = new xmldb_table('facetoface_notification');
        $index = new xmldb_index('title', XMLDB_INDEX_NOTUNIQUE, array('title'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $field = new xmldb_field('title', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $dbman->change_field_precision($table, $field);

        upgrade_mod_savepoint(true, 2018101900, 'facetoface');
    }

    // Multiple signups upgrade part 1 of 3.
    if ($oldversion < 2018102700) {

        // First set the activity default settings to maintain previous behaviour.
        $oldmulti = (bool) get_config(null, 'facetoface_multiplesessions');
        set_config('facetoface_multisignup_enable', $oldmulti);

        $restrictions = $oldmulti ? '' : 'multisignuprestrict_partially,multisignuprestrict_noshow';
        set_config('facetoface_multisignup_restrict', $restrictions);

        $maximum = $oldmulti ? 0 : 2;
        set_config('facetoface_multisignup_maximum', $maximum);

        set_config('facetoface_waitlistautoclean', 0); // Disable to maintain previous behaviour.

        // Then Create the columns for the restrictions on multiple signups.
        $table = new xmldb_table('facetoface');
        $fields = [];

        // multisignupfully - only fully attended users can signup for another event
        $fields[] = new xmldb_field('multisignupfully', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // multisignuppartly - only partially attended users can signup for another event
        $fields[] = new xmldb_field('multisignuppartly', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // multisignupnoshow - only users marked as no shows can signup for another event
        $fields[] = new xmldb_field('multisignupnoshow', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // multisignupmaximum - the maximum amount of event a user can signup for.
        $fields[] = new xmldb_field('multisignupmaximum', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // waitlistautoclean - Whether to clean the waitlist for an event after it has begun.
        $fields[] = new xmldb_field('waitlistautoclean', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        upgrade_mod_savepoint(true, 2018102700, 'facetoface');
    }

    // Multiple signups upgrade part 2 of 3.
    if ($oldversion < 2018102800) {
        // Create the default notification template for waitlistautoclean.
        $title = get_string('setting:defaultwaitlistautocleansubjectdefault', 'facetoface');
        if (\core_text::strlen($title) > 255) {
            $title = \core_text::substr($title, 0, 255);
        }

        $body = text_to_html(get_string('setting:defaultwaitlistautocleanmessagedefault', 'facetoface'));

        if (!$DB->record_exists('facetoface_notification_tpl', ['reference' => 'waitlistautoclean'])) {
            $tpl_waitlistautoclean = new stdClass();
            $tpl_waitlistautoclean->status = 1;
            $tpl_waitlistautoclean->reference = 'waitlistautoclean';
            $tpl_waitlistautoclean->title = $title;
            $tpl_waitlistautoclean->body = $body;
            $tpl_waitlistautoclean->ccmanager = 0;
            $templateid = $DB->insert_record('facetoface_notification_tpl', $tpl_waitlistautoclean);
        } else {
            $templateid = $DB->get_field('facetoface_notification_tpl', 'id', ['reference' => 'waitlistautoclean']);
        }

        // Now add the new template to existing seminars.
        // NOTE: We don't normally want to do this, but it's safe to do
        //       here since it's disabled by default and they wont send
        //       unless someone turns on the setting.
        $conditiontype = 524288; // Constant MDL_F2F_CONDITION_WAITLIST_AUTOCLEAN.
        $sql = 'SELECT f.*
                  FROM {facetoface} f
             LEFT JOIN {facetoface_notification} fn
                    ON fn.facetofaceid = f.id
                   AND fn.conditiontype = :ctype
             WHERE fn.id IS NULL';
        $f2fs = $DB->get_records_sql($sql, ['ctype' => $conditiontype]);

        $data = new stdClass();
        $data->type = 4; // MDL_F2F_NOTIFICATION_AUTO.
        $data->conditiontype = $conditiontype;
        $data->booked = 0;
        $data->waitlisted = 0;
        $data->cancelled = 0;
        $data->requested = 0;
        $data->issent = 0;
        $data->status = 0; // Disable for existing seminars.
        $data->templateid = $templateid;
        $data->ccmanager = 0;
        $data->title = $title;
        $data->body = $body;

        foreach ($f2fs as $f2f) {
            $notification = clone($data);
            $notification->facetofaceid = $f2f->id;
            $notification->courseid = $f2f->course;

            $DB->insert_record('facetoface_notification', $notification);
        }

        upgrade_mod_savepoint(true, 2018102800, 'facetoface');
    }

    // Multiple signups upgrade part 3 of 3.
    if ($oldversion < 2018102900) {
        // Just to be safe, set maximum to 1 if multisignups is disabled.
        $DB->execute('UPDATE {facetoface}
                         SET multisignupmaximum = 1
                       WHERE multiplesessions = 0');

        // Quick change to the settings for the amount dropdown.
        $enabled = (bool) get_config(null, 'facetoface_multiplesessions');

        $amount = $enabled ? 0 : 1;
        set_config('facetoface_multisignupamount', $amount);

        // Now we have finally reached the final stage of multisignup upgrades.
        // Unset the old setting, and the two new ones merged here.
        unset_config('facetoface_multiplesessions');
        unset_config('facetoface_multisignup_enable');
        unset_config('facetoface_multisignup_maximum');

        upgrade_mod_savepoint(true, 2018102900, 'facetoface');
    }

    if ($oldversion < 2018120701) {
        // Remove facetoface_fromaddress config as we use noreply address only, see TL-13943.
        unset_config('facetoface_fromaddress');
        upgrade_mod_savepoint(true, 2018120701, 'facetoface');
    }

    // Update the template's title for seminar's trainer confirmation.
    if ($oldversion < 2018120702) {
        // By default: [eventperiod] => [starttime]-[finishtime], [sessiondate]
        $default = array(
            "trainerconfirm" => array(
                "old" =>  "Seminar trainer confirmation: [facetofacename], [starttime]-[finishtime], [sessiondate]",
                "new" => "Seminar trainer confirmation: [seminarname], [eventperiod]"
            ),
            "rolerequest" => array(
                "old" => "Seminar booking role request: [facetofacename], [starttime]-[finishtime], [sessiondate]",
                "new" => "Seminar booking role request: [seminarname], [eventperiod]",
            ),
            "request" => array(
                "old" => "Seminar booking request: [facetofacename], [starttime]-[finishtime], [sessiondate]",
                "new" => "Seminar booking request: [seminarname], [eventperiod]"
            ),
            "adminrequest" => array(
                "old" => "Seminar booking admin request: [facetofacename], [starttime]-[finishtime], [sessiondate]",
                "new" => "Seminar booking admin request: [seminarname], [eventperiod]"
            )
        );

        $references = array("trainerconfirm", "rolerequest", "request", "adminrequest");
        list($sqlin, $params) = $DB->get_in_or_equal($references);

        $sql = "SELECT * FROM {facetoface_notification_tpl} WHERE reference {$sqlin}";
        $records = $DB->get_records_sql($sql, $params);
        foreach ($records as $record) {
            if (isset($default[$record->reference])) {
                $title = $default[$record->reference];
                // Only updating the title if it is the same, with the old default, otherwise,
                // leave it be, as user already modified it.
                if ($title['old'] === $record->title) {
                    $record->title = $title['new'];
                    $DB->update_record("facetoface_notification_tpl", $record);
                }
            }
        }

        upgrade_mod_savepoint(true, 2018120702, 'facetoface');
    }

    // Add support for session attendance tracking
    if ($oldversion < 2019011100) {
        // Add columns to facetoface table to support session attendance.
        $facetoface_fields = [
            new xmldb_field('multisignupunableto', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'multisignupnoshow'),
            new xmldb_field('sessionattendance', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('attendancetime', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0'),
        ];
        $table = new xmldb_table('facetoface');
        foreach ($facetoface_fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
        // Create facetoface_signups_dates_status table to track signup attendance per session date.
        $signupdatestat_fields = [
            new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null),
            new xmldb_field('signupid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null),
            new xmldb_field('sessiondateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null),
            new xmldb_field('attendancecode', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null),
            new xmldb_field('superceded', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null),
            new xmldb_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null),
            new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null)
        ];
        $signupdatestat_keys = [
            new xmldb_key('primary', XMLDB_KEY_PRIMARY, ['id']),
            new xmldb_key('signupid_fk', XMLDB_KEY_FOREIGN, ['signupid'], 'facetoface_signups', ['id']),
            new xmldb_key('sessiondateid_fk', XMLDB_KEY_FOREIGN, ['sessiondateid'], 'facetoface_sessions_dates', ['id']),
            new xmldb_key('facesigndatestat_cre_fk', XMLDB_KEY_FOREIGN, ['createdby'], 'user', ['id'])
        ];
        $table = new xmldb_table('facetoface_signups_dates_status');
        foreach ($signupdatestat_fields as $field) {
            $table->addField($field);
        }
        foreach ($signupdatestat_keys as $key) {
            $table->addKey($key);
        }

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2019011100, 'facetoface');
    }

    // Add support for event manual grading
    if ($oldversion < 2019030100) {
        $table = new xmldb_table('facetoface');
        $field = new xmldb_field('eventgradingmanual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'attendancetime');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('eventgradingmethod', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'eventgradingmanual');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Fix grades if necessary
        facetoface_upgradelib_fixup_seminar_grades();

        upgrade_mod_savepoint(true, 2019030100, 'facetoface');
    }

    // evacuate users that will be stuck in the requested state
    if ($oldversion < 2019030101) {
        facetoface_upgradelib_requestedrole_state_for_role_approval();

        upgrade_mod_savepoint(true, 2019030101, 'facetoface');
    }

    if ($oldversion < 2019032000) {
        // Update all job assignments to NULL where were deleted from user job assignments.
        $sql = "UPDATE {facetoface_signups}
                   SET jobassignmentid = NULL
                 WHERE jobassignmentid NOT IN (
                       SELECT id
                         FROM {job_assignment}
                 )";
        $DB->execute($sql);

        upgrade_mod_savepoint(true, 2019032000, 'facetoface');
    }

    if ($oldversion < 2019050100) {
        // Define field completionpass to be added to facetoface.
        $table = new xmldb_table('facetoface');

        // Conditionally launch add field completionpass.
        $field = new xmldb_field('completionpass', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'eventgradingmethod');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set "Require grade" to "Yes, any grade" if "Learner must receive a grade to complete this activity" is already enabled
        $sql = "UPDATE {facetoface}
                   SET completionpass = 1
                 WHERE id IN (
                    SELECT cm.instance
                      FROM {course_modules} cm
                      JOIN {modules} m ON m.id = cm.module
                     WHERE m.name = 'facetoface' AND cm.completion = 2 AND cm.completiongradeitemnumber = 0
                 ) AND completionpass = 0";
        $DB->execute($sql);

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2019050100, 'facetoface');
    }

    if ($oldversion < 2019061200) {
        // Create the default notification template for undercapacity.
        $title = get_string('setting:defaultundercapacitysubjectdefault', 'facetoface');
        if (\core_text::strlen($title) > 255) {
            $title = \core_text::substr($title, 0, 255);
        }

        $body = text_to_html(get_string('setting:defaultundercapacitymessagedefault', 'facetoface'));

        if (!$DB->record_exists('facetoface_notification_tpl', ['reference' => 'undercapacity'])) {
            $tpl_undercapacity = new stdClass();
            $tpl_undercapacity->status = 1;
            $tpl_undercapacity->reference = 'undercapacity';
            $tpl_undercapacity->title = $title;
            $tpl_undercapacity->body = $body;
            $tpl_undercapacity->ccmanager = 0;
            $templateid = $DB->insert_record('facetoface_notification_tpl', $tpl_undercapacity);
        } else {
            $templateid = $DB->get_field('facetoface_notification_tpl', 'id', ['reference' => 'undercapacity']);
        }

        // Now add the new template to existing seminars that don't already have one.
        // NOTE: We don't normally want to do this, but it's safe to do
        //       here since this is replacing an existing non-template notification.
        $conditiontype = 1048576; // Constant MDL_F2F_CONDITION_SESSION_UNDER_CAPACITY.
        $sql = 'SELECT f.*
                  FROM {facetoface} f
             LEFT JOIN {facetoface_notification} fn
                    ON fn.facetofaceid = f.id
                   AND fn.conditiontype = :ctype
             WHERE fn.id IS NULL';
        $f2fs = $DB->get_records_sql($sql, ['ctype' => $conditiontype]);

        $data = new stdClass();
        $data->type = 4; // MDL_F2F_NOTIFICATION_AUTO.
        $data->conditiontype = $conditiontype;
        $data->booked = 0;
        $data->waitlisted = 0;
        $data->cancelled = 0;
        $data->requested = 0;
        $data->issent = 0;
        $data->status = 1; // Replacing a hard-coded template
        $data->templateid = $templateid;
        $data->ccmanager = 0;
        $data->title = $title;
        $data->body = $body;

        foreach ($f2fs as $f2f) {
            $notification = clone($data);
            $notification->facetofaceid = $f2f->id;
            $notification->courseid = $f2f->course;

            $DB->insert_record('facetoface_notification', $notification);
        }

        upgrade_mod_savepoint(true, 2019061200, 'facetoface');
    }

    // Remove orphaned session roles.
    if ($oldversion < 2019061600) {
        $sql = "DELETE FROM {facetoface_session_roles} WHERE userid=0";
        $DB->execute($sql);

        upgrade_mod_savepoint(true, 2019061600, 'facetoface');
    }

    // Remove orphaned session roles product of deleting a user.
    if ($oldversion < 2019072500) {
        $sql = "DELETE FROM {facetoface_session_roles} WHERE userid IN (SELECT id FROM {user} WHERE deleted = 1)";
        $DB->execute($sql);

        upgrade_mod_savepoint(true, 2019072500, 'facetoface');
    }

    if ($oldversion < 2019080600) {
        // Changing the default of field attendancetime on table facetoface to 3.
        $table = new xmldb_table('facetoface');
        $field = new xmldb_field('attendancetime', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '3', 'sessionattendance');

        // Launch change of default for field attendancetime.
        $dbman->change_field_default($table, $field);

        facetoface_upgradelib_fixup_seminar_sessionattendance();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2019080600, 'facetoface');
    }

    if ($oldversion < 2019081300) {
        // Reset managerid if it is incorrectly set.
        $sql = 'UPDATE {facetoface_signups}
                   SET managerid = NULL
                 WHERE managerid = 0
                   AND id IN (
                    SELECT DISTINCT signupid
                      FROM {facetoface_signups_status}
                     WHERE superceded = 0 AND (statuscode = 40 OR statuscode = 45)
                   )';
        $DB->execute($sql);

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2019081300, 'facetoface');
    }

    // Define field completiondelay to create criteria for number of days after event when activity completion is allowed.
    if ($oldversion < 2019090100) {
        $table = new xmldb_table('facetoface');
        $field = new xmldb_field('completiondelay', XMLDB_TYPE_INTEGER, '7', null, false, null, null, 'completionpass');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2019090100, 'facetoface');
    }

    // Add room to session dates many-to-many relationship, facilitor tables, and fields completiondelay and decluttersessiontable
    if ($oldversion < 2020020300) {

        $table = new xmldb_table('facetoface_room_dates');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('sessionsdateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('roomid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        // Adding keys to table facetoface_room_dates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('faceroomdate_sess_fk', XMLDB_KEY_FOREIGN, array('sessionsdateid'), 'facetoface_sessions_dates', array('id'));
        $table->add_key('faceroomdate_room_fk', XMLDB_KEY_FOREIGN, array('roomid'), 'facetoface_room', array('id'));
        // Adding index to table facetoface_room_dates.
        $table->add_index('sessionsdateid-roomid', XMLDB_INDEX_UNIQUE, array('sessionsdateid, roomid'));
        // Conditionally launch create table for facetoface_room_dates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // If table structure has not already been modified, move existing rooms.
        $table = new xmldb_table('facetoface_sessions_dates');
        $roomidfield = new xmldb_field('roomid');
        if ($dbman->field_exists($table, $roomidfield)) {
            $transaction = $DB->start_delegated_transaction();
            // Move existing rooms to new table.
            $sql = "SELECT * FROM {facetoface_sessions_dates} WHERE roomid > :roomid";
            $rooms = $DB->get_records_sql($sql, ['roomid' => 0]);
            foreach ($rooms as $item) {
                $todb = new \stdClass();
                $todb->sessionsdateid = $item->id;
                $todb->roomid = $item->roomid;
                $DB->insert_record('facetoface_room_dates', $todb);
            }
            $transaction->allow_commit();

            // Drop roomid from facetoface_sessions_dates.
            $dbman->drop_key(
                $table,
                new xmldb_key('facesessdate_roo_fk', XMLDB_KEY_FOREIGN, array('roomid'), 'facetoface_room', array('id'))
            );
            $dbman->drop_field($table, $roomidfield);
        }

        // Define table for facetoface_facilitator.
        $table = new xmldb_table('facetoface_facilitator');
        // Adding fields to table facetoface_facilitator.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('allowconflicts', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('custom', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('hidden', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('usercreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        // Adding keys to table facetoface_facilitator.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, array('userid'), 'user', 'id');
        $table->add_key('usercreated_fk', XMLDB_KEY_FOREIGN, array('usercreated'), 'user', 'id');
        $table->add_key('usermodified_fk', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', 'id');
        // Adding index to table facetoface_facilitator.
        $table->add_index('custom', XMLDB_INDEX_NOTUNIQUE, array('custom'));
        // Conditionally launch create table for facetoface_facilitator.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table for facetoface_facilitator_info_data.
        $table = new xmldb_table('facetoface_facilitator_info_data');
        // Adding fields to table facetoface_facilitator_info_data.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fieldid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('facetofacefacilitatorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        // Adding keys to table facetoface_facilitator_info_data.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('facilitatorinfodata_fielid_fk', XMLDB_KEY_FOREIGN, array('fieldid'), 'facetoface_facilitator_info_field', array('id'));
        $table->add_key('facilitatorinfodata_facilitatorid_fk', XMLDB_KEY_FOREIGN, array('facetofacefacilitatorid'), 'facetoface_facilitator', array('id'));
        // Adding index to table facetoface_facilitator_info_data.
        $table->add_index('facelitatorinfodata_fiefcltr_uix', XMLDB_INDEX_UNIQUE, array('fieldid, facetofacefacilitatorid'));
        // Conditionally launch create table for facetoface_facilitator_info_data.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table for facetoface_facilitator_info_data_param.
        $table = new xmldb_table('facetoface_facilitator_info_data_param');
        // Adding fields to table facetoface_facilitator_info_data_param.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('dataid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('value', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL);
        // Adding keys to table facetoface_facilitator_info_data_param.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('facilitatorinfodatapara_dataid_fk', XMLDB_KEY_FOREIGN, array('dataid'), 'facetoface_facilitator_info_data', array('id'));
        // Adding index to table facetoface_facilitator_info_data.
        $table->add_index('facilitatorinfodatapara_value_ix', null, array('value'));
        // Conditionally launch create table for facetoface_facilitator_info_data_param.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table for facetoface_facilitator_info_field.
        $table = new xmldb_table('facetoface_facilitator_info_field');
        // Adding fields to table facetoface_facilitator_info_field.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('datatype', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hidden', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('locked', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('required', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('forceunique', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('defaultdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param1', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param2', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param3', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param4', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param5', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '1024', null, null, null, null);
        // Adding keys to table facetoface_facilitator_info_field.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // Conditionally launch create table for facilitator_info_field.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table for facetoface_facilitator_dates to session dates many-to-many relationship.
        $table = new xmldb_table('facetoface_facilitator_dates');
        // Adding fields to table facetoface_facilitator_dates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('sessionsdateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('facilitatorid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        // Adding keys to table facetoface_facilitator_dates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('facelitatordate_sess_fk', XMLDB_KEY_FOREIGN, array('sessionsdateid'), 'facetoface_sessions_dates', array('id'));
        $table->add_key('facelitatordate_faci_fk', XMLDB_KEY_FOREIGN, array('facilitatorid'), 'facetoface_facilitator', array('id'));
        // Adding index to table facetoface_facilitator_dates.
        $table->add_index('sessionsdateid-facilitatorid', XMLDB_INDEX_UNIQUE, array('sessionsdateid, facilitatorid'));
        // Conditionally launch create table for facetoface_facilitator_info_data_param.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field completiondelay to create criteria for number of days after event when activity completion is allowed.
        $table = new xmldb_table('facetoface_room');
        $field = new xmldb_field('url', XMLDB_TYPE_CHAR, '1024', null, false, null, null, 'allowconflicts');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field decluttersessiontable to be added to facetoface.
        $table = new xmldb_table('facetoface');
        $field = new xmldb_field('decluttersessiontable', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'waitlistautoclean');

        // Conditionally launch add field decluttersessiontable.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020020300, 'facetoface');
    }

    if ($oldversion < 2020021300) {
        // Fix problems from older upgrades.

        // Define key facesign_man_fk (foreign) to be added to facetoface_signups.
        $table = new xmldb_table('facetoface_signups');
        $key = new xmldb_key('facesign_man_fk', XMLDB_KEY_FOREIGN, array('managerid'), 'user', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Define key facesign_job_fk (foreign) to be added to facetoface_signups.
        $table = new xmldb_table('facetoface_signups');
        $key = new xmldb_key('facesign_job_fk', XMLDB_KEY_FOREIGN, array('jobassignmentid'), 'job_assignment', array('id'));
        if (!$dbman->key_exists($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Define index custom (not unique) to be added to facetoface_asset.
        $table = new xmldb_table('facetoface_asset');
        $index = new xmldb_index('custom', XMLDB_INDEX_NOTUNIQUE, array('custom'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020021300, 'facetoface');
    }

    if ($oldversion < 2020021400) {
        // Define index facesignup_sessarchiv_ix (not unique) to be added to facetoface_signups.
        $table = new xmldb_table('facetoface_signups');
        $index = new xmldb_index('facesignup_sessarchiv_ix', XMLDB_INDEX_NOTUNIQUE, array('userid', 'sessionid', 'archived'));

        // Conditionally launch add index facesignup_sessarchiv_ix.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020021400, 'facetoface');
    }

    if ($oldversion < 2020021700) {
        // Delete orphaned calendar events related to seminars.
        facetoface_upgradelib_delete_orphaned_events();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020021700, 'facetoface');
    }

    if ($oldversion < 2020061100) {
        $table = new xmldb_table('facetoface_notification');
        $field = new xmldb_field('recipients', XMLDB_TYPE_TEXT, null, null, false, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020061100, 'facetoface');
    }

    if ($oldversion < 2020061700) {
        // Create the default notification template for facilitatorcancel.
        facetoface_upgradelib_add_new_template(
            'facilitatorcancel',
            get_string('setting:defaultfacilitatorcancelsubjectdefault', 'facetoface'),
            get_string('setting:defaultfacilitatorcancelmessagedefault', 'facetoface'),
            1 << 21 // Constant MDL_F2F_CONDITION_FACILITATOR_SESSION_CANCELLATION
        );

        // Create the default notification template for facilitatortimechange.
        facetoface_upgradelib_add_new_template(
            'facilitatortimechange',
            get_string('setting:defaultfacilitatortimechangesubjectdefault', 'facetoface'),
            get_string('setting:defaultfacilitatortimechangemessagedefault', 'facetoface'),
            1 << 22 // Constant MDL_F2F_CONDITION_FACILITATOR_SESSION_DATETIME_CHANGE
        );

        // Create the default notification template for facilitatorassigned.
        facetoface_upgradelib_add_new_template(
            'facilitatorassigned',
            get_string('setting:defaultfacilitatorassignedsubjectdefault', 'facetoface'),
            get_string('setting:defaultfacilitatorassignedmessagedefault', 'facetoface'),
            1 << 23 // Constant MDL_F2F_CONDITION_FACILITATOR_SESSION_ASSIGNED
        );

        // Create the default notification template for facilitatorunassigned.
        facetoface_upgradelib_add_new_template(
            'facilitatorunassigned',
            get_string('setting:defaultfacilitatorunassignedsubjectdefault', 'facetoface'),
            get_string('setting:defaultfacilitatorunassignedmessagedefault', 'facetoface'),
            1 << 24 // Constant MDL_F2F_CONDITION_FACILITATOR_SESSION_UNASSIGNED
        );

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020061700, 'facetoface');
    }

    if ($oldversion < 2020100101) {
        // Fixed the orphaned records with statuscode 50 as we deprecated "Approved" status.
        facetoface_upgradelib_approval_to_declined_status();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020100101, 'facetoface');
    }

    // Virtual room updates.
    if ($oldversion < 2020100103) {
        // Create the room dates virtual meeting table to link room dates to virtual meetings.
        $table = new xmldb_table('facetoface_room_dates_virtualmeeting');
        // Fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('roomdateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('virtualmeetingid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        // Keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('roomdatevm_date_fk', XMLDB_KEY_FOREIGN, array('roomdateid'), 'facetoface_room_dates', array('id'));
        $table->add_key('roomdatevm_meet_fk', XMLDB_KEY_FOREIGN, array('virtualmeetingid'), 'virtualmeeting', array('id'));

        // Add the meetingid field to room dates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Create the room virtual meeting table, to link a room with a wirtual meeting plugin type.
        $table = new xmldb_table('facetoface_room_virtualmeeting');
        // Fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('roomid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
        $table->add_field('options', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        // Keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('roomvm_room_fk', XMLDB_KEY_FOREIGN, array('roomid'), 'facetoface_room', array('id'));
        $table->add_key('roomvm_user_fk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        // Indexs.
        $table->add_index('roomvm_plugin', XMLDB_INDEX_NOTUNIQUE, array('plugin'));

        // Add the meetingid field to room dates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Create the default notification template for virtualmeetingfailure.
        facetoface_upgradelib_add_new_template(
            'virtualmeetingfailure',
            get_string('setting:defaultvirtualmeetingfailuresubjectdefault', 'facetoface'),
            get_string('setting:defaultvirtualmeetingfailuremessagedefault', 'facetoface'),
            1 << 25 // MDL_F2F_CONDITION_VIRTUALMEETING_CREATION_FAILURE
        );

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020100103, 'facetoface');
    }

    if ($oldversion < 2020100104) {
        // Fixed the orphaned url records left after a room is changed from 'Internal' to 'MS teams'.
        facetoface_upgradelib_clear_room_url();

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020100104, 'facetoface');
    }

    if ($oldversion < 2020100105) {
        // Change virtual meeting creating failure notification template.
        $oldplaceholder = '[session:room:link]';
        $newplaceholder = '[seminareventdetailslink]';

        // Load templates that match reference
        $templates = $DB->get_records('facetoface_notification_tpl', ['reference' => 'virtualmeetingfailure']);
        $matchingtemplates = [];

        // For each matching template, see if old placeholder is in use and replace it
        foreach ($templates as $template) {
            if (isset($template->body) && strpos($template->body, $oldplaceholder) !== false) {
                $matchingtemplates[$template->id] = ['old' => $template->body];
                $template->body = str_replace($oldplaceholder, $newplaceholder, $template->body);
                $matchingtemplates[$template->id]['new'] = $template->body;
                $DB->update_record('facetoface_notification_tpl', $template);
            }
        }

        // For each of the matching templates, sync up the body on linked activity notifications that haven't been changed
        foreach ($matchingtemplates as $id => $templatebody) {
            $notifications = $DB->get_records('facetoface_notification', ['templateid' => $id]);
            foreach ($notifications as $f2f_notification) {
                if (isset($f2f_notification->body) && $f2f_notification->body == $templatebody['old']) {
                    $f2f_notification->body = $templatebody['new'];
                    $DB->update_record('facetoface_notification', $f2f_notification);
                }
            }
        }

        // Facetoface savepoint reached.
        upgrade_mod_savepoint(true, 2020100105, 'facetoface');
    }

    return true;
}
