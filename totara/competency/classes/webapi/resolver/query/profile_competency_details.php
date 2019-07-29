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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_orm
 */

namespace totara_competency\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_competency\data_providers\competency_progress;

class profile_competency_details implements query_resolver {
    public static function resolve(array $args, execution_context $ec) {
        if (!isset($args['user_id'])) {
            throw new \coding_exception('User id is required');
        }
        if (!isset($args['competency_id'])) {
            throw new \coding_exception('Competency id is required');
        }

        $progress = competency_progress::for($args['user_id']);
        $progress = $progress->fetch_for_competency($args['competency_id']);

        return (object) [
            'competency' => $progress->competency,
            'items' => array_map(function($assignment) {
                $proficient = $assignment->has_field('achievement') ? ($assignment->get_field('achievement')->scale_value->proficient ?? false) : false;

                return( (object) [
                    'assignment' => $assignment,
                    'competency' => $assignment->get_competency(),
                    'proficient' => boolval($proficient),
                    'my_value' => $assignment->has_field('achievement') ? $assignment->get_field('achievement')->scale_value : null,
                    'min_value' => $assignment->get_competency()->scale->min_proficient_value ?? null,
                ]);
            }, $progress->assignments),
        ];
    }
}