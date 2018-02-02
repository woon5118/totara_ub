<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package mod_glossary
 */

namespace mod_glossary\userdata;

use totara_userdata\userdata\target_user;

defined('MOODLE_INTERNAL') || die();

/**
 * Glossary entries user content.
 */
class entries extends \totara_userdata\userdata\item {
    /**
     * String used for human readable name of this item.
     *
     * @return array parameters of get_string($identifier, $component) to get full item name and optionally help.
     */
    public static function get_fullname_string() {
        return ['entries', 'mod_glossary'];
    }

    /**
     * Returns sort order.
     *
     * @return int
     */
    public static function get_sortorder() {
        return 100;
    }

    /**
     * Returns all contexts this item is compatible with, defaults to CONTEXT_SYSTEM.
     *
     * @return array
     */
    public static function get_compatible_context_levels() {
        return [CONTEXT_SYSTEM, CONTEXT_COURSECAT, CONTEXT_COURSE, CONTEXT_MODULE];
    }

    /**
     * Can user data of this item data be purged from system?
     *
     * @param int $userstatus target_user::STATUS_ACTIVE, target_user::STATUS_DELETED or target_user::STATUS_SUSPENDED
     * @return bool
     */
    public static function is_purgeable(int $userstatus) {
        return true;
    }

    /**
     * Purge user data for this item.
     *
     * NOTE: Remember that context record does not exist for deleted users any more,
     *       it is also possible that we do not know the original user context id.
     *
     * @param target_user $user
     * @param \context $context restriction for purging e.g., system context for everything, course context for purging one course
     * @return int result self::RESULT_STATUS_SUCCESS, self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED
     */
    protected static function purge(target_user $user, \context $context) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/glossary/lib.php');
        require_once($CFG->dirroot . '/mod/glossary/rsslib.php');
        require_once($CFG->dirroot . '/rating/lib.php');
        require_once($CFG->dirroot . '/comment/lib.php');

        $fs = get_file_storage();
        $rm = new \rating_manager();
        $glossaries = array();

        // Remove original exported items first.
        $join = self::get_activities_join($context, 'glossary', 'e.sourceglossaryid', 'g');
        $sql = "SELECT e.*, cm.id AS cmid, cm.idnumber AS cmidnumber
                  FROM {glossary_entries} e
                 $join
                 WHERE e.userid = :userid
              ORDER BY e.id";
        $entries = $DB->get_recordset_sql($sql, array('userid' => $user->id));
        foreach ($entries as $entry) {
            if (!isset($glossaries[$entry->sourceglossaryid])) {
                $glossaries[$entry->sourceglossaryid] = array('id' => $entry->sourceglossaryid, 'cmid' => $entry->cmid, 'cmidnumber' => $entry->cmidnumber, 'reset' => 0);
            }
            $glossarycontext = \context_module::instance($entry->cmid);

            // Delete ratings.
            $rm->delete_ratings((object)array('contextid' => $glossarycontext->id, 'component' => 'mod_glossary', 'ratingarea' => 'entry', 'itemid' => $entry->id));

            // Delete comments.
            \comment::delete_comments(array('contextid' => $glossarycontext->id, 'component' => 'mod_glossary', 'commentarea' => 'glossary_entry', 'itemid' => $entry->id));

            if ($entry->usedynalink and $entry->approved) {
                $glossaries[$entry->sourceglossaryid]['reset'] = 1;
            }
            // Exported entries are a mess, better not trigger anything here.
            $DB->set_field('glossary_entries', 'sourceglossaryid', 0, array('id' => $entry->id));
        }
        $entries->close();

        $join = self::get_activities_join($context, 'glossary', 'e.glossaryid', 'g');
        $sql = "SELECT e.*, cm.id AS cmid, cm.idnumber AS cmidnumber
                  FROM {glossary_entries} e
                 $join
                 WHERE e.userid = :userid
              ORDER BY e.id";
        $entries = $DB->get_recordset_sql($sql, array('userid' => $user->id));
        foreach ($entries as $entry) {
            if (!isset($glossaries[$entry->glossaryid])) {
                $glossaries[$entry->glossaryid] = array('id' => $entry->glossaryid, 'cmid' => $entry->cmid, 'cmidnumber' => $entry->cmidnumber, 'reset' => 0);
            }
            $glossarycontext = \context_module::instance($entry->cmid);

            // Delete ratings.
            $rm->delete_ratings((object)array('contextid' => $glossarycontext->id, 'component' => 'mod_glossary', 'ratingarea' => 'entry', 'itemid' => $entry->id));

            // Delete comments.
            \comment::delete_comments(array('contextid' => $glossarycontext->id, 'component' => 'mod_glossary', 'commentarea' => 'glossary_entry', 'itemid' => $entry->id));

            $fs->delete_area_files($glossarycontext->id, 'mod_glossary', 'attachment', $entry->id);
            $DB->delete_records('glossary_alias', array('entryid' => $entry->id));
            $DB->delete_records('glossary_entries', array('id' => $entry->id));

            if ($entry->usedynalink and $entry->approved) {
                $glossaries[$entry->glossaryid]['reset'] = 1;
            }

            // Trigger event.
            unset($entry->cmid);
            unset($entry->cmidnumber);
            $event = \mod_glossary\event\entry_deleted::create(array(
                'context' => $context,
                'objectid' => $entry->id,
                'other' => array(
                    'concept' => $entry->concept
                )
            ));
            $event->add_record_snapshot('glossary_entries', $entry);
            $event->trigger();
        }
        $entries->close();

