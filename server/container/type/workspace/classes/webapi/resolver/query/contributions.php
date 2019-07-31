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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package container_workspace
 */

namespace container_workspace\webapi\resolver\query;

use container_workspace\totara_engage\share\recipient\library;
use core\pagination\offset_cursor;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_core\advanced_feature;
use totara_engage\card\card_loader;
use totara_engage\query\query;

final class contributions implements query_resolver {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return array
     */
    public static function resolve(array $args, execution_context $ec): array {
        require_login();
        advanced_feature::require('container_workspace');

        $query = new query();
        $query->set_filters($args['filter']);
        $query->set_component('container_workspace');
        $query->set_area($args['area']);

        if (!empty($args['cursor'])) {
            $cursor = offset_cursor::decode($args['cursor']);
            $query->set_cursor($cursor);
        }

        $recipient = new library($args['workspace_id']);
        $loader = new card_loader($query);
        $paginator = $loader->fetch_not_shared($recipient);

        return [
            'cursor' => $paginator,
            'cards' => $paginator->get_items()->all()
        ];
    }
}