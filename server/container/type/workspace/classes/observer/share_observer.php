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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package container_workspace
 */
namespace container_workspace\observer;

use container_workspace\task\add_content_task;
use totara_engage\event\share_created;
use core\task\manager;
use totara_engage\share\helper;

/**
 * Observer class for share event
 */
final class share_observer {
    /**
     * share_observer constructor.
     */
    private function __construct() {
    }

    /**
     * @param share_created $event
     */
    public static function content_added(share_created $event): void {
        $others = $event->other;

        // When item does not shared to workspace, we do not need send notification.
        if ($others['area'] !== 'LIBRARY' && $others['recipient_component'] !== 'container_workspace') {
            return;
        }

        $sharer_id = $event->userid;

        $task = new add_content_task();
        $task->set_component('totara_engage');
        $task->set_custom_data(
            [
                'component' => helper::get_provider_type($others['component']),
                'workspace_id' => $others['recipient_id'],
                'sharer_id' => $sharer_id,
                'item_name' => helper::get_resource_name($others['component'], $event->objectid),
            ]
        );

        manager::queue_adhoc_task($task);
    }
}