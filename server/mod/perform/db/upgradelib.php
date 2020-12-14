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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

/**
 * Create any notification & notification recipient records that do not already exist for existing activities.
 *
 * @param array $notifications An array of class_key => default_trigger, defines what notifications to create records for.
 */
function mod_perform_upgrade_create_missing_notification_records(array $notifications) {
    global $DB;

    $notification_class_keys = array_keys($notifications);
    $time = time();
    $transaction = $DB->start_delegated_transaction();

    // This corresponds to the relationships that can be used for perform, the IDs fetched here should match what is fetched in
    // \mod_perform\models\activity\helpers\relationship_helper::get_supported_perform_relationships() (or equivalent)
    $relationship_ids = $DB->get_fieldset_sql("
        SELECT id
        FROM {totara_core_relationship}
        WHERE component IS NULL
        OR component = :component
    ", ['component' => 'mod_perform']);

    $activity_ids = $DB->get_fieldset_select('perform', 'id', '1 = 1');

    $notifications_to_insert = [];

    foreach ($activity_ids as $activity_id) {
        $existing_notifications = $DB->get_records_sql("
            SELECT notification.id, notification.class_key
            FROM {perform_notification} notification
            INNER JOIN {perform} activity
            ON notification.activity_id = activity.id
            WHERE activity.id = :activity_id
        ", ['activity_id' => $activity_id]);
        $existing_notification_class_keys = array_column($existing_notifications, 'class_key');
        $missing_notifications_class_keys = array_diff($notification_class_keys, $existing_notification_class_keys);
        $existing_notification_ids = array_column($existing_notifications, 'id');

        // Create missing notifications
        foreach ($missing_notifications_class_keys as $class_key) {
            $notifications_to_insert[] = (object) [
                'activity_id' => $activity_id,
                'class_key' => $class_key,
                'active' => 0, // Notification should always be disabled
                'triggers' => json_encode($notifications[$class_key], JSON_UNESCAPED_SLASHES),
                'created_at' => $time,
            ];
        }
    }

    $DB->insert_records_via_batch('perform_notification', $notifications_to_insert);

    $recipients_to_insert = [];

    foreach ($activity_ids as $activity_id) {
        // Create missing recipient records
        $existing_notification_ids = $DB->get_fieldset_select(
            'perform_notification', 'id', 'activity_id = :activity_id', ['activity_id' => $activity_id]
        );
        $existing_recipient_relationships = $DB->get_records_sql("
            SELECT recipient.id, recipient.core_relationship_id, notification.id AS notification_id
            FROM {perform_notification_recipient} recipient
            INNER JOIN {perform_notification} notification
            ON recipient.notification_id = notification.id
            WHERE notification.activity_id = :activity_id
        ", ['activity_id' => $activity_id]);
        $existing_recipient_relationships_map = [];
        foreach ($existing_recipient_relationships as $record) {
            $existing_recipient_relationships_map[$record->notification_id][] = $record->core_relationship_id;
        }

        foreach ($existing_notification_ids as $notification_id) {
            foreach ($relationship_ids as $relationship_id) {
                if (isset($existing_recipient_relationships_map[$notification_id]) &&
                    in_array($relationship_id, $existing_recipient_relationships_map[$notification_id])) {
                    continue;
                }
                $recipients_to_insert[] = (object) [
                    'notification_id' => $notification_id,
                    'core_relationship_id' => $relationship_id,
                    'active' => 0, // Recipient should always be disabled
                ];
            }
        }
    }

    $DB->insert_records_via_batch('perform_notification_recipient', $recipients_to_insert);

    $transaction->allow_commit();
}

/**
 * Unwraps element_response.response_data json, to simple json encoded strings.
 * This removed the need for unwrapping code in client side components and server side validation and formatting.
 *
 * answer_text: long_text, short_text
 * answer_value: numeric_rating_scale
 * answer_option: custom_rating_scale, multi_choice_single, multi_choice_multi
 * date: date_picker
 */
function mod_perform_upgrade_unwrap_response_data() {
    global $DB;

    $possible_wrapping_fields = ['answer_text', 'answer_option', 'date', 'answer_value'];

    $existing_responses = $DB->get_recordset_select('perform_element_response', "response_data <> 'null'");
    foreach ($existing_responses as $existing_response) {
        $decoded_response_data = json_decode($existing_response->response_data, true);

        if (!is_array($decoded_response_data)) {
            continue;
        }

        $unwrapped = null;

        foreach ($possible_wrapping_fields as $possible_wrapping_field) {
            if (array_key_exists($possible_wrapping_field, $decoded_response_data)) {
                $unwrapped = $decoded_response_data[$possible_wrapping_field];
                break;
            }
        }

        if ($unwrapped) {
            $unwrapped_encoded = json_encode($unwrapped);

            $DB->set_field(
                'perform_element_response',
                'response_data',
                $unwrapped_encoded,
                ['id' => $existing_response->id]
            );
        }
    }
}

/**
 * Converts any existing long text responses to the new Weka JSON format.
 */
function mod_perform_upgrade_long_text_responses_to_weka_format() {
    global $DB;
    $transaction = $DB->start_delegated_transaction();
    $responses = $DB->get_recordset_sql("
        SELECT response.id, response.response_data
        FROM {perform_element_response} response
        INNER JOIN {perform_section_element} section_element ON response.section_element_id = section_element.id
        INNER JOIN {perform_element} element ON section_element.element_id = element.id
        WHERE element.plugin_name = 'long_text'
    ");

    // Wrap the text from each response in the proper Weka JSON
    foreach ($responses as $response) {
        if (empty($response->response_data)) {
            // Response is completely empty, so don't need to do anything.
            continue;
        }

        if ($response->response_data === 'null') {
            // A string with the value 'null' that is not encoded with JSON is invalid, and will cause problems.
            // Because it isn't encoded with JSON, it wouldn't have been entered in by the user and is safe to delete.
            $DB->set_field('perform_element_response', 'response_data', null, ['id' => $response->id]);
            continue;
        }

        $response_text = json_decode($response->response_data);
        if (!is_string($response_text)) {
            // Response has already been converted into Weka JSON
            continue;
        }

        $text_elements = [];

        // Analyse the response string and insert breaks where there are newline characters
        $unbroken_string = '';
        $response_length = strlen($response_text);
        for ($i = 0; $i < $response_length; $i++) {
            $char = $response_text[$i];

            if ($char === "\n" || $char === "\r") {
                if (!empty($unbroken_string)) {
                    $text_elements[] = ['type' => 'text', 'text' => $unbroken_string];
                    $unbroken_string = '';
                }
                $text_elements[] = ['type' => 'hard_break'];
                continue;
            }

            $unbroken_string .= $char;
        }
        $text_elements[] = ['type' => 'text', 'text' => $unbroken_string];

        $weka_response = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => $text_elements,
                ],
            ],
        ];

        // Must encode it in the same way javascript does with JSON.stringify()
        // Must be equivalent to \core\json_editor\helper\document_helper::json_encode_document()
        $encoded_response = json_encode(
            $weka_response,
            JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        $DB->set_field(
            'perform_element_response',
            'response_data',
            $encoded_response,
            ['id' => $response->id]
        );
    }

    $transaction->allow_commit();
}

/**
 * Removes leftover static_content element files for deleted activities.
 */
function mod_perform_upgrade_remove_obsolete_static_content_element_files() {
    global $DB;

    /*
     * Find files for the 'performelement_static_content' component that neither have a corresponding activity
     * nor a corresponding element.
     */
    $query = "SELECT f. id, f.itemid, f.contextid
                FROM {files} f
                JOIN {context} c ON (f.contextid = c.id AND c.contextlevel = :course_module_level)
           LEFT JOIN {perform_element} e ON (f.contextid = e.context_id AND f.itemid = e.id)
           LEFT JOIN {perform} p ON p.course = c.instanceid
               WHERE f.component = :component
                 AND e.id IS NULL
                 AND p.id IS NULL";

    $component = 'performelement_static_content';

    $params = [
        'course_module_level' => CONTEXT_MODULE,
        'component' => $component,
    ];

    $records = $DB->get_records_sql($query, $params);
    foreach ($records as $record) {
        get_file_storage()->delete_area_files($record->contextid, $component, false, $record->itemid);
    }
}
