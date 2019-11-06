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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_competency
 */

use aggregation_test_aggregation\test_aggregation;
use pathway_criteria_group\criteria_group;
use pathway_learning_plan\learning_plan;
use pathway_manual\entities\rating;
use pathway_manual\manual;
use pathway_test_pathway\test_pathway;
use core\entities\user;
use totara_competency\entities\competency;
use totara_competency\entities\competency_framework;
use totara_competency\entities\pathway as pathway_entity;
use totara_competency\entities\scale;
use totara_competency\entities\scale_value;
use totara_competency\pathway;
use totara_competency\pathway_aggregation;
use totara_competency\plugintypes;
use totara_criteria\criterion;

global $CFG;
require_once($CFG->dirroot . '/totara/competency/tests/fixtures/test_achievement_detail.php');
require_once($CFG->dirroot . '/totara/competency/tests/fixtures/test_aggregation.php');
require_once($CFG->dirroot . '/totara/competency/tests/fixtures/test_pathway.php');
require_once($CFG->dirroot . '/totara/competency/tests/fixtures/test_pathway_evaluator.php');
require_once($CFG->dirroot . '/totara/competency/tests/fixtures/test_pathway_evaluator_source.php');

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/totara_competency_assignment_generator.php');

/**
 * Pathway generator.
 *
 * Usage:
 *    $generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
 */
class totara_competency_generator extends component_generator_base {

    /**
     * @var totara_competency_assignment_generator
     */
    protected $assignment_generator;

    /**************************************************************************
     * Basic competency creation
     **************************************************************************/

    /**
     * Create a test competency.
     * If the name, framework or scale is not provided, then default values will be used.
     *
     * @param string|null $name
     * @param int|competency_framework|null $framework
     * @param array|null $comp_record
     *
     * @return competency
     */
    public function create_competency($name = null, $framework = null, $comp_record = null): competency {
        if (is_null($framework)) {
            $framework = $this->create_framework();
        }
        if (!is_numeric($framework)) {
            $framework = $framework->id;
        }

        $params = $comp_record ?? [];
        if (!is_null($name)) {
            $params['fullname'] = $name;
        }

        $comp = $this->hierarchy_generator()->create_hierarchy($framework, 'competency', $params);

        return new competency($comp);
    }

    /**
     * Create a test competency framework.
     * If the scale, name or description are not provided, then default values will be used.
     *
     * @param scale|null $scale Competency scale
     * @param string|null $name Framework name
     * @param string|null $description Framework description
     * @return competency_framework
     */
    public function create_framework(scale $scale = null, string $name = null, string $description = null): competency_framework {
        $framework_data = [];
        if (isset($name)) {
            $framework_data['fullname'] = $name;
        }
        if (isset($description)) {
            $framework_data['description'] = $description;
        }
        if (isset($scale)) {
            $framework_data['scale'] = $scale->id;
        } else {
            // This is a controversial idea
            $framework_data['scale'] = $this->create_scale()->id;
        }

        $framework = $this->hierarchy_generator()->create_framework('competency', $framework_data);
        return new competency_framework($framework, false);
    }

    /**
     * Create a test competency scale.
     * If the name, description or scale values are not provided, then default values will be used.
     *
     * @param string|null $name Scale name
     * @param string|null $description Scale description
     * @param array|null $values Array of arrays, like this: [
     *                              ['name' => string, 'proficient' => bool, 'default' => bool, 'sortorder' => int],
     *                          ]
     * @return scale
     */
    public function create_scale(string $name = null, string $description = null, array $values = null): scale {
        $scale_data = [];
        if (isset($name)) {
            $scale_data['name'] = $name;
        }
        if (isset($description)) {
            $scale_data['description'] = $description;
        }

        $scale = $this->hierarchy_generator()->create_scale('comp', $scale_data, $values);
        return new scale($scale, false);
    }


    /**************************************************************************
     * Competency pathways
     **************************************************************************/

    /**
     * Create a criteria group pathway
     *
     * @param competency|stdClass|int $competency Competency entity, record or ID
     * @param criterion[]|criterion $criteria Can be multiple or a single pre-defined criterion object(s)
     * @param scale_value|stdClass|int|null $scale_value If not specified, defaults to first scale value for the competency
     * @param int|null $sort_order If not specified, defaults to first scale value for the competency
     *
     * @return criteria_group
     */
    public function create_criteria_group($competency, $criteria, $scale_value = null, int $sort_order = null): criteria_group {
        /** @var criteria_group $instance */
        $instance = $this->create_pathway(criteria_group::class, $competency, $sort_order);

        if (is_null($scale_value)) {
            $scale_value = $competency->scale->sorted_values_high_to_low->first();
        } else if (is_number($scale_value) || !$scale_value instanceof scale_value) {
            $scale_value = new scale_value($scale_value, true, true);
        }
        $instance->set_scale_value($scale_value);

        if (!is_array($criteria)) {
            $criteria = [$criteria];
        }
        foreach ($criteria as $criterion) {
            $instance->add_criterion($criterion);
        }

        $instance->save();

        return criteria_group::fetch($instance->get_id());
    }

