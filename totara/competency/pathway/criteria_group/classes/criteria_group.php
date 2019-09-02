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
 * @package totara_competency
 */

namespace pathway_criteria_group;

use totara_competency\base_achievement_detail;
use totara_competency\pathway;
use totara_competency\plugintypes;
use totara_competency\entities\scale_value;
use totara_competency\pathway_factory;
use totara_criteria\criterion;
use totara_criteria\criterion_factory;

/**
 * Class handling criteria_group pathways
 */
class criteria_group extends pathway {

    /**
     * Types of aggregation available in groups
     */
    const AGGREGATE_ALL = 1;
    const AGGREGATE_ANY_N = 2;

    /** @@var int $classification  */
    protected $classification = self::PATHWAY_SINGLE_VALUE;

    /** @var int */
    private $aggregation_method = self::AGGREGATE_ALL;

    /** @var array $aggregation_params Optional aggregation method parameters (e.g. min number of criteria) */
    private $aggregation_params = [];

    /** @var scale_value Scale value completion of the criteria in this group leads to */
    private $scale_value;

    /** @var int $last_key Last key assigned to a criterion */
    private static $last_key = 0;

    /** @var criterion[] Array of criteria in the group */
    private $criteria = [];

    /** @var [int] Ids of saved criterions */
    private $critids = [];

    /********************************************************************
     * Instantiation
     ********************************************************************/

    /**
     * Load the criteria_group configuration from the database
     *
     * @throws \coding_exception
     */
    protected function fetch_configuration() {
        global $DB;

        if (empty($this->get_path_instance_id())) {
            return;
        }

        // Get group information
        $row = $DB->get_record('pathway_criteria_group', ['id' => $this->get_path_instance_id()], '*', MUST_EXIST);

        if (!empty($row->scale_value_id)) {
            $this->set_scale_value(new scale_value($row->scale_value_id));
        }

        $this->set_aggregation_method($row->aggregation_method);
        $this->set_aggregation_params($row->aggregation_params);

        $this->fetch_criteria();
    }

    /**
     * Fetch all criteria in this group
     */
    private function fetch_criteria() {
        global $DB;

        $this->criteria = [];

        $rows = $DB->get_records(
            'pathway_criteria_group_criterion',
            ['criteria_group_id' => $this->get_path_instance_id()],
            'id'
        );
        foreach ($rows as $row) {
            $this->add_criterion(criterion_factory::fetch($row->criterion_type, $row->criterion_id));
        }
    }


    /****************************************************************************
     * Saving
     ****************************************************************************/

    /**
     * Has the pathway specific configuration changed?
     *
     * @return bool
     */
    protected function configuration_is_dirty(): bool {
        global $DB;

        if (empty($this->get_path_instance_id())) {
            return true;
        }

        $criteria_rows = $DB->get_records('pathway_criteria_group_criterion', ['criteria_group_id' => $this->get_path_instance_id()]);
        if (count($this->get_criteria()) != count($criteria_rows)) {
            return true;
        }

        $existing_critids = [];
        foreach ($criteria_rows as $row) {
            $existing_critids[] = $row->criterion_id;
        }

        foreach ($this->get_criteria() as $crit) {
            if (!in_array($crit->get_id(), $existing_critids)) {
                return true;
            }

            if ($crit->is_dirty()) {
                return true;
            }
        }

        $current_row = $DB->get_record('pathway_criteria_group', ['id' => $this->get_path_instance_id()]);

        if ($this->get_aggregation_method() != $current_row->aggregation_method ||
            json_encode($this->get_aggregation_params()) != $current_row->aggregation_params) {

            return true;
        }

        return false;
    }


