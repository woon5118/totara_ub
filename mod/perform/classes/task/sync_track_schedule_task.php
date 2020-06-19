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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\task;

use core\task\scheduled_task;
use mod_perform\task\service\track_schedule_sync;

/**
 * Synchronise track schedule updates to track_user_assignments.
 */
class sync_track_schedule_task extends scheduled_task {

    public function get_name() {
        return get_string('sync_track_schedule_task', 'mod_perform');
    }

    public function execute() {
        $track_schedule_sync = new track_schedule_sync();
        $track_schedule_sync->sync_all();
    }

}
