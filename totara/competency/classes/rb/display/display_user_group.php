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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package tassign_competency
 */

namespace totara_competency\rb\display;

use html_writer;
use moodle_url;
use rb_column;
use reportbuilder;
use stdClass;
use totara_assignment\user_groups;
use totara_reportbuilder\rb\display\base;

// TODO: WILL CONFLICT WHEN PUTTING ASSIGNMENT CODE INTO TOTARA COMPETENCY. JUST CHECK STRINGS ARE ALL THERE.
/**
 * Display class intended for user group type
 */
class display_user_group extends base {

    /**
     * Handles the display
     *
     * @param string $value
     * @param string $format
     * @param stdClass $row
     * @param rb_column $column
     * @param reportbuilder $report
     * @return string
     */
    public static function display($value, $format, stdClass $row, rb_column $column, reportbuilder $report) {
        if (empty($value)) {
            return '';
        }

        $isexport = ($format !== 'html');

        $extrafields = self::get_extrafields_row($row, $column);

        switch ($value) {
            case user_groups::USER:
                $value = self::get_user($extrafields, $isexport);
                break;
            case user_groups::COHORT:
                $value = self::get_cohort($extrafields, $isexport);
                break;
            case user_groups::POSITION:
                $value = self::get_position($extrafields, $isexport);
                break;
            case user_groups::ORGANISATION:
                $value = self::get_organisation($extrafields, $isexport);
                break;
            default:
                throw new \coding_exception('Display function for user_group '.$value.' not implemented.');
                break;
        }

        return $value;
    }

    private static function get_user(stdClass $extrafields, bool $isexport): string {
        $value = fullname(
            (object)[
                'firstname' => $extrafields->user_firstname,
                'lastname' => $extrafields->user_lastname,
                'firstnamephonetic' => $extrafields->user_firstnamephonetic,
                'lastnamephonetic' => $extrafields->user_lastnamephonetic,
                'middlename' => $extrafields->user_middlename,
                'alternatename' => $extrafields->user_alternatename
            ]
        );
        if (!empty($extrafields->user_idnumber)) {
            $value .= " ({$extrafields->user_idnumber})";
        }
        // Don't show links in spreadsheet.
        if (!$isexport) {
            $url = new moodle_url(
                '/user/view.php',
                ['id' => $extrafields->user_id]
            );
            $value = html_writer::link($url, $value);
        }
        return $value;
    }

    /**
     * This method is called dynamically in the display method based on the user group type
     *
     * @param stdClass $extrafields
     * @param bool $isexport
     * @return string
     */
    private static function get_position(stdClass $extrafields, bool $isexport): string {
        $value = $extrafields->pos_name;
        if (!empty($extrafields->pos_idnumber)) {
            $value .= " ({$extrafields->pos_idnumber})";
        }
        if (!$isexport) {
            $url = new moodle_url(
                '/totara/hierarchy/item/view.php',
                ['prefix' => 'position', 'id' => $extrafields->pos_id]
            );
            $value = html_writer::link($url, $value);
        }
        return $value;
    }

    /**
     * This method is called dynamically in the display method based on the user group type
     *
     * @param stdClass $extrafields
     * @param bool $isexport
     * @return string
     */
    private static function get_organisation(stdClass $extrafields, bool $isexport): string {
        $value = $extrafields->org_name;
        if (!empty($extrafields->org_idnumber)) {
            $value .= " ({$extrafields->org_idnumber})";
        }
        if (!$isexport) {
            $url = new moodle_url(
                '/totara/hierarchy/item/view.php',
                ['prefix' => 'organisation', 'id' => $extrafields->org_id]
            );
            return html_writer::link($url, $value);
        }
        return $value;
    }

    /**
     * This method is called dynamically in the display method based on the user group type
     *
     * @param stdClass $extrafields
     * @param bool $isexport
     * @return string
     */
    private static function get_cohort(stdClass $extrafields, bool $isexport): string {
        $value = $extrafields->coh_name;
        if (!empty($extrafields->coh_idnumber)) {
            $value .= " ({$extrafields->coh_idnumber})";
        }
        if (!$isexport) {
            $url = new moodle_url(
                '/cohort/view.php',
                ['id' => $extrafields->coh_id]
            );
            return html_writer::link($url, $value);
        }
        return $value;
    }

    /**
     * Is this column graphable?
     *
     * @param rb_column $column
     * @param \rb_column_option $option
     * @param reportbuilder $report
     * @return bool
     */
    public static function is_graphable(rb_column $column, \rb_column_option $option, reportbuilder $report) {
        return false;
    }

}
