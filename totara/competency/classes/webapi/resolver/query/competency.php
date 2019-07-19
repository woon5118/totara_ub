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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_job
 */

namespace totara_competency\webapi\resolver\query;

use context_system;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use tassign_competency\entities\competency as competency_entity;

/**
 * Query to return a single competency.
 */
class competency implements query_resolver {

    /**
     * Returns a competency, given its ID.
     *
     * @param array $args
     * @param execution_context $ec
     * @return competency_entity
     */
    public static function resolve(array $args, execution_context $ec) {
        // Basic sanity check, GraphQL does this for us, but other can call resolve.
        if (!isset($args['competencyid'])) {
            throw new \coding_exception('A required parameter (competencyid) was missing');
        }

        require_login();
        require_capability('totara/hierarchy:viewcompetency', context_system::instance());

        return new competency_entity($args['competencyid']);
    }

}