    /**
     * Create a manual rating pathway.
     *
     * @param competency|stdClass|int $competency Competency entity, record or ID.
     * @param string[]|null $roles Possible manual rating roles, e.g. [manual::ROLE_SELF, manual::ROLE_MANAGER]. Defaults to all.
     * @param int|null $sort_order Defaults to being sorted last.
     *
     * @return manual
     */
    public function create_manual($competency, array $roles = [], int $sort_order = null): manual {
        /** @var manual $instance */
        $instance = $this->create_pathway(manual::class, $competency, $sort_order);

        if (empty($roles)) {
            $roles = manual::get_all_valid_roles();
        } else if (array_diff($roles, array_values(manual::get_all_valid_roles()))) {
            throw new coding_exception('Invalid role(s) specified');
        }
        $instance->set_roles($roles);

        return $instance->save();
    }

    /**
     * Create a manual rating.
     *
     * @param competency|manual $competency_or_pathway If competency is specified, then it will create a default manual pathway
     * @param int|stdClass|user $subject_user
     * @param int|stdClass|user $rater_user
     * @param string $as_role e.g. manual::ROLE_SELF, manual::ROLE_MANAGER etc.
     * @param scale_value|int|null $scale_value If not specified, defaults to the first scale value set for the competency
     * @param string|null $comment
     *
     * @return rating
     */
    public function create_manual_rating(
        $competency_or_pathway, $subject_user, $rater_user, string $as_role, scale_value $scale_value = null, string $comment = null
    ): rating {
        if ($competency_or_pathway instanceof manual) {
            $roles = array_merge($competency_or_pathway->get_roles(), [$as_role]);
            $manual = $competency_or_pathway->set_roles($roles);
            $competency = $manual->get_competency();
        } else if ($competency_or_pathway instanceof competency) {
            $competency = $competency_or_pathway;
            $manual = $this->create_manual($competency_or_pathway, [$as_role]);
        } else {
            throw new coding_exception('Must specify either a competency or manual pathway');
        }

        $subject_id = isset($subject_user->id) ? $subject_user->id : $subject_user;
        $rater_id = isset($rater_user->id) ? $rater_user->id : $rater_user;

        if (is_null($scale_value)) {
            $scale_value = $competency->scale->sorted_values_high_to_low->first();
            $scale_value = $scale_value ? $scale_value->id : null;
        } else if ($scale_value instanceof scale_value) {
            $scale_value = $scale_value->id;
        }

        return $manual->set_manual_value($subject_id, $rater_id, $as_role, $scale_value, $comment);
    }

    /**
     * Create a learning plan pathway.
     *
     * @param competency|stdClass|int $competency Competency entity, record or ID.
     * @param int|null $sort_order Defaults to being sorted last.
     *
     * @return learning_plan
     */
    public function create_learning_plan_pathway($competency, int $sort_order = null): learning_plan {
        /** @var learning_plan $instance */
        $instance = $this->create_pathway(learning_plan::class, $competency, $sort_order);
        return $instance->save();
    }

    /**
     * Create a basic pathway, setting the competency and sort order value.
     *
     * @param string $pathway_class Pathway class, e.g. manual::class, criteria_group::class, learning_plan::class
     * @param competency|stdClass|int $competency Competency entity, record or ID.
     * @param int|null $sort_order Defaults to being sorted last.
     *
     * @return pathway New instance of the pathway class you specified.
     */
    protected function create_pathway(string $pathway_class, $competency, ?int $sort_order = null): pathway {
        /** @var pathway $instance */
        $instance = new $pathway_class();

        if (!$competency instanceof competency) {
            $competency = new competency($competency, true, true);
        }
        $instance->set_competency($competency);

        if (is_null($sort_order)) {
            $last_sort_order = pathway_entity::repository()->order_by('sortorder', 'desc')->first();
            $sort_order = $last_sort_order ? $last_sort_order->sortorder + 1 : 0;
        }
        $instance->set_sortorder($sort_order);

        return $instance;
    }


    /**************************************************************************
     * Non-competency related helpers
     **************************************************************************/