    /**
     * Save the criteria_group, associated criteria and approval roles
     *
     * @throws \coding_exception
     */
    protected function save_configuration() {
        global $DB;

        if (empty($this->get_scale_value()) || empty($this->get_scale_value()->id)) {
            throw new \coding_exception('A criteria_group pathway requires a valid scale value');
        }

        // Not checking is_dirty here as it is checked in parent::save()
        // We will only get here if something changed

        // If there are no criteria - delete this pathway
        if (count($this->get_criteria()) == 0) {
            // First 'save' the criteria - which will delete the now removed criteria
            $this->save_criteria();

            // Now delete the pathway
            return $this->delete();
        }

        // Create new criteria_group instance
        $record = new \stdClass();
        $record->aggregation_method = $this->get_aggregation_method();
        $record->aggregation_params = json_encode($this->get_aggregation_params());
        $record->scale_value_id = $this->get_scale_value()->id;
        $record->status = static::PATHWAY_STATUS_ACTIVE;
        $record->timemodified = time();

        if ($this->get_path_instance_id()) {
            $record->id = $this->get_path_instance_id();
            $DB->update_record('pathway_criteria_group', $record);
        } else {
            $this->set_path_instance_id($DB->insert_record('pathway_criteria_group', $record));
        }

        $this->save_criteria();
    }

    /**
     * Save the group_criteria to the db
     */
    private function save_criteria() {
        global $DB;

        if (empty($this->get_path_instance_id())) {
            throw new \coding_exception('Cannot save criteria without a saved pathway to link to.');
        }

        $critrows = $DB->get_records_menu(
            'pathway_criteria_group_criterion',
            ['criteria_group_id' => $this->get_path_instance_id()],
            '',
            'criterion_id, criterion_type'
        );

        $toinsert = [];
        $this->critids = [];

        foreach ($this->get_criteria() as $criterion) {
            $criterion->save();

            $criterion_id = $criterion->get_id();

            if (!isset($critrows[$criterion_id])) {
                $toinsert[] = (object)[
                    'criteria_group_id' => $this->get_path_instance_id(),
                    'criterion_type' => $criterion->get_plugin_type(),
                    'criterion_id' => $criterion->get_id(),
                ];
            }

            $this->critids[] = $criterion_id;

            unset($critrows[$criterion->get_id()]);
        }

        if (!empty($toinsert)) {
            $DB->insert_records('pathway_criteria_group_criterion', $toinsert);
        }

        // Delete removed criteria
        if (!empty($critrows)) {
            foreach ($critrows as $id => $type) {
                $criterion = criterion_factory::fetch($type, $id);
                $criterion->delete();
            }
            $DB->delete_records_list('pathway_criteria_group_criterion', 'criterion_id', array_keys($critrows));
        }
    }

    /**
     * Link the criteria to the criteria_group
     * and update critids
     */
    private function link_criteria_to_group() {
        global $DB;

        $this->critids = [];

        if (empty($this->get_criteria())) {
            return;
        }

        // Insert new criteria_group_criterion rows
        $toinsert = [];

        foreach ($this->get_criteria() as $criterion) {
            $toinsert[] = (object)[
                'criteria_group_id' => $this->get_path_instance_id(),
                'criterion_type' => $criterion->get_plugin_type(),
                'criterion_id' => $criterion->get_id(),
            ];

            $this->critids[] = $criterion->get_id();
        }

        if (!empty($toinsert)) {
            $DB->insert_records('pathway_criteria_group_criterion', $toinsert);
        }
    }

    /**
     * 'Delete' the pathway specific detail by archiving it
     */
    protected function delete_configuration() {
        global $DB;

        if (empty($this->get_path_instance_id())) {
            // Never saved
            return;
        }

        $trans = $DB->start_delegated_transaction();

        foreach ($this->criteria as $crit) {
            $crit->delete();
        }

        $DB->delete_records(
            'pathway_criteria_group_criterion',
            ['criteria_group_id' => $this->get_path_instance_id()]
        );

        $DB->delete_records(
            'pathway_criteria_group',
            ['id' => $this->get_path_instance_id()]
        );

        $trans->allow_commit();

        $this->set_path_instance_id(null);
        $this->replace_criteria([]);
    }


    /****************************************************************************
     * Getters and setters
     ****************************************************************************/

    /**
     * Get aggregation method
     *
     * @return int Aggregation method
     */
    public function get_aggregation_method(): int {
        return $this->aggregation_method;
    }

    /**
     * Set criteria_group aggregation method
     *
     * @param int Aggregation method
     * @return $this
     */
    public function set_aggregation_method(int $aggregation_method): pathway {
        $this->aggregation_method = $aggregation_method;
        return $this;
    }

