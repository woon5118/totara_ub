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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;


use totara_competency\entities\competency;
use totara_competency\entities\pathway as pathway_entity;
use totara_competency\entities\pathway_achievement;
use totara_competency\entities\scale_value;

/**
 * Base class for pathway plugins
 */
abstract class pathway {

    /* Status constants */
    public const PATHWAY_STATUS_ACTIVE = 0;
    public const PATHWAY_STATUS_ARCHIVED = 3;

    /* Classification constants */
    public const PATHWAY_MULTI_VALUE = 0;
    public const PATHWAY_SINGLE_VALUE = 1;

    public const CLASSIFICATION = self::PATHWAY_MULTI_VALUE;

    /** @var int */
    private $id = 0;

    /** @var competency Competency this relates to. */
    private $competency;

    /** @var int sortorder Sorting order of this pathway within the set */
    private $sortorder = 0;

    /** @var string $path_type Type of pathway instance. Obtained from class */
    private $path_type;

    /** @var int Specific instance id */
    private $path_instance_id;

    /** @@var int $status  */
    private $status = self::PATHWAY_STATUS_ACTIVE;

    /********************************************************************
     * Instantiation
     ********************************************************************/

    /**
     * Constructor.
     */
    final public function __construct() {
        $reflect = new \ReflectionClass($this);
        $this->path_type = $reflect->getShortName();
    }

    /**
     * Fetch the instance data from the db
     *
     * @param  int $id
     * @return static
     */
    final public static function fetch(int $id): pathway {
        global $DB;

        $record = $DB->get_record('totara_competency_pathway', ['id' => $id], '*', MUST_EXIST);

        return static::from_record($record);
    }

    /**
     * Set the instance attributes from the record
     *
     * @param \stdClass $record
     * @return static
     */
    final public static function from_record($record): pathway {
        $classname = "\\pathway_{$record->path_type}\\{$record->path_type}";
        if (!class_exists($classname) || !is_subclass_of($classname, 'totara_competency\pathway')) {
            throw new \coding_exception("Pathway type '{$record->path_type}' does not exist or is not enabled.");
        }

        /** @var pathway $pathway */
        $pathway = new $classname();
        if ($pathway->path_type !== $record->path_type) {
            throw new \coding_exception('Path type mismatch');
        }

        $competency = competency::repository()->find($record->comp_id);
        if (!$competency) {
            throw new \coding_exception('Competency for given pathway not found');
        }

        $pathway->set_id($record->id);
        $pathway->set_competency($competency);
        $pathway->set_sortorder($record->sortorder);
        $pathway->set_path_instance_id($record->path_instance_id);
        $pathway->set_status($record->status);

        $pathway->fetch_configuration();

        return $pathway;
    }

    /**
     * Load the data specific to the type of pathway.
     */
    abstract protected function fetch_configuration();


    /****************************************************************************
     * Saving
     ****************************************************************************/

    private function ensure_sortorder_exists() {
        global $DB;

        if (empty($this->sortorder)) {
            $sql = "SELECT MAX(sortorder)
                      FROM {totara_competency_pathway}
                     WHERE comp_id = :comp_id
                       AND status = :active";
            // Todo: Once we're properly implementing sortorder, we'll need to consider whether draft should be
            // included as well or instead of.
            $highest_current = $DB->get_field_sql(
                $sql,
                ['comp_id' => $this->get_competency()->id, 'active' => static::PATHWAY_STATUS_ACTIVE]
            );

            // Explicit check of false since the value could be 0.
            if ($highest_current === false || $highest_current === null) {
                $this->set_sortorder(1);
            } else {
                $this->set_sortorder($highest_current + 1);
            }
        }
    }


    /**
     * Save the pathway
     *
     * @return $this
     */
    final public function save(): pathway {
        global $DB;

        if (empty($this->get_competency())) {
            throw new \coding_exception('Unknown Competency');
        }

        $this->ensure_sortorder_exists();

        // Check whether anything changed
        if (empty($this->get_id()) || $this->configuration_is_dirty()) {
            if ($this->is_active()) {
                $this->save_configuration();
            }
        } else if (!empty($this->get_id())) {
            $old_record = $DB->get_record('totara_competency_pathway', ['id' => $this->get_id()]);
            // Only save if certain values change
            if ($old_record->sortorder == $this->get_sortorder()
                && $old_record->status == $this->get_status()
                && $old_record->path_instance_id == $this->get_path_instance_id()
            ) {
                return $this;
            }
        }

        // If we get here, we have either a new pathway or something changed
        $record = new \stdClass();
        $record->comp_id = $this->competency->id;
        $record->sortorder = $this->get_sortorder();
        $record->path_type = $this->get_path_type();
        $record->path_instance_id = $this->get_path_instance_id();
        $record->status = $this->status;
        $record->pathway_modified = time();

        if ($this->get_id()) {
            $record->id = $this->get_id();
            $DB->update_record('totara_competency_pathway', $record);
        } else {
            $this->id = $DB->insert_record('totara_competency_pathway', $record);
        }

        return $this;
    }

