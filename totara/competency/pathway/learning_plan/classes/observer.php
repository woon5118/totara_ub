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
 * @package pathway_learning_plan
 */

namespace pathway_learning_plan;

use tassign_competency\entities\competency_assignment_user;
use totara_competency\pathway;
use totara_competency\pathway_factory;
use totara_competency\pathway_aggregator;
use totara_plan\event\competency_value_set;

class observer {

    public static function competency_value_set(competency_value_set $event) {
        global $DB;

        $competency_id = $event->other['competency_id'];
        $user_id = $event->relateduserid;

        $pathway_record = $DB->get_record(
            'totara_competency_pathway',
            ['comp_id' => $competency_id, 'path_type' => 'learning_plan', 'status' => pathway::PATHWAY_STATUS_ACTIVE]
        );

        if (!$pathway_record) {
            return;
        }

        // Check if the user is assigned to this competency.
        $assigned = competency_assignment_user::repository()
            ->where('competency_id', $competency_id)
            ->where('user_id', $user_id)
            ->one();

        if (is_null($assigned)) {
            return;
        }

        $pathway = pathway_factory::fetch('learning_plan', $pathway_record->id);
        (new pathway_aggregator($pathway))->aggregate([$user_id]);
    }
}