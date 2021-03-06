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
 * @author Rob Tyler <rob.tyler@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder\rb\display;

/**
 * Class describing column display formatting for the user status column and filter.
 */
class user_status extends base {

    public static function display($value, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {

        switch($value) {
            case 1:
                $status = get_string('deleteduser', 'totara_reportbuilder');
                break;
            case 2:
                $status = get_string('suspendeduser', 'totara_reportbuilder');
                break;
            case 3:
                $status = get_string('unconfirmeduser', 'totara_reportbuilder');
                break;
            case 4:
                $status = get_string('tenantsuspended', 'totara_tenant');
                break;
            default:
                $status = get_string('activeuser', 'totara_reportbuilder');
        }

        return $status;
    }

    public static function is_graphable(\rb_column $column, \rb_column_option $option, \reportbuilder $report) {
        return false;
    }
}