    /**
     * Create an individual criterion for use in a pathway
     *
     * @param string $criterion_class Criterion subclass, e.g. onactivate::class, linkedcourses::class, coursecompletion::class etc.
     * @param competency|stdClass|int $competency Competency entity, record or ID.
     * @param int|null $aggregation_method Aggregation method, either criterion::AGGREGATE_ALL or criterion::AGGREGATE_ANY_N
     * @param int[]|null $items Array of IDs for this criterion - e.g. for coursecompletion, it would be an array of course IDs
     * @param int|null $required_items The number of items that need to be completed for this criteria to be met (ANY_N aggregation)
     *
     * @return criterion
     */
    public function create_criterion(string $criterion_class, $competency, int $aggregation_method = criterion::AGGREGATE_ALL,
        array $items = [], int $required_items = 1): criterion {
        /** @var criterion $criterion */
        $criterion = new $criterion_class();

        $criterion->set_competency_id($competency->id ?? $competency);
        $criterion->set_aggregation_method($aggregation_method);
        $criterion->set_aggregation_params(['req_items' => $required_items]);

        $criterion->add_items($items);

        $criterion->save();

        return $criterion;
    }

    /**
     * Set completion course completion status for a user in a course.
     *
     * @param stdClass|int $course Course ID or record
     * @param stdClass|int $user User ID or record
     * @param int $completion_status One of COMPLETION_STATUS_NOTYETSTARTED, COMPLETION_STATUS_INPROGRESS,
     *                                  COMPLETION_STATUS_COMPLETE, or COMPLETION_STATUS_COMPLETEVIARPL.
     */
    public function create_course_enrollment_and_completion($course, $user, int $completion_status) {
        global $CFG;
        require_once($CFG->dirroot . '/lib/enrollib.php');

        if (!is_numeric($course)) {
            $course = $course->id;
        }
        if (!is_numeric($user)) {
            $user = $user->id;
        }

        if (!core_enrol_get_all_user_enrolments_in_course($user, $course)) {
            $this->datagenerator->enrol_user($user, $course);
        }

        $completion = new completion_completion(['course' => $course, 'userid' => $user]);

        if ($completion_status == COMPLETION_STATUS_COMPLETEVIARPL) {
            $completion->rpl = 1;
        }

        if ($completion_status == COMPLETION_STATUS_NOTYETSTARTED) {
            $completion->mark_enrolled();
        } else if ($completion_status == COMPLETION_STATUS_INPROGRESS) {
            $completion->mark_inprogress();
        } else {
            $completion->mark_complete();
        }
    }

    /**
     * Create a learning plan with competencies assigned.
     *
     * @param stdClass|int $for_user User the learning plan is for
     * @param array $competencies Array of [Competency ID => Scale Value ID]
     * @param bool $completed Mark the plan as completed?
     *
     * @return development_plan
     */
    public function create_learning_plan_with_competencies($for_user, $competencies, bool $completed = false): development_plan {
        global $CFG;
        require_once($CFG->dirroot . '/totara/plan/components/competency/competency.class.php');

        if (!is_numeric($for_user)) {
            $for_user = $for_user->id;
        }

        /** @var totara_plan_generator $plan_generator */
        $plan_generator = $this->datagenerator->get_plugin_generator('totara_plan');
        $plan = $plan_generator->create_learning_plan(['userid' => $for_user]);

        $plan = new development_plan($plan->id);

        foreach ($competencies as $competency => $scale_value) {
            $plan_generator->add_learning_plan_competency($plan->id, $competency);

            (new dp_competency_component($plan))
                ->set_value($competency, $for_user, $scale_value, (object) ['manual' => true]);
        }

        if ($completed) {
            $plan->set_status(DP_PLAN_STATUS_COMPLETE);
        }

        return $plan;
    }


    /**************************************************************************
     * Testing-specific classes
     **************************************************************************/

    /**
     * Create a test pathway
     *
     * @param competency|null $competency
     * @return test_pathway|pathway
     */
    public function create_test_pathway(?competency $competency = null): pathway {
        plugintypes::enable_plugin('test_pathway', 'pathway', 'totara_competency');

        $pathway = new test_pathway();
        if (!is_null($competency)) {
            $pathway->set_competency($competency);

            $pathway->save();
        }

        return $pathway;
    }

    /**
     * Create a test aggregation type.
     *
     * @return test_aggregation|pathway_aggregation
     */
    public function create_test_aggregation(): pathway_aggregation {
        return new test_aggregation();
    }

    /**
     * Get an instance of assignment specific generator
     *
     * @return totara_competency_assignment_generator
     */
    public function assignment_generator() {
        if (is_null($this->assignment_generator)) {
            $this->assignment_generator = new totara_competency_assignment_generator($this);
        }

        return $this->assignment_generator;
    }


    /**************************************************************************
     * Internal Helpers
     **************************************************************************/

    /**
     * Get the generator used for hierarchies.
     *
     * @return totara_hierarchy_generator|component_generator_base
     */
    public function hierarchy_generator(): totara_hierarchy_generator {
        return $this->datagenerator->get_plugin_generator('totara_hierarchy');
    }

}
