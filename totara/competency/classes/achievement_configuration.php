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

namespace totara_competency;

use core\orm\entity\repository;
use core\orm\query\builder;
use totara_competency\entities\competency;
use totara_competency\entities\configuration_change;
use totara_competency\entities\configuration_history;
use totara_competency\entities\scale_aggregation;

/**
 * Class containing all relvant configuration information for a specific competency
 */
class achievement_configuration {

    public const DEFAULT_AGGREGATION = 'highest';

    /** @var string aggregation_type */
    private $aggregation_type = null;

    /** @var pathway[] These are active pathways t*/
    private $active_pathways = null;

    /** @var competency */
    private $competency;

    public function __construct(competency $competency) {
        $this->competency = $competency;
    }

    public function get_competency(): competency {
        return $this->competency;
    }

    /**
     * Returns true if the aggregation type is set for this competency
     *
     * @param string|null $aggregation_type specify if a specific type should be checked
     * @return bool
     */
    public function has_aggregation_type(string $aggregation_type = null): bool {
        return scale_aggregation::repository()
            ->where('comp_id', $this->get_competency()->id)
            ->when(!empty($aggregation_type), function (repository $repository) use ($aggregation_type) {
                $repository->where('type', $aggregation_type);
            })
            ->exists();
    }

    /**
     * Get the scale aggregation type used in this competency
     *
     * @return string Scale aggregation type
     */
    public function get_aggregation_type(): string {
        if (empty($this->aggregation_type)) {
            /** @var scale_aggregation $aggregation */
            $aggregation = scale_aggregation::repository()
                ->where('comp_id', $this->get_competency()->id)
                ->one();

            if ($aggregation) {
                $this->aggregation_type = $aggregation->type;
            }
        }

        // For now default to 'highest'
        return $this->aggregation_type ?? self::DEFAULT_AGGREGATION;
    }

    /**
     * Set the scale aggregation type used in this competency
     *
     * @param string $type Scale aggregation type
     * @return $this
     */
    public function set_aggregation_type(string $type): achievement_configuration {
        // Check that the type is valid - this will throw an error if invalid
        overall_aggregation_factory::get_classname($type);

        $this->aggregation_type = $type;
        return $this;
    }

    /**
     * Get the active pathways associated with this configuration.
     *
     * Loads from the database if necessary.
     *
     * @return pathway[]
     */
    public function get_active_pathways(): array {
        if (is_null($this->active_pathways)) {
            $pathways = $this->get_competency()->active_pathways;

            $this->active_pathways = [];
            foreach ($pathways as $pathway) {
                // We already have the competency entity so attach it to the pathway
                // to avoid another query further down the line
                $pathway->relate('competency', $this->get_competency());
                $this->active_pathways[$pathway->id] = pathway_factory::from_entity($pathway);
            }
        }

        return $this->active_pathways;
    }

    /**
     * Delete the specified pathways from the competency
     *
     * @param array $pathways
     * @param int|null $action_time
     */
    public function delete_pathways(array $pathways, ?int $action_time = null) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        // TODO: For now using the action_time to ensure change log and history is only created once per user action.
        //       A set of changes should ideally be initiated together with logging and history created before making
        //       all the individual pathway and aggregation changes.
        //       Will sort it out when the graphQL queries and mutators for the UI is finalised
        $this->save_configuration_history($action_time);

        foreach ($pathways as $pathway) {
            try {
                $pw = pathway_factory::fetch($pathway['type'], $pathway['id']);
            } catch (\Exception $e) {
                // Ignore non-existent pathways
                continue;
            }

            $pw->delete();
        }