    /**
     * Get aggregation parameters
     *
     * @return array Aggregation parameters
     */
    public function get_aggregation_params(): array {
        return $this->aggregation_params;
    }

    /**
     * Set the aggregation parameters to use
     *
     * @param  array | string $aggregation_params. This can be passed as array or json encoded string
     * @return $this
     */
    public function set_aggregation_params($params): pathway {
        if (is_string($params)) {
            // Json encoded
            $this->aggregation_params = json_decode($params, true);
        } else if (!is_null($params) && !is_array($params)) {
            $this->aggregation_params = (array)$params;
        } else {
            $this->aggregation_params = $params;
        }

        return $this;
    }

    /**
     * Returns the scale value associated with this pathway.
     *
     * @return ?scale_value A null return value indicates that any scale value may be returned
     */
    public function get_scale_value(): ?scale_value {
        return $this->scale_value;
    }

    /**
     * Set the scale value associated with this pathway.
     *
     * @param  scale_value Scale value to set
     * @return $this
     */
    public function set_scale_value(scale_value $scale_value) {
        $this->scale_value = $scale_value;
        return $this;
    }

    /**
     * Get all criteria
     *
     * @return criterion[]
     */
    public function get_criteria(): array {
        return $this->criteria;
    }

    /**
     * Add a criterion that must be completed
     *
     * @param criterion $child Child group to add
     */
    public function add_criterion(criterion $criterion): pathway {
        // Check that the criterion ids are unique.
        // If all are new - no further uniqueness checks

        $id = $criterion->get_id();
        if (empty($id) || !in_array($id, $this->critids)) {
            $key = static::$last_key++;
            $this->criteria[$key] = $criterion;

            if (!empty($id)) {
                $this->critids[] = $id;
            }
        }

        return $this;
    }

    /**
     * Remove the specified criterion
     *
     * @param int $key Assigned key of the criterion to remove
     */
    public function remove_criterion(int $key): pathway {
        $id = $this->criteria[$key]->get_id();
        if (!empty($id)) {
            $ids = array_flip($this->critids);
            unset($ids[$id]);
            $this->critids = array_flip($ids);
        }

        unset($this->criteria[$key]);
        return $this;
    }

    /**
     * Replace all associated criteria with the specified set
     *
     * @param array criteria New set of criteria
     * @return $this
     */
    public function replace_criteria(array $criteria): pathway {
        $this->criteria = $criteria;

        // Build critids and last_key
        $key = $last_key = 0;
        $this->critids = [];

        foreach ($criteria as $key => $crit) {
            // All should have ids at this point
            $id = $crit->get_id();
            if (!empty($id)) {
                $this->critids[] = $id;
            }

            if ($key > $last_key) {
                $last_key = $key;
            }
        }

        static::$last_key = $last_key;
        return $this;
    }

    /**
     * Return a summary of the criteria associated with this pathway.
     *
     * @return array List of summarized criteria objects associated with the pathway
     */
    public function get_summarized_criteria_set(): array {
        $result = array_map(
            function ($criterion) {
                return $criterion->display_instance()->get_configuration();
            },
            $this->get_criteria()
        );

        return $result;
    }


    /*******************************************************************************************************
     * Data exporting
     *******************************************************************************************************/

    /**
     * Return the name of the template to use for editing this pathway
     *
     * @return string Template name
     */
    public function get_edit_template(): string {
        return 'pathway_criteria_group/pathway_criteria_group_edit';
    }

    /**
     * Return the name of the template to use for viewing this pathway
     *
     * @return string Template name
     */
    public function get_view_template(): string {
        return 'pathway_criteria_group/pathway_criteria_group';
    }

    /**
     * Export detail for editing the pathway
     *
     * @return array containing templatedata
     */
    public function export_edit_detail(): array {
        $result = $this->export_edit_overview();
        $result['title'] = $this->get_title();
        $result['scalevalue'] = $this->get_scale_value()->id;
        $result['criteria'] = $this->export_criteria();

        return $result;
    }

