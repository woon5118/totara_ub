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
use pathway_criteria_group\criteria_group;
use pathway_learning_plan\learning_plan;
use totara_competency\achievement_configuration;
use totara_competency\entities\competency;
use totara_criteria\criterion;

class default_criteria_on_install extends adhoc_task {

    /**
     * Add default criteria to any competencies when upgrading to a version with totara_competency.
     *
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $COMP_AGGREGATION;

        /** @var competency[]|collection $competencies */
        $competencies = competency::repository()->with('children')->get();

        $linked_course_competencies = builder::table('comp_criteria')
            ->select_raw('DISTINCT competencyid AS comp_id')
            ->where('itemtype', 'coursecompletion')
            ->get();

        foreach ($competencies as $competency) {
            $configuration = new achievement_configuration($competency);
            $configuration->set_aggregation_type('first');
            $configuration->save_aggregation();

            if ($this->add_learning_plans()) {
                $lp_pathway = new learning_plan();
                $lp_pathway->set_competency($competency);
                $lp_pathway->save();
            }

            switch ($competency->aggregationmethod) {
                case $COMP_AGGREGATION['ALL']:

                    $linkedcourses_exist = $linked_course_competencies->item($competency->id);
                    $childcompetencies_exist = $competency->children->count() > 0;

                    if (!$linkedcourses_exist && !$childcompetencies_exist) {
                        continue;
                    }

                    $group = new criteria_group();
                    $group->set_competency($competency);
                    $group->set_scale_value($competency->scale->min_proficient_value);

                    if ($linkedcourses_exist) {
                        $linkedcourses = new linkedcourses();
                        $linkedcourses->set_metadata([(object) ['metakey' => 'linkedtype', 'metavalue' => linkedcourses::LINKTYPE_ALL]]);
                        $linkedcourses->set_aggregation_method(criterion::AGGREGATE_ALL);
                        $group->add_criterion($linkedcourses);
                    }

                    if ($childcompetencies_exist) {
                        $childcompetencies = new childcompetency();
                        $childcompetencies->set_aggregation_method(criterion::AGGREGATE_ALL);
                        $group->add_criterion($childcompetencies);
                    }

                    $group->save();

                    break;
                case $COMP_AGGREGATION['ANY']:

                    $linkedcourses_exist = $linked_course_competencies->item($competency->id);
                    $childcompetencies_exist = $competency->children->count() > 0;

                    if ($linkedcourses_exist) {
                        $linkedcourses = new linkedcourses();
                        $linkedcourses->set_metadata([(object) ['metakey' => 'linkedtype', 'metavalue' => linkedcourses::LINKTYPE_ALL]]);
                        $linkedcourses->set_aggregation_method(criterion::AGGREGATE_ANY_N);

                        $group1 = new criteria_group();
                        $group1->set_competency($competency);
                        $group1->set_scale_value($competency->scale->min_proficient_value);
                        $group1->add_criterion($linkedcourses);
                        $group1->save();
                    }

                    if ($childcompetencies_exist) {
                        $childcompetencies = new childcompetency();
                        $childcompetencies->set_aggregation_method(criterion::AGGREGATE_ANY_N);

                        $group2 = new criteria_group();
                        $group2->set_competency($competency);
                        $group2->set_scale_value($competency->scale->min_proficient_value);
                        $group2->add_criterion($childcompetencies);
                        $group2->save();
                    }

                    break;
                default:
                    // The only other case here should be OFF, where we don't add any criteria groups, but also
                    // for any unrecognised cases, e.g. from plugins, we won't try to do anything.
                    // No exceptions are necessary here, the user can either fix in the interface later
                    // or some plugin can deal with it if relevant.
            }
        }
    }

    private function add_learning_plans(): bool {
        global $DB;

        if (totara_feature_disabled('learningplans')) {
            return false;
        }

        return $DB->record_exists('dp_plan_competency_assign', []);
    }
}
