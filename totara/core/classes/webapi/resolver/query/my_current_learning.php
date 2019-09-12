<?php
/**
 *
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author David Curry <david.curry@totaralearning.com>
 * @package totara_core
 */

namespace totara_core\webapi\resolver\query;

use core\webapi\execution_context;
use totara_core\user_learning\item_helper as learning_item_helper;

/**
 * Query to return my programs.
 */
class my_current_learning implements \core\webapi\query_resolver {
    /**
     * Returns the user's current learning items.
     *
     * @param array $args
     * @param execution_context $ec
     */
    public static function resolve(array $args, execution_context $ec) {
        global $USER;

        // TL-21305 will find a better, encapsulated solution for require_login calls.
        require_login(null, false, null, false, true);

        $items = learning_item_helper::get_users_current_learning_items($USER->id);

        // Expand the items are required to create a specialised list for this block.
        $items = learning_item_helper::expand_learning_item_specialisations($items);

        /**
         * The sortorder for content.
         * @var string
         */
        $sortorder = 'fullname';

        \core_collator::asort_objects_by_property($items, $sortorder, \core_collator::SORT_NATURAL);

        // Filter the content to exclude duplications, completed courses and other block specific criteria.
        $items = learning_item_helper::filter_collective_learning_items($USER->id, $items);

        $learningitems = [];
        // Loop through to add component, any other transformations/pre-formatting can happen here.
        foreach ($items as $item) {
            if ($item instanceof \totara_plan\user_learning\item) {
                // We don't need the plan itself, just the contents.
                continue;
            }

            // Note: Persistant queries are <component>_<type> i.e. core_course_course(id);
            $item->itemtype = $item->get_type(); // certification, program, course.
            $item->itemcomponent = $item->get_component(); // totara_certification, totara_program, core_course

            // Make sure we have the due date, this is for programs and certifications, also courses inside learning plans.
            if ($item->item_has_duedate()) {
                $item->ensure_duedate_loaded();
            }

            // Make sure we have the percentage in the progress.
            if (method_exists($item, 'get_progress_percentage')) {
                $item->progress = $item->get_progress_percentage();
            }

            $learningitems[] = $item;
        }

        return $learningitems;
    }
}
