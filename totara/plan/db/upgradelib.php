<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2014 onwards Totara Learning Solutions LTD
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
 * @author Nathan Lewis <nathan.lewis@totaralms.com>
 * @package totara_certification
 */


// TL-14290 duedate in dp_plan_program_assign must not be -1, instead use 0.
function totara_plan_upgrade_fix_invalid_program_duedates() {
    global $DB;

    $sql = "UPDATE {dp_plan_program_assign} SET duedate = 0 WHERE duedate = -1";
    $DB->execute($sql);
}

/**
 * TL-16908 Evidence customfield files are not deleted when evidence is deleted.
 *
 * Cleans up any orphaned file records from the files table where evidence was
 * previously deleted but left the file related data in the table.
 */
function totara_plan_upgrade_clean_deleted_evidence_files() {
    global $DB;

    // When an evidence is deleted, records in the dp_plan_evidence_info_data table are removed,
    // but file entries in the files table still link to these records via the files.itemid column.
    // This code removes all the dangling file entries.

    $sql = "
      SELECT f.component, f.filearea, f.itemid
        FROM {files} f
      WHERE f.component = 'totara_customfield'
        AND (f.filearea = 'evidence' OR f.filearea = 'evidence_filemgr')
        AND NOT EXISTS (
          SELECT 1
            FROM {totara_evidence_type_info_data} dp
          WHERE dp.id = f.itemid
        )
    ";

    $context = context_system::instance()->id;
    $fs = get_file_storage();
    $results = $DB->get_recordset_sql($sql);

    foreach($results as $rs) {
        $fs->delete_area_files($context, $rs->component, $rs->filearea, $rs->itemid);
    }
    $results->close();
}

/**
 * As part of the new evidence feature (TL-19315) we no longer need these tables,
 * since we use new ones defined in totara_evidence.
 *
 * We still need dp_plan_evidence_relation however.
 */
function totara_plan_upgrade_remove_evidence_tables() {
    global $DB;
    $dbman = $DB->get_manager();

    $old_evidence_tables = [
        new xmldb_table('dp_plan_evidence'),
        new xmldb_table('dp_plan_evidence_info_field'),
        new xmldb_table('dp_plan_evidence_info_data'),
        new xmldb_table('dp_plan_evidence_info_data_param'),
        new xmldb_table('dp_evidence_type'),
    ];

    foreach ($old_evidence_tables as $table) {
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
    }
}

/** Covert any programs assigned to plans as actual program assignments using the new plan assignment type.
 *
 * Also ensure program messages will not be inadvertently sent due to this.
 *
 * The new plan assignment type was introduced via TL-24703
 */
function totara_plan_upgrade_do_program_assignments() {
    global $DB;

    $prog_messages = [];
    $prog_coursesets = [];
    $now = time();

    $sql = "SELECT DISTINCT(p.id), p.userid
              FROM {dp_plan} p
              JOIN {dp_plan_program_assign} ppa on ppa.planid = p.id";
    $records = $DB->get_records_sql($sql);

    foreach ($records as $record) {
        // Do the assignment(s), can be multiple programs for the user.
        \totara_program\assignment\plan::update_plan_assignments($record->userid, $record->id);

        // Get all that have just been assigned.
        $assignments = \totara_program\assignment\plan::get_user_assignments($record->userid, $record->id);

        // Ensure program messages are not going to be inadvertently sent due to the new assignment.
        foreach ($assignments as $assignmentid => $programid) {
            // Did the user already have an assignment to this program?
            $sql = "SELECT 1
                      FROM {prog_user_assignment} pua
                      JOIN {prog_assignment} pa ON pa.id = pua.assignmentid
                     WHERE pua.userid = :userid
                       AND pua.programid = :programid
                       AND pa.assignmenttype != :assignmenttype";
            $params = [
                'userid' => $record->userid,
                'programid' => $programid,
                'assignmenttype' => ASSIGNTYPE_PLAN,
            ];
            $alreadyassigned = $DB->record_exists_sql($sql, $params);
            if ($alreadyassigned) {
                // User already had an assignment.
                continue;
            }

            $insertrecords = [];

            // Loop through all messages set for the program.
            if (!isset($prog_messages[$programid])) {
                $prog_messages[$programid] = $DB->get_records('prog_message', ['programid' => $programid]);
            }
            foreach ($prog_messages[$programid] as $prog_message) {
                $todb = [
                    'messageid' => $prog_message->id,
                    'userid' => $record->userid,
                    'coursesetid' => 0
                ];

                if ($DB->record_exists('prog_messagelog', $todb)) {
                    // Message has already been sent.
                    continue;
                }

                switch ($prog_message->messagetype) {
                    case MESSAGETYPE_ENROLMENT:
                    case MESSAGETYPE_PROGRAM_COMPLETED:
                        // Ensure the enrolment and completed message do not get sent.
                        $todb['timeissued'] = $now;
                        $insertrecords[] = $todb;
                        break;
                    case MESSAGETYPE_COURSESET_DUE:
                    case MESSAGETYPE_COURSESET_OVERDUE:
                    case MESSAGETYPE_COURSESET_COMPLETED:
                        // Get the programs coursesets.
                        if (!isset($prog_coursesets[$programid])) {
                            $prog_coursesets[$programid] = $DB->get_records('prog_courseset', ['programid' => $programid], 'id ASC', 'id');
                        }
                        // Ensure the courseset related messages do not get sent.
                        foreach ($prog_coursesets[$programid] as $courseset) {
                            $todb['coursesetid'] = $courseset->id;
                            unset($todb['timeissued']);
                            if (!$DB->record_exists('prog_messagelog', $todb)) {
                                $todb['timeissued'] = $now;
                                $insertrecords[] = $todb;
                            }
                        }
                        break;
                    case MESSAGETYPE_PROGRAM_DUE:
                        $timedue = $DB->get_field('prog_completion', 'timedue', ['programid' => $programid, 'userid' => $record->userid]);
                        if ($timedue && $timedue > 0 && (($timedue - $prog_message->triggertime) < $now)) {
                            $todb['timeissued'] = $now;
                            $insertrecords[] = $todb;
                        }
                        break;
                    case MESSAGETYPE_PROGRAM_OVERDUE:
                        $timedue = $DB->get_field('prog_completion', 'timedue', ['programid' => $programid, 'userid' => $record->userid]);
                        if ($timedue && $timedue > 0 && (($timedue + $prog_message->triggertime) < $now)) {
                            $todb['timeissued'] = $now;
                            $insertrecords[] = $todb;
                        }
                        break;
                }
            }
            if ($insertrecords) {
                $DB->insert_records('prog_messagelog', $insertrecords);
            }
        }
    }
    return true;
}
