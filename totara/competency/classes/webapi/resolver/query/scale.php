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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com
 * @package totara_competency
 */

namespace totara_competency\webapi\resolver\query;

use context_system;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_competency\models\scale as scale_model;
use totara_core\advanced_feature;

/**
 * Query to return a single competency scale.
 */
class scale implements query_resolver {

    /**
     * Returns a competency scale, given its ID or competency id.
     *
     * @param array $args
     * @param execution_context $ec
     * @return scale_model
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('competencies');

        if (!isset($args['id']) && !isset($args['competency_id']) || isset($args['id']) && isset($args['competency_id'])) {
            throw new \coding_exception('Please provide either scale id OR competency id');
        }

        require_login();
        require_capability('totara/hierarchy:viewcompetency', context_system::instance());

        if (isset($args['id'])) {
            $model = scale_model::load_by_id_with_values($args['id']);
        } else {
            $model = scale_model::find_by_competency_id($args['competency_id']);
        }

        return $model;
    }

}