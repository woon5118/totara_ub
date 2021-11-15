<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Update 9.0 manager prefix strings to new with added "Below is the message that was sent to learner:" suffix.
 * If affects only unchanged original 9.0 strings in facetoface notifications and their templates
 */
function facetoface_upgradelib_managerprefix_clarification() {
    global $DB;

    $upgradestrings = [
        "setting:defaultconfirmationinstrmngrdefault" => "setting:defaultconfirmationinstrmngrdefault_v92",
        "setting:defaultcancellationinstrmngrdefault" => "setting:defaultcancellationinstrmngrdefault_v92",
        "setting:defaultreminderinstrmngrdefault" => "setting:defaultreminderinstrmngrdefault_v92",
        "setting:defaultrequestinstrmngrdefault" => "setting:defaultrequestinstrmngrdefault_v92",
        "setting:defaultrolerequestinstrmngrdefault" => "setting:defaultrolerequestinstrmngrdefault_v92",
        "setting:defaultadminrequestinstrmngrdefault" => "setting:defaultadminrequestinstrmngrdefault_v92",
        "setting:defaultdeclineinstrmngrdefault" => "setting:defaultdeclineinstrmngrdefault_v92",
        "setting:defaultregistrationexpiredinstrmngr" => "setting:defaultregistrationexpiredinstrmngr_v92",
        "setting:defaultpendingreqclosureinstrmngrcopybelow" => "setting:defaultpendingreqclosureinstrmngrcopybelow_v92"
    ];
    // Get all notifications templates.
    $notificationtables = ['facetoface_notification_tpl', 'facetoface_notification'];
    foreach ($notificationtables as $table) {
        $templates = $DB->get_records($table);
        foreach ($templates as $template) {
            foreach ($upgradestrings as $original => $new) {
                // Conditionaly update strings according content.
                if (strcmp($template->managerprefix, text_to_html(get_string($original, 'facetoface'))) === 0) {
                    $template->managerprefix = text_to_html(get_string($new, 'facetoface'));
                    $DB->update_record($table, $template);
                }
            }
        }
    }
}

/**
 * Ensure all Calendar events exist for seminar sessions with multiple dates
 * Events are only created for missing sessiondates if there are at least one existing event
 * Non-date values are copied from the existing event(s)
 */
function facetoface_upgradelib_calendar_events_for_sessiondates() {
    global $DB;

    // Due to the type inconsistencies between {event}.uuid and {facetoface_sessions_dates}.id,
    // we can't use a join between events and facetoface_sessions_dates. We therefore need to
    // use muliple statements to allow $DB to handle type conversions.

    // Get all the facetoface_sessions that have events with multiple dates
    $sql = 'SELECT sessionid
            FROM {facetoface_sessions_dates}
        GROUP BY sessionid
          HAVING count(id) > 1';

    if ($sessionidrows = $DB->get_records_sql($sql)) {
        // As other settings may have prevented calendar events being created for these sessions,
        // we only create missing events if there is at least one existing event for this each session

        foreach ($sessionidrows as $sessionidrow) {
            $sql = 'SELECT DISTINCT e.name, e.description, e.format, e.courseid, e.groupid, e.userid,
                                    e.uuid, e.instance, e.modulename, e.eventtype, e.visible
                  FROM {event} e
                 WHERE e.uuid = :uuid
                   AND e.modulename = :modulename
                   AND e.eventtype = :eventtype';
            $params =  array('uuid' => $sessionidrow->sessionid,
                             'modulename' => 'facetoface',
                             'eventtype' => 'facetofacesession');

            if ($eventrows = $DB->get_recordset_sql($sql, $params)) {
                foreach ($eventrows as $eventrow) {
                    $sql = 'INSERT INTO {event}
                                        (name, description, format, courseid, groupid, userid, uuid, instance, modulename, eventtype,
                                         timestart, timeduration, visible)
                                 SELECT :name, :description, :format, :courseid, :groupid, :userid, :uuid, :instance, :modulename, :eventtype,
                                        d.timestart, d.timefinish - d.timestart, :visible
                                   FROM {facetoface_sessions_dates} d
                                  WHERE d.sessionid = :sessionid
                                    AND d.timestart not in (
                                        SELECT timestart
                                        FROM {event} e
                                        WHERE e.uuid = :seluuid
                                          AND e.courseid = :selcourseid
                                          AND e.groupid = :selgroupid
                                          AND e.userid = :seluserid)';

                    $params = array('name' => $eventrow->name,
                                    'description' => $eventrow->description,
                                    'format' => $eventrow->format,
                                    'courseid' => $eventrow->courseid,
                                    'groupid' => $eventrow->groupid,
                                    'userid' => $eventrow->userid,
                                    'uuid' => $eventrow->uuid,
                                    'instance' => $eventrow->instance,
                                    'modulename' => $eventrow->modulename,
                                    'eventtype' => $eventrow->eventtype,
                                    'visible' => $eventrow->visible,
                                    'sessionid' => $sessionidrow->sessionid,
                                    'seluuid' => $eventrow->uuid,
                                    'selcourseid' => $eventrow->courseid,
                                    'selgroupid' => $eventrow->groupid,
                                    'seluserid' => $eventrow->userid,
                                );

                    $DB->execute($sql, $params);
                }
            }
        }
    }
}

