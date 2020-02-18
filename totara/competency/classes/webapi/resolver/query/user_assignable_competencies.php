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
use core\orm\cursor;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_competency\data_providers\user_assignable_competencies as provider;
use totara_competency\helpers\capability_helper;
use totara_core\advanced_feature;

/**
 * Query to return competencies available for self assignment.
 */
class user_assignable_competencies implements query_resolver {

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

        $cursor = $args['cursor'] !== null ? cursor::decode($args['cursor']) : null;

        return provider::for($args['user_id'])
            ->set_filters($filters)
            ->set_order($args['order_by'] ?? null, $args['order_dir'] ?? null)
            ->fetch_paginated($cursor);
    }

    protected static function authorize(array $args) {
        advanced_feature::require('competency_assignment');

        require_login();

        require_capability('totara/hierarchy:viewcompetency', context_system::instance());

        capability_helper::require_can_assign($args['user_id']);
    }

}