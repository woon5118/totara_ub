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

namespace ml_recommender\webapi\resolver\query;

use core\orm\pagination\offset_cursor_paginator;
use core\pagination\offset_cursor;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use ml_recommender\loader\recommended_item\playlists_loader;
use ml_recommender\query\recommended_item\user_query;
use totara_core\advanced_feature;
use totara_playlist\playlist;

/**
 * Recommended user playlists cursor
 *
 * @package ml_recommender\webapi\resolver\query
 */
final class recommended_user_playlists_cursor implements query_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return offset_cursor_paginator|null
     */
    public static function resolve(array $args, execution_context $ec): ?offset_cursor_paginator {
        global $USER;
        require_login();
        if (advanced_feature::is_disabled('ml_recommender')) {
            return null;
        }

        $user_id = $args['user_id'] ?? $USER->id;

        // Build our query
        $query = new user_query($user_id, playlist::get_resource_type(), null);

        if (isset($args['cursor'])) {
            $cursor = offset_cursor::decode($args['cursor']);
            $query->set_cursor($cursor);
        }

        // Load the interaction items
        return playlists_loader::get_recommended_playlists_for_user($query);
    }
}