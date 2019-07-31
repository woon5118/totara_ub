<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_playlist
 */
namespace totara_playlist\webapi\resolver\query;

use core\pagination\offset_cursor;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_core\advanced_feature;
use totara_engage\card\card;
use totara_engage\query\query;
use totara_playlist\totara_engage\card\loader;

final class cards implements query_resolver {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return card[]
     */
    public static function resolve(array $args, execution_context $ec): array {
        require_login();
        advanced_feature::require('engage_resources');

        $query = new query();
        $query->set_component('totara_playlist');
        $query->set_area('playlist');

        if (!empty($args['cursor'])) {
            $cursor = offset_cursor::decode($args['cursor']);
            $query->set_cursor($cursor);
        }

        $loader = new loader($query);
        $loader->set_playlist_id($args['id']);
        $paginator = $loader->fetch();

        return [
            'cursor' => $paginator,
            'cards' => $paginator->get_items()->all()
        ];
    }
}