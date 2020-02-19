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

use core\orm\collection;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_competency\entities\assignment;
use totara_competency\entities\pathway as pathway_entity;
use totara_competency\entities\pathway_achievement;
use totara_competency\pathway;
use totara_competency\pathway_factory;
use totara_core\advanced_feature;

/**
 * Returns items for each scale value, which could be criteria groups
 */
class scale_achievements implements query_resolver {

    /**
     * Returns the achievement configuration for a specific competency.
     *
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('competency_assignment');

        require_login(null, false, null, false, true);

        $pathways = self::get_active_single_value_types($args['assignment_id']);

        $scale_result = [];
        foreach ($pathways as $pathway) {
            $pathway_instance = pathway_factory::from_entity($pathway);
            $pathway_achievement = pathway_achievement::get_current($pathway_instance, $args['user_id']);
            // Let's avoid an additional query later
            $pathway_achievement->relate('pathway', $pathway);

            $scale_value = $pathway_instance->get_scale_value();
            if (!$scale_value) {
                throw new \coding_exception('Expected a single value type to have a scale value');
            }
            if (!isset($scale_result[$scale_value->id])) {
                $scale_result[$scale_value->id] = [
                    'scale_value' => $scale_value,
                    'items' => []
                ];
            }
            $scale_result[$scale_value->id]['items'][] = $pathway_achievement;
        }

        return array_values($scale_result);
    }

    /**
     * This is quite specific to this GraphQL query so keeping this here for now
     *
     * @param int $assignment_id
     * @return pathway_entity[]|collection
     */
    private static function get_active_single_value_types(int $assignment_id): collection {
        $single_value_types = pathway_factory::get_single_value_types();

        return pathway_entity::repository()
            ->where('path_type', $single_value_types)
            ->where('status', pathway::PATHWAY_STATUS_ACTIVE)
            ->join([assignment::TABLE, 'ass'], 'competency_id', 'competency_id')
            ->where('ass.id', $assignment_id)
            ->with('competency')
            ->order_by('sortorder', 'asc')
            ->get();
    }

}
