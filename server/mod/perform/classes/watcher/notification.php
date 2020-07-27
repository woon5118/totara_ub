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

use mod_perform\hook\participant_instances_created;
use mod_perform\notification\factory;

class notification {
    /**
     * @param participant_instances_created $hook
     */
    public static function create_participant_instances(participant_instances_created $hook): void {
        // FIXME: This code added to create_subject_instances() is no longer necessary.
        // FIXME: However, there is no phpunit/behat test to cover the scenario.
        // FIXME: Please remove the whole comment block once the test case(s) are added.
        // if ($subject_instance_dto->status === pending::get_code()) {
        //     // Don't dispatch notifications until the instance is activated. Once it is activated,
        //     // notifications are dispatched in \mod_perform\observers\subject_instance_manual_status::subject_instance_activated
        //     return;
        // }
        $cartel = factory::create_cartel_on_participant_instances($hook->get_dtos()->all());
        $cartel->dispatch('instance_created');
    }
}
