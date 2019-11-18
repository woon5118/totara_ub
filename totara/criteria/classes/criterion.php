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

use core\orm\query\builder;
use totara_criteria\entities\criteria_item;
use totara_criteria\entities\criteria_metadata;

/**
 * Base class for a single criterion.
 */

abstract class criterion {

    /** Aggregation constants */
    const AGGREGATE_ALL = 1;
    const AGGREGATE_ANY_N = 2;

    const METADATA_COMPETENCY_KEY = 'competency_id';

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
     * @return criterion $this
     */
    final public static function fetch(int $id): criterion {
        $criterion = entities\criterion::repository()->find_or_fail($id);
        return static::fetch_from_entity($criterion);
    }

    /**
     * Fetch specific criterion from the database
     *
     * @param entities\criterion $criterion $criterion
     * @return criterion $this
     */
    final public static function fetch_from_entity(entities\criterion $criterion): criterion {
        $instance = new static();

        if ($criterion->plugin_type != $instance->get_plugin_type()) {
            throw new \coding_exception("The specified criterion id is for another type of criterion");
        }

        $instance->set_id($criterion->id);
        $instance->set_aggregation_method($criterion->aggregation_method ?? static::AGGREGATE_ALL);
        $instance->set_aggregation_params($criterion->aggregation_params ?? []);
        $instance->set_last_evaluated($criterion->last_evaluated);

        $instance->fetch_items($criterion);
        $instance->fetch_metadata($criterion);

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
     * @return int|null
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Set the id of the criterion
     *
     * @param int|null $id New criterion id
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
        if (!in_array($aggregation_method, [static::AGGREGATE_ALL, static::AGGREGATE_ANY_N])) {
            throw new \coding_exception('Invalid aggregation method used');
        }

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
        return $this->aggregation_params ?? [];
    }

    /**
     * Set the aggregation params
     *
     * Public scope for testing purposes
     *
     * @param array|string $params. This can be passed as array or json encoded string
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
    abstract public function get_items_type();

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
     * @param int[] $itemids Id of items to link to the criterion
     * @return $this
     */
    public function set_item_ids(array $itemids): criterion {
        $this->item_ids = $itemids;
        return $this;
    }

    /**
     * Add items to the criterion
     *
     * @param int[] $item_ids Id of items to link to the criterion
     * @return criterion
     */
    public function add_items(array $item_ids): criterion {
        $newitems = array_diff($item_ids, $this->item_ids);
        $this->item_ids = array_merge($this->item_ids, $newitems);

        return $this;
    }

