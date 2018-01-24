<?php
/*
 * This file is part of Totara LMS
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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_cohort\rb\display;

/**
 * Class describing column display formatting.
 */
class cohort_association_duedate extends \totara_reportbuilder\rb\display\base {
    public static function display($value, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        $extrafields = self::get_extrafields_row($row, $column);

        $type = empty($extrafields->type) ? 0 : $extrafields->type;
        $programtypes = [COHORT_ASSN_ITEMTYPE_PROGRAM , COHORT_ASSN_ITEMTYPE_CERTIF];
        if (!in_array($type, $programtypes)) {
            return \get_string('na', 'totara_cohort');
        }

        $programid = empty($extrafields->programid) ? null : $extrafields->programid;
        $item = [
            "completiontime" => $value,
            "completionevent" => empty($extrafields->completionevent) ? null : $extrafields->completionevent,
            "completioninstance" => empty($extrafields->completioninstance) ? null : $extrafields->completioninstance,
            "id" => empty($extrafields->cohortid) ? null : $extrafields->cohortid
        ];

        $cat = new \cohorts_category();
        $text = $cat->get_completion((object)$item, $programid, false);
        return $format === 'html' ? $text : static::to_plaintext($text);
    }

    public static function is_graphable(\rb_column $column, \rb_column_option $option, \reportbuilder $report) {
        return false;
    }
}
