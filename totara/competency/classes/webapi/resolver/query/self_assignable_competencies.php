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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\webapi\resolver\query;

use context_system;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_assignment\entities\user;
use totara_competency\data_providers\self_assignable_competencies as provider;

/**
 * Query to return competencies available for self assignment.
 */
class self_assignable_competencies implements query_resolver {

    /**
     * Returns a competency, given its ID.
     *
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec) {
        self::authorize($args);

        $filters = $args['filters'] ?? [];

        return provider::for($args['user_id'])
            ->set_filters($filters)
            ->set_order($args['order_by'], $args['order_dir'])
            ->fetch_paginated($args['cursor'], $args['limit']);
    }

    protected static function authorize(array $args) {
        require_login();

        require_capability('totara/hierarchy:viewcompetency', context_system::instance());

        if ($args['user_id'] == user::logged_in()->id) {
            require_capability('tassign/competency:assignself', context_system::instance());
        } else {
            $context = \context_user::instance($args['user_id']);
            require_capability('tassign/competency:assignother', $context);
        }
    }

}