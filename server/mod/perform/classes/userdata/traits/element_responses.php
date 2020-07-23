<?php
/*
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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_perform
*/

namespace mod_perform\userdata\traits;

trait element_responses {

    /**
     * Extract the data from json and get the values according their keys
     *
     * @param \moodle_recordset $recordset
     * @return array
     */
    protected static function format_answers(\moodle_recordset $recordset): array {
        $data = [];
        foreach ($recordset as $record) {
            if ($record->plugin_name == 'multi_choice_single' && !empty($record->response_data)) {
                $record = self::multi_choice_single($record);
            } else if ($record->plugin_name == 'multi_choice_multi' && !empty($record->response_data)) {
                $record = self::multi_choice_multi($record);
            }
            unset($record->data);
            unset($record->plugin_name);
            $data[] = (array)$record;
        }
        return $data;
    }

    /**
     * Extract the multi_choice_single data from json and get the values according their keys
     *
     * @param \stdClass $record
     * @return \stdClass
     */
    private static function multi_choice_single(\stdClass $record): \stdClass {
        $data = json_decode($record->data, true);
        $response_data = json_decode($record->response_data, true);
        foreach ($data['options'] as $i => $option) {
            if ($option['name'] == $response_data['answer_option']) {
                $record->response_data = $option['value'];
                break;
            }
        }
        return $record;
    }

    /**
     * Extract the multi_choice_multi data from json and get the values according their keys
     *
     * @param \stdClass $record
     * @return \stdClass
     */
    private static function multi_choice_multi(\stdClass $record): \stdClass {
        $data = json_decode($record->data, true);
        $response_data = json_decode($record->response_data, true);
        $responses = [];
        foreach ($data['options'] as $i => $option) {
            foreach ($response_data['answer_option'] as $answer_option) {
                if ($option['name'] == $answer_option) {
                    $responses[] = $option['value'];
                }
            }
        }
        $record->response_data = $responses;
        return $record;
    }
}