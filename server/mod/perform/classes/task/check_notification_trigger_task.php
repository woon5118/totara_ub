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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\task;

use core\task\scheduled_task;
use mod_perform\entity\activity\activity as activity_entity;
use mod_perform\entity\activity\subject_instance as subject_instance_entity;
use mod_perform\entity\activity\track as track_entity;
use mod_perform\entity\activity\track_user_assignment as track_user_assignment_entity;
use mod_perform\models\activity\details\subject_instance_notification;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\notification_recipient as notification_recipient_model;
use mod_perform\notification\factory;
use mod_perform\notification\loader;
use mod_perform\notification\triggerable;
use mod_perform\state\activity\active;

/**
 * Periodically check notification event triggers.
 */
class check_notification_trigger_task extends scheduled_task {
    /**
     * @inheritDoc
     */
    public function get_name() {
        return get_string('check_notification_trigger_task', 'mod_perform');
    }

    /**
     * @inheritDoc
     */
    public function execute() {
        $loader = factory::create_loader();
        $class_keys = $loader->get_class_keys(loader::HAS_CONDITION);

        // Filter out invalid brokers
        $brokers = [];
        foreach ($class_keys as $class_key) {
            $broker = factory::create_broker($class_key);
            if ($broker instanceof triggerable) {
                $brokers[$class_key] = $broker;
            } else {
                debugging(get_class($broker) . ' does not implement triggerable', DEBUG_DEVELOPER);
            }
        }

        $page = 1;
        $processed_notifications = [];

        do {
            $paginator = subject_instance_entity::repository()
                ->as('si')
                ->join([track_user_assignment_entity::TABLE, 'tua'], 'si.track_user_assignment_id', 'tua.id')
                ->join([track_entity::TABLE, 't'], 'tua.track_id', 't.id')
                ->join([activity_entity::TABLE, 'a'], 't.activity_id', 'a.id')
                ->where('a.status', active::get_code())
                ->where_null('si.completed_at')
                ->where('tua.deleted', false)
                ->filter_by_active_notifications($class_keys)
                ->with('user_assignment')
                ->with('track.activity.active_notifications')
                ->with('participant_instances')
                ->paginate($page, 10000);

            foreach ($paginator->get_items() as $subject_instance) {
                foreach ($brokers as $class_key => $broker) {
                    $notification_entity = $subject_instance
                        ->activity()
                        ->active_notifications
                        ->find('class_key', $class_key);
                    // We can ignore this as there's no active one
                    if (!$notification_entity) {
                        continue;
                    }

                    $notification = notification_model::load_by_entity($notification_entity);
                    $condition = factory::create_condition($notification);
                    $recipients = notification_recipient_model::load_by_notification($notification, true);
                    if (!$recipients->count()) {
                        continue;
                    }

                    $record = subject_instance_notification::load_by_subject_instance($subject_instance);
                    if (!$broker->is_triggerable_now($condition, $record)) {
                        continue;
                    }

                    $dealer = factory::create_dealer_on_participant_instances($subject_instance->participant_instances->all());
                    $dealer->dispatch($class_key);

                    $processed_notifications[$notification->id] = $notification;
                }
            }

            $page++;
        } while ($paginator->get_next() !== null);

        $now = factory::create_clock()->get_time();
        foreach ($processed_notifications as $notification) {
            $notification->set_last_run_at($now);
        }
    }
}