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
 * @package totara_competency
 */

namespace totara_competency;


use totara_competency\entities\competency_achievement;
use totara_competency\entities\pathway_achievement;
use totara_competency\entities\scale_value;
use totara_competency\event\competency_achievement_updated;

/**
 * Class aggregator
 *
 * Aggregates the competency values for users based on the given achievement configuration.
 */
final class competency_achievement_aggregator {

    /** @var achievement_configuration */
    private $achievement_configuration;

    /** @var pathway_aggregation */
    private $aggregation_instance;

    /** @var null|int[] */
    private $proficient_scale_value_ids;

    /**
     * aggregator constructor.
     * @param achievement_configuration $achievement_configuration
     */
    public function __construct(achievement_configuration $achievement_configuration) {
        $this->achievement_configuration = $achievement_configuration;
    }

    /**
     * @return achievement_configuration
     */
    public function get_achievement_configuration(): achievement_configuration {
        return $this->achievement_configuration;
    }

    private function get_aggregation_instance(): pathway_aggregation {
        if (is_null($this->aggregation_instance)) {
            $type = $this->get_achievement_configuration()->get_aggregation_type();
            $this->aggregation_instance = pathway_aggregation_factory::create($type);
            $this->aggregation_instance->set_pathways($this->get_achievement_configuration()->get_active_pathways());
        }

        return $this->aggregation_instance;
    }

    public function set_aggregation_instance(pathway_aggregation $aggregation_instance): competency_achievement_aggregator {
        $this->aggregation_instance = $aggregation_instance;
        return $this;
    }

    /**
     * Aggregate the competency values for a given set of users.
     *
     * Will add or update the comp_record entries for the users.
     *
     * @param int[] $user_ids
     */
    public function aggregate($user_ids) {
        global $DB;

        if (empty($user_ids)) {
            return;
        }

        $competency_id = $this->get_achievement_configuration()->get_competency()->id;

        // We must get this time now rather than as we make the record because otherwise
        // there is risk of some pathway achievements being completed at the same time slipping through.
        $aggregation_time = time();

        $this->get_aggregation_instance()->set_user_ids($user_ids)->aggregate();

        [$insql, $params] = $DB->get_in_or_equal($user_ids, SQL_PARAMS_NAMED);

        $params['newstatus'] = competency_achievement::ACTIVE_ASSIGNMENT;
        $params['compid'] = $competency_id;

        $user_assignments = $DB->get_records_sql(
            'SELECT tacu.id,
                    tacu.user_id,
                    tacu.assignment_id,
                    COALESCE(ca.id, NULL) AS comp_achievement_id,
                    COALESCE(ca.scale_value_id, NULL) AS scale_value_id
                 FROM {totara_competency_assignment_users} tacu
            LEFT JOIN {totara_competency_achievement} ca
                   ON tacu.user_id = ca.user_id
                  AND tacu.assignment_id = ca.assignment_id
                  AND ca.status = :newstatus
                WHERE tacu.competency_id = :compid
                  AND tacu.user_id ' . $insql,
            $params
        );

        foreach ($user_assignments as $user_assignment) {
            $value_id = $this->get_aggregation_instance()->get_achieved_value_id($user_assignment->user_id);
            $achieved_via = $this->get_aggregation_instance()->get_achieved_via($user_assignment->user_id);

            if (is_null($user_assignment->comp_achievement_id)) {
                $previous_comp_achievement = null;
            } else {
                $previous_comp_achievement = new competency_achievement($user_assignment->comp_achievement_id);
            }

            if ($user_assignment->scale_value_id != $value_id || is_null($previous_comp_achievement)) {
                $new_comp_achievement = new competency_achievement();
                $new_comp_achievement->comp_id = $competency_id;
                $new_comp_achievement->user_id = $user_assignment->user_id;
                $new_comp_achievement->assignment_id = $user_assignment->assignment_id;
                $new_comp_achievement->scale_value_id = $value_id;
                $new_comp_achievement->proficient = (int) $this->is_proficient($value_id);
                $new_comp_achievement->status = competency_achievement::ACTIVE_ASSIGNMENT;
                $new_comp_achievement->time_created = $aggregation_time;
                $new_comp_achievement->time_status = $aggregation_time;
                $new_comp_achievement->time_proficient = $aggregation_time;
                $new_comp_achievement->time_scale_value = $aggregation_time;
                $new_comp_achievement->last_aggregated = $aggregation_time;
                $new_comp_achievement->save();

                foreach ($achieved_via as $pathway_achievement) {
                    $via_record = new \stdClass();
                    $via_record->comp_achievement_id = $new_comp_achievement->id;
                    $via_record->pathway_achievement_id = $pathway_achievement->id;
                    $DB->insert_record('totara_competency_achievement_via', $via_record);
                }

                if (!is_null($previous_comp_achievement)) {
                    $previous_comp_achievement->status = competency_achievement::SUPERSEDED;
                    $previous_comp_achievement->time_status = $aggregation_time;
                    $previous_comp_achievement->save();
                }

                $achieved_via_ids = array_map(
                    function (pathway_achievement $achievement) {return $achievement->id;},
                    $achieved_via
                );
                competency_achievement_updated::create(
                    [
                        'context' => \context_system::instance(),
                        'objectid' => $new_comp_achievement->id,
                        'relateduserid' => $user_assignment->user_id,
                        'other' => ['competency_id' => $competency_id, 'achieved_via_ids' => $achieved_via_ids],
                    ]
                )->trigger();
            } else {
                // No change.
                $previous_comp_achievement->last_aggregated = $aggregation_time;
                $previous_comp_achievement->save();
            }
        }

        // Get active comp_records with no associated user assignment record.
        $params = [
            'compid' => $this->get_achievement_configuration()->get_competency()->id,
            'newstatus' => competency_achievement::ACTIVE_ASSIGNMENT,
        ];

        $to_archive = $DB->get_fieldset_sql(
            'SELECT ca.id
            FROM {totara_competency_achievement} ca
       LEFT JOIN {totara_competency_assignment_users} tacu
              ON ca.assignment_id = tacu.assignment_id
             AND ca.user_id = tacu.user_id
           WHERE tacu.id IS NULL
             AND ca.comp_id = :compid
             AND ca.status = :newstatus',
            $params);

        if (!empty($to_archive)) {
            [$archiveinsql, $params] = $DB->get_in_or_equal($to_archive, SQL_PARAMS_NAMED);
            $params['newstatus'] = competency_achievement::ARCHIVED_ASSIGNMENT;
            $params['timestatus'] = $aggregation_time;

            $DB->execute(
                'UPDATE {totara_competency_achievement}
                    SET status = :newstatus,
                        time_status = :timestatus
                  WHERE id ' . $archiveinsql,
                $params
            );
        }
    }

    /**
     * Checks whether a given scale value is considered proficient.
     *
     * @param int $value_id
     * @return bool True if the scale value is proficient.
     */
    private function is_proficient($value_id): bool {
        if (is_null($this->proficient_scale_value_ids)) {
            $value = new scale_value($value_id);
            if ($value->proficient) {
                $this->proficient_scale_value_ids[$value->id] = $value->id;
                return true;
            }
        }

        return isset($this->proficient_scale_value_ids[$value_id]);
    }
}