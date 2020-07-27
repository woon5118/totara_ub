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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\notification;

use coding_exception;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\participant_instance as participant_instance_model;

/**
 * The cartel class.
 */
class cartel {
    /** @var integer[] */
    private $participant_instance_ids;

    /**
     * Constructor. *Do not instantiate this class directly. Use the factory class.*
     *
     * @param integer[] $participant_instance_ids
     */
    public function __construct(array $participant_instance_ids) {
        $this->participant_instance_ids = $participant_instance_ids;
    }

    /**
     * @param string $class_key
     * @throws coding_exception
     */
    public function dispatch(string $class_key): void {
        $subject_instance_id = false;
        $dealer = null;

        $entities = participant_instance_entity::repository()->where_in('id', $this->participant_instance_ids)->get()->all();
        /** @var participant_instance_entity[] $entities */
        foreach ($entities as $entity) {
            $instance = participant_instance_model::load_by_entity($entity);
            if ($instance->subject_instance_id !== $subject_instance_id) {
                $notification = notification_model::load_by_activity_and_class_key($instance->get_subject_instance()->get_activity(), $class_key);
                $dealer = factory::create_dealer_on_notification($notification);
                $subject_instance_id = $instance->subject_instance_id;
            }
            if (!$dealer) {
                // The notification is not active, recipients are not set, etc.
                continue;
            }
            $user = $instance->get_participant();
            $relationship = $instance->get_core_relationship();
            $dealer->post($user, $relationship);
        }
    }
}
