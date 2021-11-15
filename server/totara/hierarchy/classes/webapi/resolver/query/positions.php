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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_hierarchy
 */

namespace totara_hierarchy\webapi\resolver\query;

use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_user_capability;
use hierarchy_position\data_providers\positions as positions_provider;
use core\pagination\cursor;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use core\webapi\middleware\require_login;
use core\webapi\resolver\has_middleware;

class positions implements query_resolver, has_middleware {

    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        global $USER;

        $query = $args['query'] ?? [];
        $order_by = $query['order_by'] ?? 'fullname';
        $order_dir = $query['order_dir'] ?? 'ASC';
        $result_size = $query['result_size'] ?? positions_provider::DEFAULT_PAGE_SIZE;
        $enc_cursor = $query['cursor'] ?? null;
        $filters = $query['filters'] ?? [];

        $context = \context_user::instance($USER->id);
        $ec->set_relevant_context($context);

        $cursor = $enc_cursor ? cursor::decode($enc_cursor) : null;

        return (new positions_provider())
            ->set_page_size($result_size)
            ->set_filters($filters)
            ->set_order($order_by, $order_dir)
            ->fetch_paginated($cursor);
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('positions'),
            new require_user_capability('totara/hierarchy:viewposition'),
        ];
    }
}