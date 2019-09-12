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

use core\orm\query\builder;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_competency\entities\assignment;
use totara_competency\entities\pathway as pathway_entity;
use totara_competency\pathway;
use totara_competency\pathway_factory;
use totara_core\advanced_feature;

/**
 * Returns items for each scale value, which could be criteria groups
 */
class achievement_paths implements query_resolver {
    /**
     * Returns the achivement configuration for a specific competency.
     *
     * @param array $args
     * @param execution_context $ec
     * @return array
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('competency_assignment');

        require_login(null, false, null, false, true);

        $classification_enums = [
            pathway::PATHWAY_SINGLE_VALUE => 'SINGLEVALUE',
            pathway::PATHWAY_MULTI_VALUE => 'MULTIVALUE',
        ];

        // For now this is hardcoded, any additional paths will come after those
        $order = [
            'manual',
            'criteria_group',
            'learning_plan'
        ];
        // All single_value class pathways should be grouped together
        $single_value_key = array_search('criteria_group', $order);

        $pathway_types = self::get_active_pathway_types($args['assignment_id']);

        $paths = [];
        foreach ($pathway_types as $pathway_type) {
            /** @var pathway $pathway_classname */
            $pathway_classname = pathway_factory::get_classname($pathway_type);
            // We want all single value pathways grouped together
            if ($pathway_classname::CLASSIFICATION === pathway::PATHWAY_SINGLE_VALUE) {
                $paths[$single_value_key] = [
                    'class' => $classification_enums[pathway::PATHWAY_SINGLE_VALUE],
                    'type' => null
                ];
            } else {
                $order_key = array_search($pathway_type, $order);
                // We have to account for unknown pathways, we add them at the end
                if ($order_key === false) {
                    $order_key = max(count($order) + 1, count($paths) + 1);
                }
                $paths[$order_key] = [
                    'class' => $classification_enums[$pathway_classname::CLASSIFICATION],
                    'type' => $pathway_type
                ];
            }
        }

        ksort($paths);

        return array_values($paths);
    }

    private static function get_active_pathway_types(int $assignment_id): array {
        return builder::table(pathway_entity::TABLE)
            ->select(['id', 'path_type'])
            ->join([assignment::TABLE, 'ass'], 'comp_id', 'competency_id')
            ->where('status', pathway::PATHWAY_STATUS_ACTIVE)
            ->where('ass.id', $assignment_id)
            ->group_by(['id', 'path_type'])
            ->get()
            ->pluck('path_type');
    }

}
