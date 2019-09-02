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
 * @package totara_competency
 */

defined('MOODLE_INTERNAL') || die();

use pathway_criteria_group\criteria_group;
use pathway_manual\manual;
use totara_competency\entities\competency;
use totara_competency\entities\scale_value;
use totara_competency\pathway;
use totara_competency\pathway_aggregation;
use totara_competency\plugintypes;
use totara_competency\base_achievement_detail;

/**
 * Pathway generator.
 *
 * Usage:
 *    $generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');
 */
class totara_competency_generator extends component_generator_base {

    /**
     * Create a criteria_group pathway
     *
     *  Data
     *  [
     *      'comp_id' => 1, // Required Competency id
     *      'sortorder' => 0, // Optional Sortorder. Defaults to 0
     *      'scale_value_id' => 1, // Required Scale value id
     *      'aggregation' => criteria_group::AGGREGATE_ALL, // Optional Aggregation method - Defaults to ALL
     *      'req_items' => [], // Optional  number of required items
     *      'criteria' => [], // List of initialized criteria in the group
     *  ]
     *
     * @param array $data Criteria_group data
     * @return pathway
     */
    public function create_criteria_group(array $data = []) {
        global $DB;

        $instance = new criteria_group();
        $instance->set_competency(new competency($data['comp_id']));
        if (empty($instance->get_competency())) {
            throw new \coding_exception('Unknown competency id');
        }

        $instance->set_sortorder($data['sortorder'] ?? 0);

        if (empty($data['scale_value'])) {
            // Retrieve the first scale value for the competency
            $vals = $instance->get_competency()->scale->scale_values;
            $instance->set_scale_value($vals->first());
        } else {
            $instance->set_scale_value(new scale_value($data['scale_value']));
        }

        $instance->set_aggregation_method($data['aggregation'] ?? criteria_group::AGGREGATE_ALL);
        $instance->set_aggregation_params(['req_items' => $data['req_items'] ?? 1]);

        foreach ($data['criteria'] as $criterion) {
            $instance->add_criterion($criterion);
        }

        $instance->save();

        return criteria_group::fetch($instance->get_id());
    }

    /**
     * Create a manual pathway
     *
     *  Data
     *  [
     *      'comp_id' => 1, // Required Competency id
     *      'sortorder' => 0, // Optional Sortorder. Defaults to 0
     *      'roles' => [], // List of roles. Valid values are defined in  pathway_manual::ROLE_... constants
     *  ]
     *
     * @param array $data Manual data
     * @return pathway
     */
    public function create_manual(array $data = []) {
        global $DB;

        $instance = new manual();
        $instance->set_competency(new competency($data['comp_id']));
        if (empty($instance->get_competency())) {
            throw new \coding_exception('Unknown competency id');
        }

        $instance->set_sortorder($data['sortorder'] ?? 0);
        $instance->set_roles($data['roles'] ?? []);

        $instance->save();

        return manual::fetch($instance->get_id());
    }

    /**
     * Create a test pathway
     *
     * @param ?competency $competency
     * @return pathway
     */
    public function create_test_pathway(?competency $competency = null): test_pathway {
        plugintypes::enable_plugin('test_pathway', 'pathway', 'totara_competency');

        $pathway = new test_pathway();
        if (!is_null($competency)) {
            $pathway->set_competency($competency);

            $pathway->save();
        }

        return $pathway;
    }


    public function create_test_aggregation(): test_aggregation {
        return new test_aggregation();
    }

    /**
     * Create a test competency
     * If the name, framework and/or scale is not provided, default values will be used
     *
     * @param  string|null $comp_name Competency fullname
     * @param  int|null    $fw_id     Id of the framework to create the competency in
     * @param  int|null    $scale_id  Scale to use. This will only be used if the framework id is not provided
     * @param  \stdClass|null $comp_record Competency attributes to use. Allowed values are the same as \totara_hierarchy_generator::create_comp
     */
    public function create_competency(?string $comp_name = null, ?int $fw_id = null, ?int $scale_id = null, $comp_record = null): competency {
        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator =  $this->datagenerator->get_plugin_generator('totara_hierarchy');

        if (is_null($fw_id)) {
            if (is_null($scale_id)) {
                $scale = $hierarchy_generator->create_scale('comp');
                $scale_id = $scale->id;
            }

            $compfw = $hierarchy_generator->create_comp_frame(['scale' => $scale_id]);
            $fw_id = $compfw->id;
        }

        $params = $comp_record ?? [];
        $params['frameworkid'] = $fw_id;
        if (!is_null($comp_name)) {
            $params['fullname'] = $comp_name;
        }

        $comp = $hierarchy_generator->create_comp($params);

        return new competency($comp);
    }
}


class test_pathway extends pathway {

    // METHODS AND PROPERTIES FOR TEST PURPOSES
    // These methods can be used to give this test_pathway object some custom behaviour.
    // Remember that these will only influence this instance. If another instance is loaded, it will not
    // hold these custom values.

    /**
     * @param null|scale_value|Closure $scale_value Can be a scale value or null which will be returned
     *   for anyone. Or could be a closure to vary the return value according to user id for example.
     * @return test_pathway
     */
    public function set_test_aggregate_current_value($scale_value): test_pathway {
        $this->scale_value = $scale_value;
        return $this;
    }

    // ACTUAL METHODS USED FOR A PATHWAY

    protected function fetch_configuration() {
    }

    protected function save_configuration() {
    }

    protected function configuration_is_dirty(): bool {
        return false;
    }

    protected function delete_configuration() {
    }

    public function aggregate_current_value(int $user_id): base_achievement_detail {
        if ($this->scale_value instanceof Closure) {
            // If a function was passed in, we'll execute that.
            $scale_value = ($this->scale_value)($user_id);
        } else {
            $scale_value = $this->scale_value;
        }

        if (is_null($scale_value)) {
            return new test_achievement_detail();
        } else {
            $achievement_detail = new test_achievement_detail();
            $achievement_detail->set_scale_value_id($scale_value->id);
            return $achievement_detail;
        }
    }

    public function get_short_description(): string {
        return 'Test pathway short description';
    }

    public function get_summary_template(): string {
        return '';
    }

    public function get_definition_template(): string {
        return '';
    }

    public function get_detail_endpoint(): string {
        return '';
    }


    /**
     * Todo: If a default method gets added in the base class. May be able to take this away.
     *
     * @return array
     */
    public function export_detail(): array {
        return [];
    }

    public function export_summary(): array {
        return [];
    }
}

class_alias('test_pathway', 'pathway_test_pathway\\test_pathway');

class test_achievement_detail extends base_achievement_detail {
    public function get_achieved_via_strings(): array {
        return [''];
    }
}

class test_aggregation extends pathway_aggregation {

    // METHODS ADDED TO ASSIST WITH TESTING.

    private $test_achieved_value_ids = [];
    private $test_achieved_vias = [];

    public function set_test_aggregated_data($achieved_values, $achieved_vias): test_aggregation {
        $this->test_achieved_value_ids = $achieved_values;
        $this->test_achieved_vias = $achieved_vias;
        return $this;
    }

    // ACTUAL METHODS REQUIRED FOR PATHWAY AGGREGATION.

    protected function do_aggregation() {
        foreach ($this->test_achieved_value_ids as $userid => $achieved_value_id) {
            $this->set_achieved_value_id($userid, $achieved_value_id);
        }
        foreach ($this->test_achieved_vias as $userid => $achieved_via) {
            $this->set_achieved_via($userid, $achieved_via);
        }
    }
}

class_alias('test_aggregation', 'aggregation_test_aggregation\\test_aggregation');