/**
 * Move requested state to requestedrole state for role approval.
 */
function facetoface_upgradelib_requestedrole_state_for_role_approval() {
    global $DB;

    // magic numbers
    /** @var int @see \mod_facetoface\seminar::APPROVAL_ROLE */
    $seminar_approval_role = 2;
    /** @var int @see \mod_facetoface\signup\state\requested::get_code() */
    $requested_statuscode = 40;
    /** @var int @see \mod_facetoface\signup\state\requestedrole::get_code() */
    $requestedrole_statuscode = 44;

    $trans = $DB->start_delegated_transaction();
    $statuses = $DB->get_records_sql(
        'SELECT sus.*
         FROM {facetoface_signups_status} sus
         JOIN {facetoface_signups} su ON su.id = sus.signupid
         JOIN {facetoface_sessions} s ON s.id = su.sessionid
         JOIN {facetoface} f ON f.id = s.facetoface
         WHERE f.approvaltype = :at AND sus.statuscode = :cd AND sus.superceded = 0',
        [ 'at' => $seminar_approval_role, 'cd' => $requested_statuscode ]
    );
    foreach ($statuses as $status) {
        $status->statuscode = $requestedrole_statuscode;
        $DB->update_record('facetoface_signups_status', $status);
    }
    $trans->allow_commit();
}

/**
 * Rewrite grades if they are 0.0000 and their signup state is either fully/partially attended.
 * Note that we decided in TL-20775, to not provide fixup for a system upgrading from Evergreen-20190322.
 */
function facetoface_upgradelib_fixup_seminar_grades() {
    global $DB;
    /** @var \moodle_database $DB */
    // magic numbers
    // status_fully: 100
    // status_partially: 90
    // status_booked: 70
    // grade_fully: 100
    // grade_partially: 50

    $DB->execute('UPDATE {facetoface_signups_status} SET grade = NULL WHERE statuscode <= 70');
    $DB->execute('UPDATE {facetoface_signups_status} SET grade = 50 WHERE statuscode = 90 AND grade = 0');
    $DB->execute('UPDATE {facetoface_signups_status} SET grade = 100 WHERE statuscode = 100 AND grade = 0');
}

/**
 * Do what seminar::fix_up_session_attendance_time() do.
 */
function facetoface_upgradelib_fixup_seminar_sessionattendance() {
    global $DB;
    /** @var \moodle_database $DB */

    if (get_config('facetoface', 'sessionattendance') == 1) {
        switch (get_config('facetoface', 'attendancetime')) {
            case 0:
                // ATTENDANCE_TIME_END -> SESSION_ATTENDANCE_END
                set_config('sessionattendance', 4, 'facetoface');
                break;
            case 1:
                // ATTENDANCE_TIME_START -> SESSION_ATTENDANCE_START
                set_config('sessionattendance', 5, 'facetoface');
                break;
            case 2:
                // ATTENDANCE_TIME_ANY -> SESSION_ATTENDANCE_UNRESTRICTED
                set_config('sessionattendance', 2, 'facetoface');
                break;
            default:
                // SESSION_ATTENDANCE_DISABLED
                set_config('sessionattendance', 0, 'facetoface');
                break;
        }
    }

    // ATTENDANCE_TIME_END -> SESSION_ATTENDANCE_END
    $DB->execute('UPDATE {facetoface} SET sessionattendance = 4 WHERE sessionattendance = 1 AND attendancetime = 0');
    // ATTENDANCE_TIME_START -> SESSION_ATTENDANCE_START
    $DB->execute('UPDATE {facetoface} SET sessionattendance = 5 WHERE sessionattendance = 1 AND attendancetime = 1');
    // ATTENDANCE_TIME_ANY -> SESSION_ATTENDANCE_UNRESTRICTED
    $DB->execute('UPDATE {facetoface} SET sessionattendance = 2 WHERE sessionattendance = 1 AND attendancetime = 2');

    // Disable session attendance tracking if previous event attendance time is none of defined ATTENDANCE_TIME_xxx values
    [ $sql, $params ] = $DB->get_in_or_equal([0, 1, 2], SQL_PARAMS_QM, 'param', false);
    $DB->execute('UPDATE {facetoface} SET sessionattendance = 0 WHERE sessionattendance = 1 AND attendancetime ' . $sql, $params);
}

/**
 * Do what seminar::fix_up_session_attendance_time() do.
 */
