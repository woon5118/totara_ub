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
 * @package totara_criteria
 */

namespace totara_criteria;

/**
 * Base class for a single criterion.
 */

abstract class criterion {

    /** Aggregation constants */
    const AGGREGATE_ALL = 1;
    const AGGREGATE_ANY_N = 2;

    /** @var int $id */
    private $id;

    /** @var string $type Type of criterion*/
    private $plugin_type;

    /** @var int $aggregation_method Aggregation method to use */
    private $aggregation_method = self::AGGREGATE_ALL;

    /** @var array $aggregation_params Optional aggregation method parameters (e.g. min number of items) */
    private $aggregation_params = [];

    /** @var array $items */
    private $item_ids = [];

    /** @var array $metadata */
    private $metadata = [];

    /** @var int $last_evaluated */
    private $last_evaluated;

    /**
     * Constructor.
     */
    public function __construct() {
        $reflect = new \ReflectionClass($this);
        $this->plugin_type = $reflect->getShortName();
    }

    /**
     * Fetch specific criterion from the database
     *
     * @param int $id Id of the criterion to fetch
     * @return $this
     * @throws \coding_exception
     */
    final public static function fetch(int $id): criterion {
        global $DB;

        $instance = new static();

        $record = $DB->get_record('totara_criteria', ['id' => $id], '*', MUST_EXIST);
        if ($record->plugin_type != $instance->get_plugin_type()) {
            throw new \coding_exception("The specified criterion id is for another type of criterion");
        }

        $instance->set_id($id);
        $instance->set_aggregation_method($record->aggregation_method ?? static::AGGREGATE_ALL);
        $instance->set_aggregation_params($record->aggregation_params ?? []);

        $instance->fetch_items();
        $instance->fetch_metadata();

        return $instance;
    }

    /*******************************************************************************************************
     * Getters and setters
     *******************************************************************************************************/