        foreach ($glossaries as $gdata) {
            $glossary = $DB->get_record('glossary', array('id' => $gdata['id']));
            $glossary->cmidnumber = $gdata['cmidnumber'];
            $course = $DB->get_record('course', array('id' => $glossary->course));
            $cm = $DB->get_record('course_modules', array('id' => $gdata['cmid']));

            // Purge concept cache if necessary.
            if ($gdata['reset']) {
                \mod_glossary\local\concept_cache::reset_glossary($glossary);
            }

            // Delete cached RSS feeds.
            if (!empty($CFG->enablerssfeeds)) {
                glossary_rss_delete_file($glossary);
            }

            if ($user->status !== $user::STATUS_DELETED) {
                // Update completion state.
                $completion = new \completion_info($course);
                if ($completion->is_enabled($cm) == COMPLETION_TRACKING_AUTOMATIC && $glossary->completionentries) {
                    $completion->update_state($cm, COMPLETION_INCOMPLETE, $user->id);
                }

                // Regrade if assessed.
                if ($glossary->assessed) {
                    glossary_update_grades($glossary, $user->id, true);
                }
            }
        }

        return self::RESULT_STATUS_SUCCESS;
    }

    /**
     * Can user data of this item data be exported from the system?
     *
     * @return bool
     */
    public static function is_exportable() {
        return true;
    }

    /**
     * Export user data from this item.
     *
     * @param target_user $user
     * @param \context $context restriction for exporting i.e., system context for everything and course context for course export
     * @return \totara_userdata\userdata\export|int result object or integer error code self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED
     */
    protected static function export(target_user $user, \context $context) {
        global $DB;

        $fs = get_file_storage();

        $export = new \totara_userdata\userdata\export();

        $join = self::get_activities_join($context, 'glossary', 'e.glossaryid', 'g');
        $sql = "SELECT e.*, cm.id AS cmid
                  FROM {glossary_entries} e
                 $join
                 WHERE e.userid = :userid
              ORDER BY e.id";
        $entries = $DB->get_recordset_sql($sql, array('userid' => $user->id));
        foreach ($entries as $entry) {
            $e = array('id' => $entry->id, 'glossaryid' => $entry->glossaryid, 'concept' => $entry->concept, 'definition' => $entry->definition);
            $filecontext = \context_module::instance($entry->cmid, IGNORE_MISSING);
            if ($filecontext) {
                $files = $fs->get_area_files($filecontext->id, 'mod_glossary', 'entry', $entry->id, "timemodified", false);
                if ($files) {
                    $e['files'] = array();
                    foreach ($files as $file) {
                        if ($file->get_userid() != $user->id) {
                            continue;
                        }
                        $e['files'][] = array('id' => $file->get_id(), 'filename' => $file->get_filepath() . $file->get_filename());
                        $export->files[] = $file;
                    }
                }
                if ($entry->attachment) {
                    $files = $fs->get_area_files($filecontext->id, 'mod_glossary', 'attachment', $entry->id, "timemodified", false);
                    if ($files) {
                        $e['attachments'] = array();
                        foreach ($files as $file) {
                            if ($file->get_userid() != $user->id) {
                                continue;
                            }
                            $e['attachments'][] = array('id' => $file->get_id(), 'filename' => $file->get_filepath() . $file->get_filename());
                            $export->files[] = $file;
                        }
                    }
                }
            }
            $export->data[] = $e;
        }
        $entries->close();

        // There are no files in exported entries, also ignore any potential duplication of exported entries in export.
        $join = self::get_activities_join($context, 'glossary', 'e.sourceglossaryid', 'g');
        $sql = "SELECT e.*, cm.id AS cmid
                  FROM {glossary_entries} e
                 $join
                 WHERE e.userid = :userid
              ORDER BY e.id";
        $entries = $DB->get_recordset_sql($sql, array('userid' => $user->id));
        foreach ($entries as $entry) {
            $e = array('id' => $entry->id, 'glossaryid' => $entry->glossaryid, 'concept' => $entry->concept, 'definition' => $entry->definition);
            $export->data[] = $e;
        }
        $entries->close();

        return $export;
    }

    /**
     * Can user data of this item be somehow counted?
     *
     * @return bool
     */
    public static function is_countable() {
        return true;
    }

    /**
     * Count user data for this item.
     *
     * @param target_user $user
     * @param \context $context restriction for counting i.e., system context for everything and course context for course data
     * @return int  integer is the count >= 0, negative number is error result self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED
     */
    protected static function count(target_user $user, \context $context) {
        global $DB;

        $join = self::get_activities_join($context, 'glossary', 'e.glossaryid', 'g');
        $sql = "SELECT COUNT(e.id)
                  FROM {glossary_entries} e
                 $join
                 WHERE e.userid = :userid";
        $count = (int)$DB->count_records_sql($sql, array('userid' => $user->id));

        // Exported entries.
        $join = self::get_activities_join($context, 'glossary', 'e.sourceglossaryid', 'g');
        $sql = "SELECT COUNT(e.id)
                  FROM {glossary_entries} e
                 $join
                 WHERE e.userid = :userid";
        return $count + (int)$DB->count_records_sql($sql, array('userid' => $user->id));
    }
}