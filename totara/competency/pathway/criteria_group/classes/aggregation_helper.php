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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package pathway_criteria_group
 */

namespace pathway_criteria_group;

use totara_competency\achievement_configuration;
use totara_competency\competency_achievement_aggregator;
use totara_competency\pathway;
use totara_competency\pathway_aggregator;

class aggregation_helper {

    public static function get_pathways_containing_criterion_item($criterion_item_id, $status) {
        global $DB;

        $sql = "SELECT cp.id
                  FROM {totara_competency_pathway} cp
                  JOIN {pathway_criteria_group} pcg
                    ON pcg.id = cp.path_instance_id
                   AND cp.path_type = :pathtype
                  JOIN {pathway_criteria_group_criterion} pcgc
                    ON pcgc.criteria_group_id = pcg.id
                  JOIN {totara_criteria} tc
                    ON pcgc.criterion_id = tc.id
                  JOIN {totara_criteria_item} tci
                    ON tc.id = tci.criterion_id
                 WHERE tci.id = :itemid";

        $pathway_ids = $DB->get_fieldset_sql($sql, ['pathtype' => 'criteria_group', 'itemid' => $criterion_item_id]);

        $pathways = [];
        foreach ($pathway_ids as $pathway_id) {
            $pathways[$pathway_id] = criteria_group::fetch($pathway_id);
        }

        return $pathways;
    }

    public static function aggregate_based_on_item($user_id, $criterion_item_id) {
        $competencies = [];

        $pathways = static::get_pathways_containing_criterion_item($criterion_item_id, pathway::PATHWAY_STATUS_ACTIVE);

        foreach ($pathways as $pathway) {
            $aggregator = new pathway_aggregator($pathway);
            $aggregator->aggregate([$user_id]);

            $competency = $pathway->get_competency();
            // We'll aggregate the competencies in a separate loop.
            // Collect them in such a way that each competency is aggregated once (keyed by id).
            $competencies[$competency->id] = $competency;
        }

        foreach ($competencies as $competency) {
            $configuration = new achievement_configuration($competency);
            $aggregator = new competency_achievement_aggregator($configuration);
            $aggregator->aggregate([$user_id]);
        }
    }
}