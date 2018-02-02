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
 * Glossary comments user content.
 */
class comments extends \totara_userdata\userdata\item {
    /**
     * String used for human readable name of this item.
     *
     * @return array parameters of get_string($identifier, $component) to get full item name and optionally help.
     */
    public static function get_fullname_string() {
        return ['comments', 'core'];
    }

    /**
     * Returns sort order.
     *
     * @return int
     */
    public static function get_sortorder() {
        return 200;
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
        global $DB;

        $join = self::get_activities_join($context, 'glossary', 'e.glossaryid', 'g');
        $sql = "SELECT DISTINCT c.itemid, c.contextid
                  FROM {comments} c
                  JOIN {glossary_entries} e ON e.id = c.itemid 
                 $join
                 WHERE c.userid = :userid AND c.component = 'mod_glossary' AND c.commentarea = 'glossary_entry' AND c.contextid = ctx.id";
        $comments = $DB->get_recordset_sql($sql, array('userid' => $user->id));
        foreach ($comments as $comment) {
            $DB->delete_records('comments', array('userid' => $user->id, 'component' => 'mod_glossary',
                'commentarea' => 'glossary_entry', 'itemid' => $comment->itemid, 'contextid' => $comment->contextid));
        }
        $comments->close();

        // Exported entries.
        $join = self::get_activities_join($context, 'glossary', 'e.sourceglossaryid', 'g');
        $sql = "SELECT DISTINCT c.itemid, c.contextid
                  FROM {comments} c
                  JOIN {glossary_entries} e ON e.id = c.itemid 
                 $join
                 WHERE c.userid = :userid AND c.component = 'mod_glossary' AND c.commentarea = 'glossary_entry' AND c.contextid = ctx.id";
        $comments = $DB->get_recordset_sql($sql, array('userid' => $user->id));
        foreach ($comments as $comment) {
            $DB->delete_records('comments', array('userid' => $user->id, 'component' => 'mod_glossary',
                'commentarea' => 'glossary_entry', 'itemid' => $comment->itemid, 'contextid' => $comment->contextid));
        }
        $comments->close();

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

        $export = new \totara_userdata\userdata\export();

        $join = self::get_activities_join($context, 'glossary', 'e.glossaryid', 'g');
        $sql = "SELECT c.*, g.id AS glossaryid
                  FROM {comments} c
                  JOIN {glossary_entries} e ON e.id = c.itemid 
                 $join
                 WHERE c.userid = :userid AND c.component = 'mod_glossary' AND c.commentarea = 'glossary_entry' AND c.contextid = ctx.id
              ORDER BY c.id";
        $comments = $DB->get_recordset_sql($sql, array('userid' => $user->id));
        foreach ($comments as $comment) {
            $export->data[] = array('glossaryid' => $comment->glossaryid, 'entryid' => $comment->itemid, 'content' => $comment->content);;
        }

        // Exported entries.
        $join = self::get_activities_join($context, 'glossary', 'e.sourceglossaryid', 'g');
        $sql = "SELECT c.*, g.id AS glossaryid
                  FROM {comments} c
                  JOIN {glossary_entries} e ON e.id = c.itemid 
                 $join
                 WHERE c.userid = :userid AND c.component = 'mod_glossary' AND c.commentarea = 'glossary_entry' AND c.contextid = ctx.id
              ORDER BY c.id";
        $comments = $DB->get_recordset_sql($sql, array('userid' => $user->id));
        foreach ($comments as $comment) {
            $export->data[] = array('glossaryid' => $comment->glossaryid, 'entryid' => $comment->itemid, 'content' => $comment->content);;
        }

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
        $sql = "SELECT COUNT(c.id)
                  FROM {comments} c
                  JOIN {glossary_entries} e ON e.id = c.itemid 
                 $join
                 WHERE c.userid = :userid AND c.component = 'mod_glossary' AND c.commentarea = 'glossary_entry' AND c.contextid = ctx.id";
        $count = (int)$DB->count_records_sql($sql, array('userid' => $user->id));

        // Exported entries.
        $join = self::get_activities_join($context, 'glossary', 'e.sourceglossaryid', 'g');
        $sql = "SELECT COUNT(c.id)
                  FROM {comments} c
                  JOIN {glossary_entries} e ON e.id = c.itemid 
                 $join
                 WHERE c.userid = :userid AND c.component = 'mod_glossary' AND c.commentarea = 'glossary_entry' AND c.contextid = ctx.id";

        return $count + (int)$DB->count_records_sql($sql, array('userid' => $user->id));
    }
}