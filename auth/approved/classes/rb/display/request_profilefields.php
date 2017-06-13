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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 *
 * @package auth_approved
 */

namespace auth_approved\rb\display;

use \totara_reportbuilder\rb\display\base;

final class request_profilefields extends base {
    public static function display($value, $format, \stdClass $row, \rb_column $column, \reportbuilder $report) {
        // TODO - display all sign up profile fields values the same way as on user profile page
        return 'TODO';

        /*
        global $OUTPUT;

        // Retrieve the extra row data.
        $extra = self::get_extrafields_row($row, $column);

        if ($format == 'html') {
            return $OUTPUT->action_link(new moodle_url('/mod/facetoface/view.php', array('f' => $extra->activity_id)), $value);
        } else {
            return $value;
        }


        $customfields = json_decode($value);

            foreach ($customfields as $name => $value) {
                if ($value === null) {
                    continue; // Null means "don't update the existing data", so skip this field.
                }

                if ($value === "" && !$saveemptyfields) {
                    continue; // CSV import and empty fields are not saved, so skip this field.
                }

                $profile = str_replace('customfield_', 'profile_field_', $name);
                // If the custom field is a menu, the option index will be set by function totara_sync_data_preprocess.
                $user->{$profile} = $value;
            }
         */
    }
}
