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
 * @package totara_comment
 */
namespace totara_comment\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_comment\comment;
use totara_comment\loader\comment_loader;
use totara_comment\resolver_factory;

final class comments implements query_resolver {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return comment[]
     */
    public static function resolve(array $args, execution_context $ec): array {
        require_login();

        $instance_id = $args['instanceid'];
        $component = $args['component'];
        $area = $args['area'];

        $resolver = resolver_factory::create_resolver($component);
        $cursor = $resolver->get_default_cursor($area);

        if (isset($args['cursor'])) {
            $cursor = $cursor::decode($args['cursor']);
        }

        $paginator = comment_loader::get_paginator(
            $instance_id,
            $component,
            $area,
            $cursor
        );

        $comments = $paginator->get_items()->all();

        // We need to sort the comments base on the time creation, so that the earliest comments
        // will go to first index following by the next recent comments.
        return array_reverse($comments);
    }
}