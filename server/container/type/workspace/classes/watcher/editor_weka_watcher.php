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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
namespace container_workspace\watcher;

use container_workspace\discussion\discussion;
use container_workspace\workspace;
use editor_weka\hook\find_context;

/**
 * Watcher for editor weka.
 */
final class editor_weka_watcher {
    /**
     * @param find_context $hook
     * @return void
     */
    public static function load_context(find_context $hook): void {
        global $DB;
        $component = $hook->get_component();

        if (workspace::get_type() !== $component) {
            return;
        }

        $area = $hook->get_area();
        if (discussion::AREA === $area) {
            $discussion_id = $hook->get_instance_id();

            if (null !== $discussion_id) {
                $workspace_id = $DB->get_field(
                    'workspace_discussion',
                    'course_id',
                    ['id' => $discussion_id]
                );

                $context = \context_course::instance($workspace_id);
                $hook->set_context($context);
                return;
            }
        }

        if (workspace::DESCRIPTION_AREA === $area) {
            $workspace_id = $hook->get_instance_id();
            if (null !== $workspace_id) {
                $context = \context_course::instance($workspace_id);
                $hook->set_context($context);
            }
        }
    }
}