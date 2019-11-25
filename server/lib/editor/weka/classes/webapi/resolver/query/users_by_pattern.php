<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package editor_weka
 */
namespace editor_weka\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;

/**
 * Searching users by pattern.
 */
final class users_by_pattern implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return \stdClass[]
     */
    public static function resolve(array $args, execution_context $ec): array {
        global $USER;
        require_login();

        // For now we will work with \core_message\api to find the users - however, there should be
        // a generic API to search for the users in future - and which it should be used in here.
        [$contacts, $courses, $non_contacts] = \core_message\api::search_users($USER->id, $args['pattern'], 20);
        $user_contacts = array_merge($contacts, $non_contacts);

        $result_records = [];
        $user_name_fields = get_all_user_name_fields();

        // Reset on keys.
        $user_name_fields = array_values($user_name_fields);

        // Adding more additional fields in order to make resolver work.
        $user_name_fields[] = 'picture';
        $user_name_fields[] = 'imagealt';
        $user_name_fields[] = 'email';

        $user_name_fields = implode(", ", $user_name_fields);

        foreach ($user_contacts as $user_contact) {
            // The contact record does not have the information we need.
            $user_record = clone $user_contact;
            $user_record->id = $user_contact->userid;

            $field_record = \core_user::get_user($user_record->id, $user_name_fields, MUST_EXIST);
            $field_attributes = get_object_vars($field_record);

            foreach ($field_attributes as $field_attribute => $value) {
                $user_record->{$field_attribute} = $value;
            }

            $result_records[] = $user_record;
        }

        return $result_records;
    }
}
