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
