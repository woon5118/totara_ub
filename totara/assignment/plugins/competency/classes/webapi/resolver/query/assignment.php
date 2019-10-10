<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @package tassign_competency
 */

namespace tassign_competency\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use tassign_competency\models\assignment as assignment_model;
use totara_core\advanced_feature;

/**
 * Query to return a single competency.
 */
class assignment implements query_resolver {

    /**
     * Returns a competency, given its ID.
     *
     * @param array $args
     * @param execution_context $ec
     * @return assignment_model
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('perform');

        require_login();

        return assignment_model::load_by_id($args['assignmentid']);
    }

}