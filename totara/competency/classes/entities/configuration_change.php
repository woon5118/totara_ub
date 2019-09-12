<?php

namespace totara_competency\entities;

use core\orm\entity\entity;

/**
 * @property-read int $id ID
 * @property int $comp_id
 * @property int $assignment_id
 * @property int $time_changed
 * @property string $change_type
 * @property string $related_info
 */
class configuration_change extends entity {

    public const TABLE = 'totara_competency_configuration_change';

    // Configuration change constants
    public const CHANGED_CRITERIA = 'criteria_changed';
    public const CHANGED_AGGREGATION = 'aggregation_changed';
    public const CHANGED_MIN_PROFICIENCY = 'min_proficiency_changed';


    /**
     * Log a configuration change
     *
     * @param string $change_type Type of change to log
     * @param ?int $action_time Action time. Only 1 configuration change log entry is created for a specific time
     * @return configuration_change
     */
    public static function add_competency_entry(
        int $competency_id,
        string $change_type,
        ?int $action_time = null
    ): configuration_change {
        $valid_types = [
            self::CHANGED_AGGREGATION,
            self::CHANGED_CRITERIA,
            self::CHANGED_MIN_PROFICIENCY,
        ];

        if (!in_array($change_type, $valid_types)) {
            throw new \coding_exception('Invalid configuration change type');
        }

        if (!is_null($action_time)) {
            $logged = configuration_change::repository()
                ->where('comp_id', '=', $competency_id)
                ->where('change_type', '=', $change_type)
                ->where('time_changed', '=', $action_time)
                ->one();

            if (!is_null($logged)) {
                /** @var configuration_change $logged */
                // We only log a change once - expecting client (ui) to use the same action time when applying changes
                return $logged;
            }
        }

        $entry = new configuration_change();
        $entry->comp_id = $competency_id;
        $entry->change_type = $change_type;
        $entry->time_changed = $action_time ?? time();
        $entry->save();

        return $entry;
    }

    /**
     * Add entries for each competency that uses a scale where the min proficiency has changed.
     *
     * @param int $scale_id
     * @param int $new_min_proficiency_id
     */
    public static function min_proficiency_change(int $scale_id, int $new_min_proficiency_id) {
        // Get all competencies using this scale.
        $competencies = competency::repository()
            ->join(competency_framework::TABLE, competency::TABLE . 'frameworkid', '=', competency_framework::TABLE . 'id')
            ->join('comp_scale_assignments', competency_framework::TABLE . 'id', '=', 'comp_scale_assignments.frameworkid')
            ->where('comp_scale_assignments.scaleid', '=', $scale_id)
            ->get();

        $time = time();

        foreach ($competencies as $competency) {
            $entry = new configuration_change();
            $entry->comp_id = $competency->id;
            $entry->change_type = self::CHANGED_MIN_PROFICIENCY;
            $entry->time_changed = $time;
            $entry->related_info = json_encode(['new_min_proficiency_id' => $new_min_proficiency_id]);
            $entry->save();
        }
    }

    /**
     * Get the related info for this configuration change as an associative array.
     *
     * @return array
     */
    public function get_decoded_related_info(): array {
        if (empty($this->related_info)) {
            return [];
        }

        $decoded = json_decode($this->related_info, true);

        // related_info may have been 'NULL' in which case the above empty check would have returned false.
        if (empty($decoded)) {
            return [];
        }

        return $decoded;
    }
}
