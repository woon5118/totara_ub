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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package totara_reportedcontent
 */

namespace totara_reportedcontent\webapi\resolver\query;

use context_system;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_reportedcontent\loader\review_loader;
use totara_reportedcontent\review;

/**
 * Class reviews
 *
 * @package totara_reportedcontent\webapi\resolver\query
 */
final class reviews implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return review[]
     */
    public static function resolve(array $args, execution_context $ec): array {
        global $USER;

        require_login();

        // API is only open to those who can see this capability (eg, users shouldn't be able to look up report details)
        $has_capability = has_capability('totara/reportedcontent:manage', context_system::instance(), $USER);
        if (!$has_capability) {
            throw new \coding_exception('nopermission', 'totara_reportedcontent');
        }

        $item_id = $args['item_id'];
        $context_id = $args['context_id'];
        $component = $args['component'];
        $area = $args['area'];

        $page = 1;
        if (isset($args['page'])) {
            $page = (int) $args['page'];
        }

        $paginator = review_loader::get_paginator($item_id, $context_id, $component, $area, $page);
        return $paginator->get_items()->all();
    }
}