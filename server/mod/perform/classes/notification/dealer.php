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
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\participant_instance as participant_instance_model;

/**
 * The dealer class.
 */
class dealer {
    /** @var participant_instance_model[] */
    private $participant_instances;

    /**
     * Constructor. *Do not instantiate this class directly. Use the factory class.*
     *
     * @param participant_instance_model[] $participant_instances
     * @throws coding_exception
     * @internal
     */
    public function __construct(array $participant_instances) {
        if (!empty(array_filter($participant_instances, function ($x) {
            return !($x instanceof participant_instance_model);
        }))) {
            throw new coding_exception('participant_instances must be an array of participant_instance models');
        }
        $this->participant_instances = $participant_instances;
    }

    /**
     * Fire a notification associated to the class key.
     *
     * @param string $class_key
     * @throws coding_exception
     */
    public function dispatch(string $class_key): void {
        $subject_instance_id = false;
        $mailer = null;

        foreach ($this->participant_instances as $instance) {
            $user = $instance->get_participant();
            $activity = $instance->get_subject_instance()->get_activity();

            // Do not send any notification if the recipient is not a participant
            // in the same tenant as the activity. They won't be able to access the activity anymore.
            // Also skip deleted users
            if ($user->is_internal()
                && (
                    $activity->get_context()->is_user_access_prevented($user->id)
                    || $instance->is_subject_or_participant_deleted()
                )
            ) {
                continue;
            }

            if ($instance->subject_instance_id !== $subject_instance_id) {
                $notification = notification_model::load_by_activity_and_class_key($activity, $class_key);
                $mailer = factory::create_mailer_on_notification($notification);
                $subject_instance_id = $instance->subject_instance_id;
            }
            if (!$mailer) {
                // The notification is not active, recipients are not set, etc.
                continue;
            }
            $relationship = $instance->get_core_relationship();
            $placeholders = placeholder::from_participant_instance($instance);
            $mailer->post($user, $relationship, $placeholders);
        }
    }
}
