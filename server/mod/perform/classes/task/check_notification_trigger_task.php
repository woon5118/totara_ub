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
use mod_perform\models\activity\details\subject_instance_notification;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\notification_recipient as notification_recipient_model;
use mod_perform\notification\factory;
use mod_perform\notification\loader;
use mod_perform\notification\triggerable;
use mod_perform\state\activity\active;
use totara_core\entities\relationship as relationship_entity;

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

        $clock = factory::create_clock();
        $activities = activity_entity::repository()->where('status', active::get_code())->get();
        foreach ($activities as $activity_entity) {
            /** @var activity_entity $activity_entity */
            // TODO: grab all necessities with a single giant query outside of the outer loop.
            $records = subject_instance_notification::load_by_activity($activity_entity);
            if (empty($records)) {
                continue;
            }
            foreach ($class_keys as $class_key) {
                $broker = factory::create_broker($class_key);
                if (!($broker instanceof triggerable)) {
                    debugging(get_class($broker) . ' does not implement triggerable', DEBUG_DEVELOPER);
                    continue;
                }
                $activity = activity_model::load_by_entity($activity_entity);
                $notification = notification_model::load_by_activity_and_class_key($activity, $class_key);
                if (!$notification->active) {
                    continue;
                }
                $condition = factory::create_condition($notification);
                $recipients = notification_recipient_model::load_by_notification($notification, true);
                if (!$recipients->count()) {
                    continue;
                }
                foreach ($records as $record) {
                    /** @var triggerable $broker */
                    if (!$broker->is_triggerable_now($condition, $record)) {
                        continue;
                    }
                    $entity = subject_instance_entity::repository()->find($record->id);
                    /** @var subject_instance_entity $entity */
                    $dealer = factory::create_dealer_on_participant_instances($entity->participant_instances->all());
                    $dealer->dispatch($class_key);
                }
                $notification->set_last_run_at($clock->get_time());
            }
        }
    }
}
