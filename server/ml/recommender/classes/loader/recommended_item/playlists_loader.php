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
 * @package ml_recommender
 */

namespace ml_recommender\loader\recommended_item;

use core\orm\pagination\offset_cursor_paginator;
use core\orm\query\builder;
use ml_recommender\entity\recommended_item;
use ml_recommender\entity\recommended_user_item;
use ml_recommender\query\recommended_item\item_query;
use ml_recommender\query\recommended_item\user_query;
use totara_engage\card\card_resolver;
use totara_playlist\entity\playlist as playlist_entity;
use totara_playlist\playlist;

/**
 * Loader class for a recommended item
 */
final class playlists_loader {
    /**
     * Preventing this class from construction
     */
    private function __construct() {
    }

    /**
     * Select all related playlists based on the provided component id
     *
     * @param item_query $query
     * @return offset_cursor_paginator
     */
    public static function get_recommended_playlists(item_query $query): offset_cursor_paginator {
        $builder = static::get_base_playlist_query(recommended_item::TABLE);

        $builder->where('r.target_item_id', $query->get_target_item_id());
        $builder->where('r.target_component', $query->get_target_component());
        $builder->where('r.target_area', $query->get_target_area());

        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }

    /**
     * Select all recommended playlists for the user
     *
     * @param user_query $query
     * @return offset_cursor_paginator
     */
    public static function get_recommended_playlists_for_user(user_query $query): offset_cursor_paginator {
        $builder = static::get_base_playlist_query(recommended_user_item::TABLE);

        $builder->where('r.user_id', $query->get_target_user_id());
        $builder->where('r.component', $query->get_target_component());
        $builder->where('r.area', $query->get_target_area());

        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }

    /**
     * @param string $table
     * @return builder
     */
    private static function get_base_playlist_query(string $table): builder {
        $builder = builder::table($table, 'r');

        // Join against playlists
        $builder->join([playlist_entity::TABLE, 'p'], 'r.item_id', 'p.id');
        $builder->select([
            'p.*',
            'p.id as instanceid',
            'r.component as component'
        ]);
        $builder->where('r.component', 'totara_playlist');
        $builder->results_as_arrays();

        $builder->order_by_raw('r.score DESC');
        $builder->map_to(
            function(array $record) {
                return card_resolver::create_card(playlist::get_resource_type(), $record);
            }
        );

        return $builder;
    }
}