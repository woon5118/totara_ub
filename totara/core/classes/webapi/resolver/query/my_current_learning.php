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
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use totara_core\user_learning\item_base;
use totara_core\user_learning\item_helper as learning_item_helper;
use totara_plan\user_learning\item as plan_item;

/**
 * Query to return my programs.
 */
class my_current_learning implements query_resolver, has_middleware {

    /**
     * Returns the user's current learning items.
     *
     * @param array $args
     * @param execution_context $ec
     * @return array|item_base[]
     */
    public static function resolve(array $args, execution_context $ec) {
        global $USER;

        $items = learning_item_helper::get_users_current_learning_items($USER->id);

        // Expand the items are required to create a specialised list for this block.
        $items = learning_item_helper::expand_learning_item_specialisations($items);

        \core_collator::asort_objects_by_property($items, 'fullname', \core_collator::SORT_NATURAL);

        // Filter the content to exclude duplications, completed courses and other block specific criteria.
        $items = learning_item_helper::filter_collective_learning_items($USER->id, $items);

        $learning_items = array_filter($items, function ($item) {
            // We don't need the plan itself, just the contents.
            return !$item instanceof plan_item;
        });

        return $learning_items;
    }

    public static function get_middleware(): array {
        return [
            require_login::class
        ];
    }

}
