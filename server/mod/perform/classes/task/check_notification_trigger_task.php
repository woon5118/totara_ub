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

use core\orm\query\builder;
use core\task\scheduled_task;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_user_assignment as track_user_assignment_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\notification_recipient as notification_recipient_model;
use mod_perform\notification\factory;
use mod_perform\state\activity\active;

/**
 * Periodically check notification event triggers.
 */
class check_notification_trigger_task extends scheduled_task {
    public function get_name() {
        return get_string('check_notification_trigger_task', 'mod_perform');
    }

    public function execute() {
        // TODO: only instance_created_reminder is implemented in TL-26164.
        $class_keys = ['instance_created_reminder'];
        // $loader = factory::create_loader();
        // $class_keys = array_filter($loader->get_class_keys(), function ($class_key) use ($loader) {
        //     return $loader->support_triggers($class_key);
        // });

        $clock = factory::create_clock();
        $activities = activity_entity::repository()->where('status', active::get_code())->get();
        foreach ($class_keys as $class_key) {
            $broker = factory::create_broker($class_key);
            foreach ($activities as $activity_entity) {
                /** @var activity_entity $activity_entity */
                $activity = activity_model::load_by_entity($activity_entity);
                $notification = notification_model::load_by_activity_and_class_key($activity, $class_key);
                if (!$notification->active) {
                    continue;
                }
                $recipients = notification_recipient_model::load_by_notification($notification, true);
                if (!$recipients->count()) {
                    continue;
                }
                // TODO: grab all necessities with a single giant query outside of the loop.
                $records = builder::table(subject_instance_entity::TABLE, 'si')
                    ->join([track_user_assignment_entity::TABLE, 'tua'], 'si.track_user_assignment_id', 'tua.id')
                    ->join([track_entity::TABLE, 't'], 'tua.track_id', 't.id')
                    ->where('t.activity_id', $activity->id)
                    ->where_null('si.completed_at')
                    ->where('tua.deleted', false)
                    ->select(['si.id', 'tua.subject_user_id', 'tua.job_assignment_id'])
                    // how can I get an instance creation time?
                    ->add_select('si.created_at')
                    ->get();
                if (empty($records)) {
                    continue;
                }
                foreach ($records as $record) {
                    if (!$broker->check_trigger_condition($notification, $record, $clock)) {
                        continue;
                    }
                    $cartel = factory::create_cartel_on_user_assignment($activity, $record);
                    $cartel->dispatch($class_key);
                }
                $notification->set_last_run_time($clock->get_time());
            }
        }
    }
}
