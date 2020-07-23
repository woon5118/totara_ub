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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\watcher;

use mod_perform\hook\subject_instances_created;
use mod_perform\notification\factory;
use mod_perform\state\subject_instance\pending;
use mod_perform\task\service\subject_instance_dto;

class notification {
    public static function create_subject_instances(subject_instances_created $hook): void {
        foreach ($hook->get_dtos() as $dto) {
            /** @var subject_instance_dto $dto */
            if ($dto->status === pending::get_code()) {
                // Don't dispatch notifications until the instance is activated. Once it is activated,
                // notifications are dispatched in \mod_perform\observers\subject_instance_manual_status::subject_instance_activated
                continue;
            }

            $cartel = factory::create_cartel($dto);
            $cartel->dispatch('instance_created');
        }
    }
}