    /**
     * Has the pathway specific configuration changed?
     *
     * @return bool
     */
    abstract protected function configuration_is_dirty(): bool;

    /**
     * Save the pathway specific configuration
     */
    abstract protected function save_configuration();

    /**
     * Archive the pathway
     *
     * @return $this
     */
    final private function archive(): pathway {
        if (empty($this->get_id())) {
            return $this;
        }

        // We only archive the pathway.
        // Configuration is deleted
        $this->delete_configuration();
        $this->set_status(static::PATHWAY_STATUS_ARCHIVED);
        $this->save();

        return $this;
    }

    /**
     * 'Delete' the pathway and all its associated configuration
     *
     */
    final public function delete() {
        if ($this->is_active()) {
            return $this->archive();
        }

        return $this;
    }

    /**
     * Delete the pathway specific detail
     */
    abstract protected function delete_configuration();

    /**
     * Delete all pathway data relating to a specific competency.
     *
     * @param competency|int $competency Competency entity or ID
     */
    final public static function delete_all_for_competency($competency) {
        global $DB;
        $DB->transaction(function () use ($competency) {
            $pathways = pathway_entity::repository()
                ->where('comp_id', $competency->id ?? $competency);

            foreach ($pathways->get() as $pathway) {
                static::fetch($pathway->id)->delete_configuration();

                pathway_achievement::repository()
                    ->where('pathway_id', $pathway->id)
                    ->delete();
            }

            $pathways->delete();
        });
    }


    /****************************************************************************
     * Getters and setters
     ****************************************************************************/

