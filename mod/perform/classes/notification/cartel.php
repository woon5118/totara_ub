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
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\notification as notification_model;

/**
 * The cartel class.
 */
class cartel {
    /** @var activity_model */
    private $activity;

    /** @var integer */
    private $user_id;

    /** @var integer|null */
    private $job_assignment_id;

    public function __construct(activity_model $activity, int $user_id, ?int $job_assignment_id) {
        $this->activity = $activity;
        $this->user_id = $user_id;
        $this->job_assignment_id = $job_assignment_id;
    }

    /**
     * @param string $class_key
     * @throws coding_exception
     */
    public function dispatch(string $class_key): void {
        $notification = notification_model::load_by_activity_and_class_key($this->activity, $class_key);
        if (!$notification->active) {
            return;
        }
        // TODO: optimise out when no recipients are active
        $dealer = factory::create_dealer($notification, $this->user_id, $this->job_assignment_id);
        $broker = factory::create_broker($notification->class_key);
        $broker->execute($dealer, $notification);
    }
}