    /**
     * Remove the specified items
     *
     * @param int[] $item_ids Ids of items to remove
     * @return criterion
     */
    public function remove_items(array $item_ids): criterion {
        $this->item_ids = array_diff($this->item_ids, $item_ids);
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
     * @param \stdClass[] $metadata Metadata metakey/metavalue pairs
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
     * @param \stdClass[]|criteria_metadata[] $metadata Metadata metakey/metavalue pairs
     * @return criterion
     */
    public function add_metadata($metadata): criterion {
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
     * @param int[] $metakeys Keys of metadata to remove
     * @return criterion
     */
    public function remove_metadata(array $metakeys): criterion {
        foreach ($metakeys as $metakey) {
            unset($this->metadata[$metakey]);
        }

        return $this;
    }

    /**
     * Set the associated competency
     *
     * @param int $competency_id
     * @return criterion
     */
    public function set_competency_id(int $competency_id): criterion {
        $this->add_metadata([
            (object)['metakey' => self::METADATA_COMPETENCY_KEY, 'metavalue' => $competency_id],
        ]);
        return $this;
    }

    /**
     * Get the associated competency
     *
     * @return int|null
     */
    public function get_competency_id(): ?int {
        foreach ($this->get_metadata() as $metakey => $metaval) {
            if ($metakey == static::METADATA_COMPETENCY_KEY) {
                return $metaval;
            }
        }

        return null;
    }

    /**
     * Get last_evaluated
     *
     * @return int|null
     */
    public function get_last_evaluated(): ?int {
        return $this->last_evaluated;
    }

    /**
     * Set last_evaluated
     *
     * @param int|null $last_evaluated
     * @return criterion
     */
    public function set_last_evaluated(?int $last_evaluated): criterion {
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
     *
     * @param entities\criterion $criterion
     */
    private function fetch_items(entities\criterion $criterion) {
        $this->item_ids = [];

        $rows = $criterion->items;
        foreach ($rows as $row) {
            // item_id is the actual item_id, (course_id, etc)
            $this->item_ids[] = $row->item_id;
        }
    }

    /**
     * Load the metadata of this criterion
     *
     * @param entities\criterion $criterion
     */
    private function fetch_metadata(entities\criterion $criterion) {
        $this->add_metadata($criterion->metadata);
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
     * Validate the criterion attributes
     *
     * @return string|null Error description
     */
    protected function validate(): ?string {
        return null;
    }

    /**
     * Save this criterion and all its items
     *
     * @return $this
     */
    public function save(): criterion {
        $err_message = $this->validate();
        if (!is_null($err_message)) {
            throw new \coding_exception($err_message);
        }

        if (!$this->is_dirty()) {
            return $this;
        }

        $exists = (bool) $this->get_id();

        $criterion = $exists ? new entities\criterion($this->get_id()) : new entities\criterion();
        $criterion->plugin_type = $this->get_plugin_type();
        $criterion->aggregation_method = $this->get_aggregation_method();
        $criterion->aggregation_params = json_encode($this->get_aggregation_params());
        $criterion->criterion_modified = time();
        $criterion->save();

        $this->set_id($criterion->id);

        if (!$exists && empty($this->item_ids)) {
            $this->update_items();
        }

        $this->save_items();
        $this->save_metadata();

        return $this;
    }

    /**
     * Save the criterion items
     */
    private function save_items() {
        $current_items = criteria_item::repository()
            ->where('criterion_id', $this->get_id())
            ->get()
            ->all();

        $current_items = array_column($current_items, 'id', 'item_id');

        foreach ($this->get_item_ids() as $item_id) {
            $this->save_criterion_item($item_id);
            unset($current_items[$item_id]);
        }

        if (!empty($current_items)) {
            criteria_item::repository()
                ->where('id', $current_items)
                ->delete();
        }
    }

    /**
     * Update derived items (e.g. currently linked courses, current child competencies, etc.)
     */
    abstract public function update_items(): criterion;

    /**
     * Save the metadata of this criterion
     */
    private function save_metadata() {
        $current_keys = criteria_metadata::repository()
            ->where('criterion_id', $this->get_id())
            ->get()
            ->all();

        $current_keys = array_column($current_keys, 'id', 'metakey');

        if (!empty($this->metadata)) {
            foreach ($this->metadata as $metakey => $metaval) {
                if (isset($current_keys[$metakey])) {
                    $metadata = new criteria_metadata($current_keys[$metakey]);
                    unset($current_keys[$metakey]);
                } else {
                    $metadata = new criteria_metadata();
                }
                $metadata->metavalue = $metaval;
                $metadata->criterion_id = $this->id;
                $metadata->metakey = $metakey;
                $metadata->save();
            }
        }

        if (!empty($current_keys)) {
            criteria_metadata::repository()
                ->where('id', $current_keys)
                ->delete();
        }
    }

    /**
     * Save the criterion_item if it doesn't exist
     *
     * @param int $item_id Item id to add
     * @return int $id Id of the criterion_item row
     */
    private function save_criterion_item(int $item_id): int {
        // Ensure there is only one of each item
        $criterion = entities\criteria_item::repository()
            ->where('item_type', $this->get_items_type())
            ->where('item_id', $item_id)
            ->where('criterion_id', $this->id)
            ->one();

        if (empty($criterion)) {
            // TODO: Do we need separate constants for the items?

            // Insert the criterion_item
            $criterion = new entities\criteria_item();
            $criterion->criterion_id = $this->id;
            $criterion->item_type = $this->get_items_type();
            $criterion->item_id = $item_id;
            $criterion->save();
        }

        return $criterion->id;
    }

    /**
     * Save the last_evaluated date of the criterion
     *
     * @return $this
     */
    public function save_last_evaluated(): criterion {
        // Not doing anything if not saved previously or last_evaluated not yet set
        if (empty($this->get_id() or empty($this->get_last_evaluated()))) {
            return $this;
        }

        $criterion = new entities\criterion($this->get_id());
        $criterion->last_evaluated = $this->get_last_evaluated();
        $criterion->save();

        return $this;
    }


    /**
     * Delete the criterion
     */
    public function delete() {
        if (empty($this->get_id())) {
            // Never saved before
            return;
        }

        builder::get_db()->transaction(function () {
            // Delete all the items and metadata
            $this->delete_items();
            $this->delete_metadata();

            // Delete the actual criterion
            entities\criterion::repository()
                ->where('id', $this->get_id())
                ->delete();

            // Unset the id as it doesn't exist anymore
            $this->set_id(null);
        });
    }


    /**
     * Delete the items
     */
    private function delete_items() {
        global $DB;

        // TODO replace with ORM
        $sql =
            "DELETE
               FROM {totara_criteria_item_record}
              WHERE criterion_item_id IN
                (SELECT id
                   FROM {totara_criteria_item}
                  WHERE criterion_id = :criterionid)";
        $DB->execute($sql, ['criterionid' => $this->get_id()]);

        criteria_item::repository()
            ->where('criterion_id', $this->get_id())
            ->delete();
    }

    /**
     * Delete the metadata
     */
    private function delete_metadata() {
        criteria_metadata::repository()
            ->where('criterion_id', $this->get_id())
            ->delete();
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

        $sql = "
            SELECT i.id, COALESCE(r.criterion_met, 0) as criterion_met
            FROM {totara_criteria_item} i
            LEFT JOIN {totara_criteria_item_record} r 
                ON i.id = r.criterion_item_id 
                    AND r.user_id = :userid
            WHERE i.criterion_id = :criterionid 
                AND i.item_type = :itemtype
        ";

        $params = [
            'userid' => $user_id,
            'criterionid' => $this->get_id(),
            'itemtype' => $this->get_items_type()
        ];

        $existing_records = $DB->get_records_sql_menu($sql, $params);

        return $existing_records;
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