        configuration_change::add_competency_entry(
            $this->competency->id,
            configuration_change::CHANGED_CRITERIA,
            $action_time
        );
        $transaction->allow_commit();
    }

    /**
     * Link and save pathways defined in the default preset to this competency if it has no existing pathways
     *
     * @param int|null $action_time Time when this action was initiated.
     * @return $this
     */
    public function link_default_preset(?int $action_time = null): achievement_configuration {
        global $DB;

        if (!empty($this->get_pathways)) {
            return $this;
        }

        // No need to save history - no previous configuration

        $transaction = $DB->start_delegated_transaction();

        $pathways = achievement_criteria::get_default_pathways($this->get_competency()->scale, $this->get_competency()->id);
        foreach ($pathways as $pw) {
            $pw->set_competency($this->competency);
            $pw->save();
        }

        // TODO: Should we maybe not log this if no action_time is provided to cater for the case
        //       when this is called for new competencies??
        configuration_change::add_competency_entry(
            $this->competency->id,
            configuration_change::CHANGED_CRITERIA,
            $action_time
        );
        $transaction->allow_commit();

        return $this;
    }

    /**
     * Save an aggregation type
     *
     * @param int|null $action_time Time when this action was initiated.
     * @return $this
     */
    public function save_aggregation(?int $action_time = null): achievement_configuration {
        builder::get_db()->transaction(function () use ($action_time) {
            $type = $this->get_aggregation_type();

            /** @var scale_aggregation $aggregation */
            $aggregation = scale_aggregation::repository()
                ->where('comp_id', $this->get_competency()->id)
                ->one();

            if (!$aggregation) {
                $aggregation = new scale_aggregation();
                $aggregation->comp_id = $this->get_competency()->id;
            } else if ($type == $aggregation->type) {
                // In case it did not change don't do anything
                return;
            }

            // TODO: For now using the action_time to ensure change log and history is only created once per user action.
            //       A set of changes should ideally be initiated together with logging and history created before making
            //       all the individual patway and aggregation changes.
            //       Will sort it out when the graphQL queries and mutators for the UI is finalised
            $this->save_configuration_history($action_time);

            $aggregation->type = $type;
            $aggregation->timemodified = time();
            $aggregation->save();

            configuration_change::add_competency_entry(
                $this->competency->id,
                configuration_change::CHANGED_AGGREGATION,
                $action_time
            );
        });

        return $this;
    }

    /**
     * Determine whether any single-use criterion is used in this competency's criteria
     *
     * @return bool
     */
    public function has_singleuse_criteria() {
        foreach ($this->get_active_pathways() as $pw) {
            if ($pw->get_classification() == pathway::PATHWAY_SINGLE_VALUE && $pw->has_singleuse_criteria()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dump the current configuration in json format for historical purposes in the history table
     *
     * @param int|null $action_time
     * @param string|null $configuration_dump
     * @return $this
     */
    public function save_configuration_history(?int $action_time = null, ?string $configuration_dump = null) {
        global $DB;

        if (!is_null($action_time)) {
            $saved = configuration_history::repository()
                ->where('comp_id', '=', $this->competency->id)
                ->where('active_from', '=', $action_time)
                ->one();

            if (!is_null($saved)) {
                // We only log a change once - expecting client (ui) to use the same action time when applying changes
                return $this;
            }
        }

        $action_time = $action_time ?? time();

        $transaction = $DB->start_delegated_transaction();

        // First set the last dump's 'active_to' timestamp
        $last_history = configuration_history::repository()
            ->where('comp_id', '=', $this->competency->id)
            ->where('active_to', '=', null)
            ->one();

        if (!is_null($last_history)) {
            $last_history->active_to = $action_time;
            $last_history->save();
        }

        $configuration_dump = $configuration_dump ?? self::get_current_configuration_dump($this->competency->id);

        $entry = new configuration_history();
        $entry->comp_id = $this->competency->id;
        $entry->active_from = $action_time ?? time();
        $entry->configuration = $configuration_dump;
        $entry->save();

        $transaction->allow_commit();

        return $this;
    }

    /**
     * Get a dump of the current configuration.
     * Data is read from the database and not the instance as the instance may
     * already have been updated
     *
     * @param int $comp_id Competency id
     * @return string Configuration dump
     */
    public static function get_current_configuration_dump(int $comp_id): string {
        global $DB;

        $params = ['comp_id' => $comp_id];

        $dumpobj = [
            'aggregation' => $DB->get_field('totara_competency_scale_aggregation', 'type', $params),
            'pathways' => pathway::dump_competency_pathways($comp_id),
        ];

        if ($dumpobj['aggregation'] === false) {
            $dumpobj['aggregation'] = self::DEFAULT_AGGREGATION;
        }

        return json_encode($dumpobj);
    }

}
