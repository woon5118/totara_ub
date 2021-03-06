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
 * @author David Curry <david.curry@totaralearning.com>
 * @package totara_hierarchy
 */

namespace hierarchy_goal\userdata;

use totara_userdata\userdata\target_user;
use hierarchy_goal\userdata\company_helper as helper;

defined('MOODLE_INTERNAL') || die();

/**
 * User's company goal assignment content.
 */
class company_export_visible extends \totara_userdata\userdata\item {

    /**
     * Returns sort order.
     *
     * @return int
     */
    public static function get_sortorder() {
        return 210;
    }

    /**
     * Can user data of this item data be purged from system?
     *
     * @param int $userstatus target_user::STATUS_ACTIVE, target_user::STATUS_DELETED or target_user::STATUS_SUSPENDED
     * @return bool
     */
    public static function is_purgeable(int $userstatus) {
        return false;
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
     * @param \context $context
     * @return \totara_userdata\userdata\export|int result object or integer error code self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED
     */
    protected static function export(target_user $user, \context $context) {
        $capcontext = \context_system::instance();
        if ($user->status != target_user::STATUS_DELETED) {
            $capcontext = \context_user::instance($user->id);
        }

        if (!has_capability('totara/hierarchy:viewowncompanygoal', $capcontext, $user)) {
           return new \totara_userdata\userdata\export();
        }

        return helper::export($user, $context);
    }

    /**
     * Can user data of this item be counted?
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
     * @param \context $context
     * @return int amount of data or negative integer status code (self::RESULT_STATUS_ERROR or self::RESULT_STATUS_SKIPPED)
     */
    protected static function count(target_user $user, \context $context) {
        $capcontext = \context_system::instance();
        if ($user->status != target_user::STATUS_DELETED) {
            $capcontext = \context_user::instance($user->id);
        }

        if (!has_capability('totara/hierarchy:viewowncompanygoal', $capcontext, $user)) {
           return 0;
        }

        return helper::count($user, $context);
    }
}
