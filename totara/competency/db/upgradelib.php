<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package totara_competency
 */

defined('MOODLE_INTERNAL') || die();

function totara_competency_install_migrate_achievements() {
    global $DB;
    $dbman = $DB->get_manager();

    $comp_record_table = new xmldb_table('comp_record');

    if (!$dbman->table_exists($comp_record_table)) {
        // This might be an initial install or some other scenario. Whatever the reason may be, nothing to do.
        return;
    }

    // We take from the history table as this actually includes current records as well.
    // We determine the current record from the following rules:
    // 1. The comp_history_record has a corresponding comp_record
    // 2. The timemodified on the comp_history_record is most recent.
    // There is a risk that you have 2 history records with the same timemodified, so we can choose the one that is actually linked
    // to a current record.
    // There is also a very narrow risk that somehow a history record has a later date than the current comp_record, if that
    // may have happened, we'll take the one linked to the comp_record since that's been the one the user considers current
    // up til now.
    // If there's no comp_record, then we'll just be making a history record the most recent timestamp the current one.

    $histories = $DB->get_recordset_sql("
        SELECT crh.id, crh.competencyid, crh.userid, crh.proficiency, crh.timemodified, crh.timeproficient, COALESCE (cr.id, 0) AS comp_record_id
          FROM {comp_record_history} crh
     LEFT JOIN {comp_record} cr 
            ON crh.competencyid = cr.competencyid
           AND crh.userid = cr.userid
           AND (crh.proficiency = cr.proficiency OR crh.proficiency IS NULL AND cr.proficiency IS NULL)
      ORDER BY crh.competencyid, crh.userid, comp_record_id DESC, crh.timemodified DESC
        ");

    $now = time();
    $all_scale_values = $DB->get_records('comp_scale_values');
    $comp_achievements = [];

    // Each combination of user/competency should have one active achievement record.
    // We've ordered by competency and user so that records for each combination are grouped together.
    $current_competency = null;
    $current_user = null;
    // This is true when we are at the first record for a given user and competency.
    $first = true;

    foreach ($histories as $history) {
        if ($current_competency != $history->competencyid || $current_user != $history->userid) {
            // This means we are now up to the records of another user/competency combination.
            $current_competency = $history->competencyid;
            $current_user = $history->userid;
            $first = true;
        }

        $comp_achievement = new stdClass();
        $comp_achievement->comp_id = $history->competencyid;
        $comp_achievement->user_id = $history->userid;
        $comp_achievement->assignment_id = 0;
        $comp_achievement->scale_value_id = $history->proficiency;
        $comp_achievement->proficient = $all_scale_values[$history->proficiency]->proficient ?? 0;

        if ($first) {
            // Represents an achievement from an archived assignment.
            $comp_achievement->status = 1;
            // This makes sure we don't try to add another current record.
            $first = false;
        } else {
            // Represents an achievement that was superseded by another.
            $comp_achievement->status = 2;
        }

        $comp_achievement->time_created = $history->timemodified;
        $comp_achievement->time_proficient = $history->timeproficient;
        $comp_achievement->time_status = $history->timemodified;
        $comp_achievement->time_scale_value = $history->timemodified;
        $comp_achievement->last_aggregated = $now;

        $comp_achievements[] = $comp_achievement;

        if (count($comp_achievements) >= BATCH_INSERT_MAX_ROW_COUNT) {
            $DB->insert_records('totara_competency_achievement', $comp_achievements);
            $comp_achievements = [];
        }
    }

    $histories->close();

    if (count($comp_achievements) > 0) {
        $DB->insert_records('totara_competency_achievement', $comp_achievements);
    }
}