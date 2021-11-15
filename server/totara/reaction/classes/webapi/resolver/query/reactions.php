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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_reaction
 */
namespace totara_reaction\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use totara_reaction\exception\reaction_exception;
use totara_reaction\loader\reaction_loader;
use totara_reaction\resolver\resolver_factory;

/**
 * Query to get all the reactions
 */
final class reactions implements query_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec): array {
        global $USER;

        $component = $args['component'];
        $instance_id = $args['instanceid'];
        $area = $args['area'];

        $resolver = resolver_factory::create_resolver($component);

        if(!$ec->has_relevant_context()) {
            $context = $resolver->get_context($instance_id, $area);
            $ec->set_relevant_context($context);
        } else {
            $context = $ec->get_relevant_context();
        }

        if ($context->is_user_access_prevented($USER->id) ||
            !$resolver->can_view_reactions($instance_id, $USER->id, $area)) {
            throw reaction_exception::on_view();
        }

        $page = 1;
        if (isset($args['page'])) {
            $page = (int) $args['page'];
        }

        $paginator = reaction_loader::get_paginator(
            $component,
            $area,
            $instance_id,
            $page
        );

        return $paginator->get_items()->all();
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
        ];
    }

}
