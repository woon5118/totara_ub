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
 * @package totara_competency
 */

namespace totara_competency\task;

use core\collection;
use core\orm\query\builder;
use core\task\adhoc_task;
use criteria_childcompetency\childcompetency;
use criteria_linkedcourses\linkedcourses;
use criteria_linkedcourses\metadata_processor as linked_courses_metadata_processor;
use pathway_criteria_group\criteria_group;
use pathway_learning_plan\learning_plan;
use totara_competency\achievement_configuration;
use totara_competency\entities\competency;
use totara_competency\entities\scale;
use totara_competency\legacy_aggregation;
use totara_core\advanced_feature;
use totara_criteria\criterion;

class default_criteria_on_install extends adhoc_task {

    /**
     * Add default criteria to any competencies when upgrading to a version with totara_competency.
     *
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        /**
         * Load any competencies that don't already have an associated overall aggregation type record.
         *
         * This should therefore include any competencies from prior to totara_competency.
         *
         * If this task is running again after previously failing, it should at least exclude competencies that had
         * already had criteria added.
         *
         * @var competency[]|collection $competencies
         */
        $competencies = competency::repository()
            ->with('children')
            ->join('totara_competency_scale_aggregation', 'id', '=', 'comp_id', 'left')
            ->where('totara_competency_scale_aggregation.id', null)
            ->get();

        /**
         * Links the frameworks to scales.
         *
         * Scales are then loaded below. By having them in one separate array, we reduce having to fetch
         * them again for each competency.
         */
        $framework_to_scale = builder::table('comp_scale_assignments')
            ->select(['frameworkid', 'scaleid'])
            ->get();

        /** @var scale[]|collection $scales */
        $scales = scale::repository()->get();

        foreach ($competencies as $competency) {
            $configuration = new achievement_configuration($competency);
            $configuration->set_aggregation_type('first');
            $configuration->save_aggregation();

            if ($this->should_add_learning_plans()) {
                $lp_pathway = new learning_plan();
                $lp_pathway->set_competency($competency);
                $lp_pathway->save();
            }

            $scale_id = $framework_to_scale->item($competency->frameworkid)->scaleid;
            $min_proficient_value = $scales->item($scale_id)->min_proficient_value;

            $aggregation = new legacy_aggregation($competency);
            $aggregation->create_default_criteria(new linkedcourses(), $min_proficient_value)
                ->create_default_criteria(new childcompetency(), $min_proficient_value);
        }

        // Make sure all linked courses are synced
        linked_courses_metadata_processor::update_item_links(null);
    }

    private function should_add_learning_plans(): bool {
        if (advanced_feature::is_disabled('perform')) {
            // If perform isn't enabled, we'll need to add the learning plan pathway here since users will not be able
            // to access an interface to add them themselves if they need them.
            return true;
        }

        if (totara_feature_disabled('learningplans')) {
            return false;
        }

        return builder::table('dp_plan_competency_assign')->exists();
    }
}
