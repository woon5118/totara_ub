<?php
/*
 * This file is part of Totara Learn
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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @author David Curry <david.curry@totaralearning.com>
 * @package core
 */

namespace core\webapi\resolver\query;

use core\webapi\execution_context;

final class categories_by_parent_category implements \core\webapi\query_resolver {
    public static function resolve(array $args, execution_context $ec) {
        global $CFG;
        require_once($CFG->dirroot . '/lib/coursecatlib.php');

        // TL-21305 will find a better, encapsulated solution for require_login calls.
        require_login(null, false, null, false, true);

        if (!empty($args['sort'])) {
            $sortstr = mb_strtolower($args['sort']);
            $sortorder = [
                $sortstr => 1
            ];
            $options = ['sort' => $sortorder];
        } else {
            $options = [];
        }

        // Note: This takes care of visibility checks as long as the 3rd parameter is false.
        $category = \coursecat::get($args['categoryid']);

        // Note: This seems to handle the visibility of the child categories.
        return $category->get_children($options);
    }
}
