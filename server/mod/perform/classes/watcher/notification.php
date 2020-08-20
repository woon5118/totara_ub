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

use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\hook\participant_instances_created;
use mod_perform\notification\factory;

class notification {
    /**
     * @param participant_instances_created $hook
     */
    public static function create_participant_instances(participant_instances_created $hook): void {
        $participant_instances = participant_instance_entity::repository()->where_in('id', $hook->get_dtos()->map_to(function ($dto) {
            return $dto->id;
        })->all())->get()->all();
        $dealer = factory::create_dealer_on_participant_instances($participant_instances);
        $dealer->dispatch('instance_created');
    }
}
