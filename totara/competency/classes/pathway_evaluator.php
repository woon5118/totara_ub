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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;


use totara_competency\entities\pathway_achievement;

abstract class pathway_evaluator {

    /** @var pathway $pathway */
    protected $pathway;

    /** @var pathway_evaluator_user_source $user_id_source */
    protected $user_id_source = null;

    /**
     * Constructor.
     *
     * @param pathway $pathway
     * @param pathway_user_source $user_source
     */
    public function __construct(pathway $pathway, pathway_evaluator_user_source $user_id_source) {
        // Making use of an update_operation value to enable us to determine which users have
        // updated criteria associated with this pathway. Without it, each pathway will also re-aggregating all
        // users marked as changed in previously processed pathways

        /** @var string $operation_value */
        $operation_value = $pathway->get_path_type() . '__' . $pathway->get_id();
        $user_id_source->set_competency_id_value($pathway->get_competency()->id);
        $user_id_source->set_update_operation_value($operation_value);
        $this->user_id_source = $user_id_source;

        $this->pathway = $pathway;
    }

    /**
     * Evaluate user achievements for the specific pathway of all assigned users
     *
     * @param int|null $evaluation_time
     */
    public function aggregate(?int $evaluation_time = null) {

        if (is_null($evaluation_time)) {
            $evaluation_time = time();
        }

        // First archive pathway_achievements of users no longer assigned
        $this->user_id_source->archive_non_assigned_achievements($this->pathway, $evaluation_time);
        // And mark newly assigned users as having changes
        $this->user_id_source->mark_newly_assigned_users($this->pathway);

        // Now evaluate achievements for assigned users to mark which ones were changed
        $this->evaluate_user_achievements($evaluation_time);

        // reaggregate users that have changes
        $this->reaggregate(($evaluation_time));
    }

    /**
     * Evaluate the value achieved for all assigned users
     * Each plugin should override this method if it requires specific evaluation steps
     * @param int $evaluation_time
     */
    protected function evaluate_user_achievements(int $evaluation_time) {
        $this->user_id_source->mark_users_to_reaggregate($this->pathway);
    }

    /**
     * reaggregate all users with changed completion values
     * Plugins should override this method if it requires additional joins or actions
     *
     * @param int $evaluation_time
     */
    protected function reaggregate(int $evaluation_time) {
        global $DB;

        /** @var \moodle_recordset $to_reaggregate */
        $to_reaggregate = $this->user_id_source->get_users_to_reaggregate($this->pathway, $evaluation_time);

        // We do not update has_changed even if this pathway doesn't result in a new value as another pathway may also
        // have set the flag. Yes, it may mean that we re-aggregate too many users on the higher levels, but at least
        // we will not skip any that need re-aggregation
        foreach ($to_reaggregate as $record) {
            $aggregated_achievement_detail = $this->pathway->aggregate_current_value($record->user_id);

            if (!is_null($record->achievement_id)) {
                $achievement = new pathway_achievement($record->achievement_id);
                if ($aggregated_achievement_detail->get_scale_value_id() == $record->scale_value_id) {
                    $achievement->last_aggregated = $evaluation_time;
                    $achievement->save();
                } else {
                    $achievement->archive();
                    $this->create_achievement($record->user_id, $evaluation_time, $aggregated_achievement_detail);
                }
            } else {
                $this->create_achievement($record->user_id, $evaluation_time, $aggregated_achievement_detail);
            }
        }
        $to_reaggregate->close();
    }

    /**
     * Create a new pathway_achievement_record
     *
     * @param int $user_id
     * @param int $evaluation_time
     * @param base_achievement_detail $achievement_detail
     */
    private function create_achievement(int $user_id, int $evaluation_time, base_achievement_detail $achievement_detail) {
        $new_achievement = new pathway_achievement();
        $new_achievement->pathway_id = $this->pathway->get_id();
        $new_achievement->user_id = $user_id;
        $new_achievement->scale_value_id = $achievement_detail->get_scale_value_id();
        $new_achievement->status = pathway_achievement::STATUS_CURRENT;
        $new_achievement->last_aggregated = $evaluation_time;
        $new_achievement->date_achieved = $evaluation_time;
        $new_achievement->related_info = json_encode($achievement_detail->get_related_info());
        $new_achievement->save();
    }

}
