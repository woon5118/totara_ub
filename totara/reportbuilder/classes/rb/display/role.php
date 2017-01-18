<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @author Rob Tyler <rob.tyler@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder\rb\display;

/**
 * Class describing column display formatting for columns using the select_multi_data filter.
 */
class role extends base {

    /*
     * @var array Cache the system roles to make the report more efficient.
     */
    private static $systemroles = array();

    /*
     * Caches and returns a list of system roles.
     *
     * @return array Return a list of cached system roles.
     */
    private static function get_assignable_roles() {
        if (!self::$systemroles) {
            self::$systemroles = get_assignable_roles(\context_system::instance());
        }

        return self::$systemroles;
    }

    /*
     * Reset the system roles for testing.
     */
    private static function set_assignable_roles() {
        self::$systemroles = null;
    }

    /**
     * Displays the systems roles for the user.
     *
     * @param string $value
     * @param string $format
     * @param \stdClass $row
     * @param \rb_column $column
     * @param \reportbuilder $report
     * @return string
     */
    public static function display($value, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        if ($value) {
            $systemroles = self::get_assignable_roles();

            $roles = trim($value, '|');
            $roles = explode('|', $roles);
            $names = array();

            foreach ($roles as $role) {
                $names[] = $systemroles[$role];
            }

            // We need to sort the result to stop Behat tests from failing.
            sort($names);

            $value = implode(', ', $names);
        }

        return $value;
    }

    /**
     * Can the result the display method be used for a graph series?
     *
     * @param \rb_column $column
     * @param \rb_column_option $option
     * @param \reportbuilder $report
     * @return bool
     */
    public static function is_graphable(\rb_column $column, \rb_column_option $option, \reportbuilder $report) {
        return false;
    }
}