    /**
     * Get the pathway id
     * @return ?int Id of the pathway
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Set the pathway id
     *
     * @param ?int $id New pathway id
     * @return $this
     */
    private function set_id(?int $id): pathway {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the competency
     * @return competency
     */
    public function get_competency(): competency {
        return $this->competency;
    }

    /**
     * Set the competency
     *
     * @param competency $competency
     * @return $this
     */
    public function set_competency(competency $competency): pathway {
        $this->competency = $competency;

        return $this;
    }

    /**
     * Get the sortorder
     *
     * @return int Current sortorder
     */
    public function get_sortorder(): int {
        return $this->sortorder;
    }

    /**
     * Set the sortorder
     *
     * @param int $sortorder
     * @return $this
     */
    public function set_sortorder(int $sortorder): pathway {
        $this->sortorder = $sortorder;
        return $this;
    }

    /**
     *  Get the pathway type
     *
     * @return string
     */
    public function get_path_type(): string {
        return $this->path_type;
    }

    // No set_path_type - derived from class

    /**
     * Get instance id
     *
     * @return ?int Path instance id
     */
    public function get_path_instance_id(): ?int {
        return $this->path_instance_id;
    }

    /**
     * Set the instance id
     *
     * @param ?int Path instance id
     * @return $this
     */
    protected function set_path_instance_id(?int $instance_id): pathway {
        $this->path_instance_id = $instance_id;
        return $this;
    }

    /**
     * Is this pathway active?
     *
     * @return bool
     */
    public function is_active(): bool {
        return $this->status == static::PATHWAY_STATUS_ACTIVE;
    }

    /**
     * Is this pathway archived?
     *
     * @return bool
     */
    public function is_archived(): bool {
        return $this->status == static::PATHWAY_STATUS_ARCHIVED;
    }

    /**
     * Return the pathway status
     *
     * @return int
     */
    public function get_status(): int {
        return $this->status;
    }

    /**
     * Return a string presentation of the pathway status
     *
     * @return string
     */
    public function get_status_name(): string {
        $string_keys = [
            static::PATHWAY_STATUS_ACTIVE => 'pathwaystatusactive',
            static::PATHWAY_STATUS_ARCHIVED => 'pathwaystatusarchived',
        ];

        return strtoupper(get_string($string_keys[$this->status], 'totara_competency'));
    }

    /**
     * Set the pathway status
     *
     * @param int $status New status
     * @return $this
     * @throws \coding_exception
     */
    protected function set_status(int $status): pathway {
        switch ($status) {
            case static::PATHWAY_STATUS_ACTIVE:
            case static::PATHWAY_STATUS_ARCHIVED:
                $this->status = $status;
                break;

            default:
                throw new \coding_exception('Unknown pathway status');
        }

        return $this;
    }

    /**
     * Get pathway classification (Single or Multi value)
     *
     * @return int
     */
    public function get_classification(): int {
        return static::CLASSIFICATION;
    }

    /**
     * Return a string presentation of the pathway classification
     *
     * @return string
     */
    public function get_classification_name(): string {
        $string_keys = [
            static::PATHWAY_MULTI_VALUE => 'pathwaymultivalue',
            static::PATHWAY_SINGLE_VALUE => 'pathwaysinglevalue',
        ];

        return strtoupper(get_string($string_keys[static::CLASSIFICATION], 'totara_competency'));
    }

    /**
     * Returns the scale value associated with this instance of this pathway.
     *
     * Override this method for pathways that can be set to correspond to particular value.
     *
     * @return ?scale_value A null return value indicates that any scale value may be returned
     */
    public function get_scale_value(): ?scale_value {
        return null;
    }

    /**
     * Return a summary of the set of criteria associated with this pathway.
     * Criteria detail should contain a title with optional items and aggregation
     *
     * This should be overwritten by plugins to provide their information
     *
     * @return array List of summarized criteria associated with the pathway
     */
    public function get_summarized_criteria_set(): array {
        return [(object)['item_type' => $this->get_title()]];
    }

    /**
     * Get the achievement detail object for this pathway type
     *
     * @return base_achievement_detail
     */
    public function get_achievement_detail(): base_achievement_detail {
        $detail_class = pathway_factory::get_namespace($this->get_path_type()) . '\\achievement_detail';
        if (!is_subclass_of($detail_class, base_achievement_detail::class)) {
            throw new \coding_exception('Not detail class found', "No achievement_detail class found for {$this->get_path_type()}");
        }
        return new $detail_class();
    }


    /*******************************************************************************************************
     * Data exporting
     *******************************************************************************************************/

    /**
     * Export the template name and data for editing this pathway
     *
     * @return array
     */
    public function export_pathway_edit_template(): array {
        $result = $this->export_edit_overview();
        $result['pathway_templatename'] = $this->get_edit_template();

        return $result;
    }

    /**
     * Export the template name and data for viewing this pathway
     *
     * @return array
     */
    public function export_pathway_view_template(): array {
        $result = $this->export_view_detail();
        $result['pathway_templatename'] = $this->get_view_template();

        return $result;
    }


    /**
     * Return the name of the template to use for editing this pathway
     * Plugins should overwrite if required
     *
     * @return string Template name
     */
    public function get_edit_template(): string {
        return '';
    }

    /**
     * Return the name of the template to use for viewing this pathway
     * Plugins should overwrite if required
     *
     * @return string Template name
     */
    public function get_view_template(): string {
        return '';
    }

    /**
     * Return the tite for the pathway
     * Plugins should overwrite if required
     *
     * @return string
     */
    public function get_title(): string {
        $classname = (new \ReflectionClass($this))->getShortName();
        return ucfirst(get_string('pluginname', "pathway_{$classname}"));
    }

    /**
     * Return a short description of the content of this pathway
     * Plugins should overwrite if required
     *
     * @return string Short description
     */
    public function get_short_description(): string {
        return '';
    }

    /**
     * Export the pathway overview.
     * This contains only enough information to list the pathway
     * Plugins should overwrite if required
     *
     * @return array
     */
    public function export_edit_overview(): array {
        $result = [
            'id' => $this->get_id(),
            'type' => $this->get_path_type(),
            'title' => $this->get_title(),
            'sortorder' => $this->get_sortorder(),
        ];

        if ($this->get_classification() == static::PATHWAY_SINGLE_VALUE) {
            $result['scalevalue'] = $this->get_scale_value()->id;
        }

        return $result;
    }

    /**
     * Export detail for editing the pathway
     * This contains detail information
     * Plugins should overwrite if required
     *
     * @return array Exported detail
     */
    public function export_edit_detail(): array {
        return $this->export_edit_overview();
    }

    /**
     * Export detail for viewing this pathway
     * This contains translated information ready for display only pages
     * Plugins should overwrite if required
     *
     * @return array
     */
    public function export_view_detail(): array {
        return [
            'id' => $this->get_id(),
            'title' => $this->get_title(),
        ];
    }

    /**
     * Get the current configurations for all active pathways belonging to the specified competency in an associative array
     *
     * @param int $comp_id Competency id
     * @return array
     */
    public static function dump_competency_pathways(int $comp_id) {
        global $DB;

        $result = $DB->get_records('totara_competency_pathway', ['comp_id' => $comp_id, 'status' => static::PATHWAY_STATUS_ACTIVE]);
        foreach ($result as $id => $pathway) {
            if (!is_null($pathway->path_instance_id)) {
                $pathway->detail = pathway_factory::dump_pathway_configuration($pathway->path_type, $pathway->path_instance_id);
            } else {
                $pathway->detail = null;
            }
        }

        return $result;
    }

    /**
     * Retrieve the pathway configuration
     *
     * @param ?int $id Instance id
     * @return \stdClass | null
     */
    public static function dump_pathway_configuration(?int $id = null) {
        return null;
    }


    /**************************************************************************
     * User specific
     **************************************************************************/

    /**
     * Calculates what value the user has achieved for this pathway.
     *
     * The value is included in the pathway's implementation of base_achievement_detail. This allows
     * us to get the scale value id from that instance and also information related to how the
     * value was achieved.
     *
     * @param int $user_id
     * @return base_achievement_detail as implemented by the pathway plugin in question
     */
    abstract public function aggregate_current_value(int $user_id): base_achievement_detail;

}
