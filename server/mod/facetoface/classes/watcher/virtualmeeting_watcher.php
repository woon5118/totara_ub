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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface\watcher;

use core\task\manager as task_manager;
use mod_facetoface\hook\resources_are_being_updated;
use mod_facetoface\task\manage_virtualmeetings_adhoc_task;

final class virtualmeeting_watcher {

    /**
     * @param resources_are_being_updated $hook
     */
    public static function resources_updated(resources_are_being_updated $hook) {
        $task = manage_virtualmeetings_adhoc_task::create_from_seminar_event_id($hook->seminarevent->get_id());
        task_manager::queue_adhoc_task($task);
    }
}
