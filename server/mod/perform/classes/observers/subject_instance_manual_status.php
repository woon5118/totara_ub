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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\observers;

use core\collection;
use core\event\base;
use mod_perform\entities\activity\subject_instance;
use mod_perform\event\subject_instance_activated;
use mod_perform\notification\factory;
use mod_perform\task\service\participant_instance_creation;
use mod_perform\task\service\subject_instance_dto;

class subject_instance_manual_status {

    /**
     * React to a subject instance being activated.
     *
     * @param base|subject_instance_activated $event
     */
    public static function subject_instance_activated(base $event): void {
        $subject_instance = new subject_instance($event->objectid);
        $subject_instance_dto = subject_instance_dto::create_from_entity($subject_instance);

        self::generate_instances($subject_instance_dto);
        self::trigger_notifications($subject_instance_dto);
    }

    /**
     * Create participant instances.
     *
     * @param subject_instance_dto $subject_instance_dto
     */
    private static function generate_instances(subject_instance_dto $subject_instance_dto): void {
        $subject_instance_dto_collection = new collection([$subject_instance_dto]);
        (new participant_instance_creation())
            ->generate_instances($subject_instance_dto_collection);
    }

    /**
     * Trigger notifications to be sent to the participant users.
     *
     * @param subject_instance_dto $subject_instance_dto
     */
    private static function trigger_notifications(subject_instance_dto $subject_instance_dto): void {
        $cartel = factory::create_cartel($subject_instance_dto);
        $cartel->dispatch('instance_created');
    }

}