    /**
     * Get the id of the criterion
     *
     * Public scope for testing purposes
     *
     * @return ?int
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Set the id of the criterion
     *
     * @param ?int $id New criterion id
     * @return $this
     */
    public function set_id(?int $id): criterion {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the plugin type
     *
     * Public scope for testing purposes

     * @return string
     */
    public function get_plugin_type(): string {
        return $this->plugin_type;
    }

    /**
     * Get the aggregation method
     *
     * Public scope for testing purposes
     *
     * @return int Aggregation method
     */
    public function get_aggregation_method(): int {
        return $this->aggregation_method;
    }

    /**
     * Set the aggregation method
     *
     * Public scope for testing purposes
     *
     * @param  int $aggregation_method
     * @return $this
     */
    public function set_aggregation_method(int $aggregation_method): criterion {
        $this->aggregation_method = $aggregation_method;

        return $this;
    }

    /**
     * Get the aggregation params
     *
     * Public scope for testing purposes
     *
     * @return array Aggregation parameters
     */
    public function get_aggregation_params(): array {
        return $this->aggregation_params;
    }

    /**
     * Set the aggregation params
     *
     * Public scope for testing purposes
     *
     * @param  array | string $aggregation_params. This can be passed as array or json encoded string
     * @return $this
     */
    public function set_aggregation_params($params): criterion {
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
     * Get the title for the criterion
     *
     * @return string
     */
    public function get_title() {
        $classname = (new \ReflectionClass($this))->getShortName();
        return get_string('pluginname', "criteria_{$classname}");
    }

    /**
     * Get the type of items associated with this criterion
     * V1 - Assuming that a criterion can only store items of a single type
     */
    abstract protected function get_items_type();

    /**
     * Get the ids of items associated with this criterion
     *
     * Public scope for testing purposes
     *
     * @return array
     */
    public function get_item_ids() {
        return $this->item_ids;
    }

    /**
     * Replace the ids of items associated with this criterion
     *
     * @param [int] $itemids Id of items to link to the criterion
     * @return $this
     */
    public function set_item_ids(array $itemids): criterion {
        $this->item_ids = $itemids;
        return $this;
    }

    /**
     * Add items to the criterion
     *
     * @param [int] $itemids Id of items to link to the criterion
     */
    public function add_items(array $itemids): criterion {
        $newitems = array_diff($itemids, $this->item_ids);
        $this->item_ids = array_merge($this->item_ids, $newitems);

        return $this;
    }

    /**
     * Remove the specified items
     *
     * @param [int] $itemids Ids of items to remove
     */
    public function remove_items(array $itemids): criterion {
        $this->item_ids = array_diff($this->item_ids, $itemids);
        return $this;
    }

    /**
     * Get the metadata of this criterion
     *
     * @return array
     */
    public function get_metadata() {
        return $this->metadata;
    }

    /**
     * Replace the metadata of this criterion
     *
     * @param [\stdClass] $metadata Metadata metakey/metavalue pairs
     * @return $this
     */
    public function set_metadata(array $metadata): criterion {
        $this->metadata = [];
        $this->add_metadata($metadata);
        return $this;
    }

    /**
     * Add metadata to the criterion
     *
     * @param [\stdClass] $metadata Metadata metakey/metavalue pairs
     * @throws \coding_exception
     */
    public function add_metadata(array $metadata): criterion {
        foreach ($metadata as $obj) {
            if (is_array($obj)) {
                $obj = (object)$obj;
            }

            if (empty($obj->metakey) || !isset($obj->metavalue)) {
                throw new \coding_exception("Criterion metadata requires a metakey / metavalue pair");
            }
            $this->metadata[$obj->metakey] = $obj->metavalue;
        }

        return $this;
    }

    /**
     * Remove the specified metadata keys
     *
     * @param [int] $metakeys Keys of metadata to remove
     */
    public function remove_metadata(array $metakeys): criterion {
        foreach ($metakeys as $metakey) {
            unset($this->metadata[$metakey]);
        }

        return $this;
    }

    /**
     * Get last_evaluated
     *
     * @return ?int
     */
    public function get_last_evaluated(): ?int {
        return $this->last_evaluated;
    }

    /**
     * Set last_evaluated
     *
     * @param int $last_evaluated
     */
    public function set_last_evaluated(int $last_evaluated): criterion {
        $this->last_evaluated = $last_evaluated;
        return $this;
    }

    /**
     * Does this criterion have associated items
     * Plugins should overwrite if required
     *
     * @return bool
     */
    public function has_items(): bool {
        return true;
    }

    /**
     * Does this criterion allow for aggregation between items
     * Plugins should overwrite if required
     *
     * @return bool
     */
    public function has_aggregation(): bool {
        return true;
    }

    /**
     * Does this criterion have associated metadata
     * Plugins should overwrite if required
     *
     * @return bool
     */
    public function has_metadata(): bool {
        return true;
    }

    /**
     * Is this a single-use criterion
     * Plugins should overwrite if required
     *
     * @return bool
     */
    public function is_singleuse(): bool {
        return false;
    }

    /**
     * Return the display instance for this criterion
     *
     * @return criterion_display
     */
    public function display_instance(): criterion_display {
        $classname = $this->get_display_class();
        return new $classname($this);
    }

    /**
     * Return the display class for this criterion
     *
     * @return string Class to use for displaying
     */
    abstract protected function get_display_class(): string;


    /*******************************************************************************************************
     * Retrieve and Save
     *******************************************************************************************************/

    /**
     * Load the items belonging to this criterion
     */
    private function fetch_items() {
        $this->item_ids = [];

        $rows = $this->retrieve_items();
        foreach ($rows as $row) {
            // item_id is the actual item_id, (course_id, etc)
            $this->item_ids[] = $row->item_id;
        }
    }

    /**
     * Load the metadata of this criterion
     */
    private function fetch_metadata() {
        $this->metadata = [];

        $rows = $this->retrieve_metadata();
        $this->add_metadata($rows);
    }

    /**
     * Retrieve the items belonging to this criterion
     */
    private function retrieve_items() {
        global $DB;

        if (empty($this->get_id())) {
            return [];
        }

        $sql =
            "SELECT itm.*
               FROM {totara_criteria_item} itm
              WHERE itm.criterion_id = :criterionid
           ORDER BY itm.item_type";
        $params = ['criterionid' => $this->get_id(), 'itemtype' => $this->get_items_type(), 'notarchived' => 0];

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Retrieve the metadata of this criterion
     */
    private function retrieve_metadata() {
        global $DB;

        if (empty($this->get_id())) {
            return;
        }

        return $DB->get_records('totara_criteria_metadata', ['criterion_id' => $this->get_id()]);
    }

    /**
     * Check whether any information on the criterion changed from the saved values
     *
     * @return bool
     */
    public function is_dirty(): bool {
        if (empty($this->get_id())) {
            return true;
        }

        $existing_crit = self::fetch($this->get_id());

        $tst_methods = [
            'get_aggregation_method',
            'get_aggregation_params',
            'get_item_ids',
            'get_metadata',
        ];

        foreach ($tst_methods as $method) {
            if ($existing_crit->{$method}() != $this->{$method}()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save this criterion and all its items
     *
     * @return $this
     */
    public function save(): criterion {
        global $DB;

        if (!$this->is_dirty()) {
            return $this;
        }

        $record = new \stdClass();
        $record->plugin_type = $this->get_plugin_type();
        $record->aggregation_method = $this->get_aggregation_method();
        $record->aggregation_params = json_encode($this->get_aggregation_params());
        $record->criterion_modified = time();

        if ($this->get_id()) {
            $record->id = $this->get_id();
            $DB->update_record('totara_criteria', $record);
        } else {
            $this->set_id($DB->insert_record('totara_criteria', $record));
        }

        $this->save_items();
        $this->save_metadata();

        return $this;
    }

    /**
     * Save the criterion items
     */
    private function save_items() {
        global $DB;

        $current_items = $DB->get_records_menu('totara_criteria_item', ['criterion_id' => $this->get_id()], '', 'item_id, id');

        foreach ($this->get_item_ids() as $itemid) {
            $this->save_criterion_item($itemid);
            unset($current_items[$itemid]);
        }

        if (!empty($current_items)) {
            $DB->delete_records_list('totara_criteria_item_record', 'criterion_item_id', $current_items);
            $DB->delete_records_list('totara_criteria_item', 'id', $current_items);
        }
    }

    /**
     * Save the metadata of this criterion
     */
    private function save_metadata() {
        global $DB;

        $current_keys = $DB->get_records_menu('totara_criteria_metadata', ['criterion_id' => $this->get_id()], '', 'metakey, id');

        if (!empty($this->metadata)) {
            foreach ($this->metadata as $metakey => $metaval) {
                $record = [
                    'criterion_id' => $this->id,
                    'metakey' => $metakey,
                ];

                $existing = isset($current_keys[$metakey]);

                if ($existing) {
                    $record['id'] = $current_keys[$metakey];
                    $record['metavalue'] = $metaval;
                    $DB->update_record('totara_criteria_metadata', $record);

                    unset($current_keys[$metakey]);
                } else {
                    $record['metavalue'] = $metaval;
                    $DB->insert_record('totara_criteria_metadata', $record);
                }
            }
        }

        if (!empty($current_keys)) {
            $DB->delete_records_list('totara_criteria_metadata', 'id', $current_keys);
        }
    }


    /**
     * Save the criterion_item if it doesn't exist
     *
     * @param int $item_id Item id to add
     * @return int $id Id of the criterion_item row
     */
    private function save_criterion_item(int $itemid): int {
        global $DB;

        // Ensure there is only one of each item
        $id = $DB->get_field(
            'totara_criteria_item',
            'id',
            ['item_type' => $this->get_items_type(), 'item_id' => $itemid, 'criterion_id' => $this->id],
            IGNORE_MISSING
        );

        if ($id == false) {
            // TODO: Do we need separate constants for the items?

            // Insert the criterion_item
            $record = (object)[
                'criterion_id' => $this->id,
                'item_type' => $this->get_items_type(),
                'item_id' => $itemid,
                'criterion_modified' => time(),
            ];

            $id = $DB->insert_record('totara_criteria_item', $record);
        }

        return $id;
    }

    /**
     * Save the last_evaluated date of the criterion
     *
     * @return $this
     */
    public function save_last_evalated(): criterion {
        global $DB;

        // Not doing anything if not saved previously or last_evaluated not yet set
        if (empty($this->get_id() or empty($this->get_last_evaluated()))) {
            return $this;
        }

        $record = new \stdClass();
        $record->id = $this->get_id();
        $record->last_evaluated = $this->get_last_evaluated();
        $DB->update_record('totara_criteria', $record);

        return $this;
    }


    /**
     * Delete the criterion
     */
    public function delete() {
        global $DB;

        if (empty($this->get_id())) {
            // Never saved before
            return;
        }

        $trans = $DB->start_delegated_transaction();

        // Delete all the items and metadata
        $this->delete_items();
        $this->delete_metadata();

        // Delete the actual criterion
        $DB->delete_records('totara_criteria', ['id' => $this->get_id()]);

        // Unset the id as it doesn't exist anymore
        $this->set_id(null);

        $trans->allow_commit();
    }


    /**
     * Delete the items
     */
    private function delete_items() {
        global $DB;

        $sql =
            "DELETE
               FROM {totara_criteria_item_record}
              WHERE criterion_item_id IN
                (SELECT id
                   FROM {totara_criteria_item}
                  WHERE criterion_id = :criterionid)";
        $DB->execute($sql, ['criterionid' => $this->get_id()]);
        $DB->delete_records('totara_criteria_item', ['criterion_id' => $this->get_id()]);
    }

    /**
     * Delete the metadata
     */
    private function delete_metadata() {
        global $DB;

        $DB->delete_records('totara_criteria_metadata', ['criterion_id' => $this->get_id()]);
    }

    /**
     * Dump the current criterion configuration from the database
     *
     * @param int $id Criterion id
     * @return \stdClass Criterion configuration
     */
    public static function dump_criterion_configuration(int $id) {
        global $DB;

        if ($result = $DB->get_record('totara_criteria', ['id' => $id])) {
            $result->items = $DB->get_records('totara_criteria_item', ['criterion_id' => $id]);
            $result->metadata = $DB->get_records('totara_criteria_metadata', ['criterion_id' => $id]);

            return $result;
        }

        return null;
    }


    /*******************************************************************************************************
     * User evaluation
     *******************************************************************************************************/

    /**
     * Evaluate all items and aggregate the results to return a single value
     *
     * @param int $user_id Evaluate criteria satisfaction for this user
     * @return bool
     */
    // TODO: name ?? evaluate or aggregate??
    public function aggregate(int $user_id): bool {
        if (empty($this->item_ids)) {
            return false;
        }

        // TODO: Should we allow for 0 completed??
        if ($this->aggregation_method == self::AGGREGATE_ANY_N && empty($this->aggregation_params['req_items'])) {
            // Default to min 1 item
            $this->aggregation_params['req_items'] = 1;
        }

        $overall = $this->aggregation_method == self::AGGREGATE_ALL;
        $num_completed = 0;

        $item_results = $this->get_item_results($user_id);

        foreach ($item_results as $item_result) {

            // Aggregate this item completion result and previously evaluated results
            switch ($this->aggregation_method) {
                case self::AGGREGATE_ALL:
                    $overall = $overall && $item_result;
                    // If any is false - we can stop testing
                    if (!$overall) {
                        break 2;
                    }
                    break;

                case self::AGGREGATE_ANY_N:
                    if ($item_result) {
                        $num_completed += 1;
                        if ($num_completed >= (int)$this->aggregation_params['req_items']) {
                            $overall = true;
                            // We can stop testing now - result has been determined
                            break 2;
                        }
                    }
                    break;

                default:
                    break;
            }
        }

        return $overall;
    }

    /**
     * @return string that is class name of item_evaluator for this criteria type. Must use the item_evaluator trait.
     */
    abstract public static function item_evaluator(): string;

    /**
     * Gets the item records for a user for this criterion. Does not update item records, but the returned results
     * will include elements where there were no corresponding item records. e.g. an item record hadn't been created
     * for a given user and item yet. The elements default to '0' (not complete).
     *
     * @param $user_id
     * @return array with item ids as keys and values of either '1' for criteria met or '0' for not met.
     */
    protected function get_item_results($user_id): array {
        global $DB;

        // We can't use $this->item_ids.
        // They're the id of the course or whatever external thing we're referring to.
        $criterion_item_ids = array_keys($this->retrieve_items());

        if (count($criterion_item_ids) == 0) {
            return [];
        }

        $zero_results = array_fill_keys($criterion_item_ids, 0);

        [$insql, $params] = $DB->get_in_or_equal($criterion_item_ids, SQL_PARAMS_NAMED);
        $params['userid'] = $user_id;

        $existing_records = $DB->get_records_sql_menu(
            'SELECT criterion_item_id, criterion_met
                   FROM {totara_criteria_item_record}
                  WHERE user_id = :userid
                    AND criterion_item_id ' . $insql,
            $params
        );

        // To remind you of php's use of the + operator with arrays:
        // It joins the arrays. Where keys match, the value from the left-hand array is used and the right hand ignored.
        // So in this case, if it's in $existing_records, it will use that value, otherwise 0 from $zero_results is used.
        return $existing_records + $zero_results;
    }


    /*******************************************************************************************************
     * Data exporting
     * TODO - remove once all APIs have been replaced by GraphQL
     *******************************************************************************************************/

    /**
     * Export the edit template name and data
     *
     * @return array
     */
    public function export_criterion_edit_template(): array {
        $results = $this->export_edit_overview();
        $results['criterion_templatename'] = $this->get_edit_template();

        return $results;
    }

    /**
     * Export the view template name and data
     *
     * @return array
     */
    public function export_criterion_view_template(): array {
        $result = $this->export_view_detail();
        $result['criterion_templatename'] = $this->get_view_template();

        return $result;
    }


    /**
     * Return the name of the template for defining this criterion
     * Plugins should overwrite if required
     *
     * @return string Edit template name
     */
    public function get_edit_template(): string {
        return '';
    }

    /**
     * Return the name of the template to view this criterion
     * Plugins should overwrite if required
     *
     * @return string View template name
     */
    public function get_view_template(): string {
        return '';
    }

    /**
     * Export the criterion overview data.
     * This contains only enough information to list the criterion
     * Plugins should overwrite if required
     *
     * @return array
     */
    public function export_edit_overview(): array {
        return [
            'id' => $this->get_id() ?? 0,
            'type' => $this->get_plugin_type(),
            'title' => $this->get_title(),
            'singleuse' => $this->is_singleuse(),
        ];
    }

    /**
     * Exportdetail for editing the criterion
     * This contains detail information
     * Plugins should overwrite if required
     *
     * @return array Exported detail
     */
    public function export_edit_detail(): array {
        $results = $this->export_edit_overview();

        if ($this->has_items()) {
            $results['items'] = $this->export_edit_items();
        }

        if ($this->has_metadata()) {
            $results['metadata'] = $this->export_edit_metadata();
        }

        if ($this->has_aggregation()) {
            $results['aggregation'] = $this->export_edit_aggregation();
        }

        return $results;
    }

    /**
     * Export criterion aggregation data
     * Plugins should overwrite if required
     *
     * @return array
     */
    public function export_edit_aggregation(): array {
        return [
            'method' => $this->aggregation_method,
            'reqitems' => $this->aggregation_params['req_items'] ?? 1,
        ];
    }

    /**
     * Export criterion item data
     * Plugins should overwrite if required
     *
     * @return  array
     */
    public function export_edit_items(): array {
        return [];
    }

    /**
     * Export criterion metadata data
     * Plugins should overwrite if required
     *
     * @return  array
     */
    public function export_edit_metadata(): array {
        $results = [];

        foreach ($this->get_metadata() as $metakey => $metaval) {
            $results[] = [
                'metakey' => $metakey,
                'metavalue' => $metaval,
            ];
        }

        return $results;
    }


    /**
     * Export detail for viewing this criterion
     * This contains translated information ready for display only pages
     * Plugins should overwrite if required
     *
     * @return array
     */
    public function export_view_detail(): array {
        $result = [];

        if ($this->has_items()) {
            $result['title'] = $this->display_instance()->get_display_items_type();
            $result['items'] = $this->export_view_items();
        } else {
            $result['title'] = $this->get_title();
        }

        if ($this->has_metadata()) {
            $result['metadata'] = $this->export_view_metadata();
        }

        if ($this->has_aggregation()) {
            $result['aggregation'] = $this->export_view_aggregation();
        }

        return $result;
    }

    /**
     * Export detail for viewing aggregation used in this criterion
     * Plugins should overwrite if required
     *
     * @return string
     */
    public function export_view_aggregation(): string {
        return $this->get_summarized_aggregation();
    }


    /**
     * Export detail for viewing items associated with this criterion
     * As this will typically be the item names, each plugin should
     * overwrite this to export the correct values
     *
     * @return  [string] Array of item summaries
     */
    public function export_view_items(): array {
        return $this->get_summarized_items();
    }

    /**
     * Export detail for viewing metadata associated with this criterion
     * Plugins will typically interpret the associated metadata and export it accordingly.
     * Plugins should overwrite this function as needed
     *
     * @return  [string] Array
     */
    public function export_view_metadata(): array {
        return [];
    }

}