    /**
     * Export information of the criteria in this group for editing
     *
     * @return array
     */
    public function export_criteria(): array {
        $result = [];

        foreach ($this->criteria as $criterion) {
            $result[] = $criterion->export_criterion_edit_template();
        }

        return $result;
    }

    /**
     * Get a short description of the content of this pathway
     *
     * @return Short description
     */
    public function get_short_description(): string {
        $criteria_types = [];
        foreach ($this->criteria as $criterion) {
            if (!isset($criteria_types[$criterion->get_plugin_type()])) {
                $criteria_types[$criterion->get_plugin_type()] = $criterion->get_title();
            }
        }

        $glue = ' ' . get_string('and', 'totara_competency') . ' ';
        return implode($glue, $criteria_types);
    }

    /**
     * Export detail for viewing this pathway
     *
     * @return array
     */
    public function export_view_detail(): array {
        $result = [
            'id' => $this->get_id(),
            'title' => $this->get_title(),
            'criteria' => [],
        ];

        $keys = array_keys($this->criteria);
        $lastkey = end($keys);
        foreach ($this->criteria as $key => $criterion) {
            $result['criteria'][] = array_merge($criterion->export_criterion_view_template(), ['showand' => ($key != $lastkey)]);
        }

        return $result;
    }

    /**
     * Retrieve the current configuration from the database
     *
     * @param ?int $id Instance id
     * @return \stdClass | null
     */
    public static function dump_pathway_configuration(?int $id = null) {
        global $DB;

        if (!is_null($id) && $result = $DB->get_record('pathway_criteria_group', ['id' => $id])) {
            $result->criteria = $DB->get_records('pathway_criteria_group_criterion', ['criteria_group_id' => $id]);
            foreach ($result->criteria as $id => $criterion) {
                $criterion->detail = criterion_factory::dump_criterion_configuration($criterion->criterion_type, $criterion->criterion_id);
            }

            return $result;
        }

        return null;
    }



    /*************************************************************************************************
     * Per user
     * ***********************************************************************************************/
    /**
     * Return obtained scale value for this user
     * (Not naming it aggregate to avoid confusion of aggregation of all pathway results for this user)
     *
     * Returns achievement detail which will contain the scale value id achieved.
     * If a value was achieved, related info will include all criteria that were satisfied.
     * If no value was achieved, related info will be empty.
     *
     * @param int $user_id User to calculate the results for
     * @return base_achievement_detail
     */
    public function aggregate_current_value(int $user_id): base_achievement_detail {
        if (is_null($this->scale_value) || empty($this->criteria)) {
            return new achievement_detail();
        }

        $achievement_detail = new achievement_detail();

        $result = $this->aggregation_method == static::AGGREGATE_ALL;
        foreach ($this->criteria as $criterion) {
            $crit_satisfied = $criterion->aggregate($user_id);
            if ($crit_satisfied) {
                $achievement_detail->add_completed_criterion($criterion);
            }
            switch ($this->aggregation_method) {
                case static::AGGREGATE_ALL:
                    $result = $result && $crit_satisfied;
                    // If any criterion not yet satisfied by the user, we can stop
                    if (!$result) {
                        break 2;
                    }
                    break;

                case static::AGGREGATE_ANY_N:
                    // Todo: Is there is supposed to a be a minimum number of any criteria satisfied here?
                    $result = $result || $crit_satisfied;
                    // If $result is true at this point, we could stop looping as we know they have
                    // satisfied aggregation. However, we do also need to record all criteria that we satisfied.
                    break;

                default:
                    // Invalid aggregation method
                    $result = false;
                    break 2;
            }
        }

        if ($result) {
            $achievement_detail->set_scale_value_id($this->scale_value->id);
        } else {
            // Remove any achievement data if the user didn't actually achieve a value.
            $achievement_detail->set_related_info([]);
        }

        return $achievement_detail;
    }

   /**
     * Does this instance contain any singleuse criteria
     *
     * @return bool
     */
    public function has_singleuse_criteria(): bool {
        foreach ($this->criteria as $criterion) {
            if ($criterion->is_singleuse()) {
                return true;
            }
        }

        return false;
    }
}