function facetoface_upgradelib_delete_orphaned_events() {
    global $DB;

    // Delete seminar event orphan records from calendar.
    $id_cast = $DB->sql_cast_2char('id');
    $sql = "DELETE FROM {event}
                      WHERE modulename = 'facetoface'
                        AND uuid NOT IN (
                                 SELECT {$id_cast} FROM {facetoface_sessions}
                        )";
    $DB->execute($sql);
}

/**
 * Wrapper for adding a new template.
 *
 * @param string $reference
 * @param string $title
 * @param string $body
 * @param integer $conditiontype MDL_F2F_CONDITION_xx as **a magic number**
 */
function facetoface_upgradelib_add_new_template(string $reference, string $title, string $body, int $conditiontype) {
    global $DB;
    /** @var moodle_database $DB */

    if (\core_text::strlen($title) > 255) {
        $title = \core_text::substr($title, 0, 255);
    }

    // Make sure that $conditiontype is a power of 2.
    if (($conditiontype & ($conditiontype - 1)) !== 0) {
        throw new coding_exception('$conditiontype is not a valid number.');
    }

    $body = text_to_html($body);

    $templateid = $DB->get_field('facetoface_notification_tpl', 'id', ['reference' => $reference]);
    if ($templateid === false) {
        $templateid = $DB->insert_record('facetoface_notification_tpl', [
            'status' => 1,
            'reference' => $reference,
            'title' => $title,
            'body' => $body,
            'ccmanager' => 0,
        ]);
    }

    // Now add the new template to existing seminars that don't already have one.
    // NOTE: We don't normally want to do this, but it's safe to do
    //       here since this is replacing an existing non-template notification.
    $sql = 'SELECT f.id, f.course
              FROM {facetoface} f
         LEFT JOIN {facetoface_notification} fn
                ON fn.facetofaceid = f.id
               AND fn.conditiontype = :ctype
             WHERE fn.id IS NULL';
    $f2fs = $DB->get_records_sql($sql, ['ctype' => $conditiontype]);

    $data = [
        'type' => 4, // MDL_F2F_NOTIFICATION_AUTO.
        'conditiontype' => $conditiontype,
        'booked' => 0,
        'waitlisted' => 0,
        'cancelled' => 0,
        'requested' => 0,
        'issent' => 0,
        'status' => 1, // Replacing a hard-coded template
        'templateid' => $templateid,
        'ccmanager' => 0,
        'title' => $title,
        'body' => $body,
    ];

    foreach ($f2fs as $f2f) {
        $notification = array_merge([
            'facetofaceid' => $f2f->id,
            'courseid' => $f2f->course,
        ], $data);

        $DB->insert_record('facetoface_notification', $notification);
    }
}

/**
 * Fixed the orphaned records with statuscode 50 as we deprecated "Approved" status.
 */
function facetoface_upgradelib_approval_to_declined_status() {
    global $DB;

    $superceded = 0;
    $statuscode = 50;
    $statuses = $DB->get_records_sql(
        'SELECT fss.*, u.id AS userid, f.id AS facetofaceid
           FROM {facetoface_signups_status} fss
           JOIN {facetoface_signups} fs ON fs.id = fss.signupid
           JOIN {facetoface_sessions} s ON s.id = fs.sessionid
           JOIN {facetoface} f ON f.id = s.facetoface
           JOIN {user} u ON u.id = fs.userid
          WHERE superceded = :superceded AND statuscode = :statuscode',
        ['superceded' => $superceded, 'statuscode' => $statuscode]
    );
    /** @see \mod_facetoface\signup\state\declined::get_code() */
    $declined_status = 30;
    $upgrade_log_notice = defined('UPGRADE_LOG_NOTICE') ? UPGRADE_LOG_NOTICE : 1;
    $trans = $DB->start_delegated_transaction();
    foreach ($statuses as $status) {
        // Update the record.
        $DB->set_field('facetoface_signups_status', 'statuscode', $declined_status, ['id' => $status->id]);

        // Add a log message.
        upgrade_log(
            $upgrade_log_notice,
            'mod_facetoface',
            'Invalid user signup cancelled: userid ' . $status->userid . ', facetofaceid ' . $status->facetofaceid
        );
    }
    $trans->allow_commit();
}

/**
 * Fixed the orphaned url records left after a room is changed from 'Internal' to 'MS teams'.
 */
function facetoface_upgradelib_clear_room_url() {
    global $DB;

    $trans = $DB->start_delegated_transaction();
    $rooms = $DB->get_records_sql(
        "SELECT frvm.roomid
           FROM {facetoface_room_virtualmeeting} frvm
           JOIN {facetoface_room} fr ON fr.id = frvm.roomid
          WHERE fr.url != ''"
    );
    foreach ($rooms as $room) {
        $DB->set_field('facetoface_room', 'url', '', ['id' => $room->roomid]);
    }
    $trans->allow_commit();
}