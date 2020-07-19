<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\orm\entity\repository;
use core\orm\query\builder;
use core\orm\query\table;
use mod_perform\models\activity\track_status;
use mod_perform\state\activity\active;

/**
 * Repository for track user assignment entities
 */
final class track_user_assignment_repository extends repository {

    /**
     * @param int $track_id
     * @return $this
     */
    public function filter_by_track_id(int $track_id): self {
        $this->where('track_id', $track_id);

        return $this;
    }

    /**
     * Filter for active records, essentially those which are not deleted
     *
     * @return $this
     */
    public function filter_by_active(): self {
        $this->where('deleted', false);

        return $this;
    }

    /**
     * Filter for records where the period restriction matches the current time.
     *
     * @return $this
     */
    public function filter_by_time_interval(): self {
        $now = time();
        $this->where_not_null('period_start_date')
            ->where('period_start_date', '<=', $now)
            ->where(function (builder $builder) use ($now) {
                $builder->where_null('period_end_date')
                    ->or_where('period_end_date', '>', $now);
            });

        return $this;
    }

    /**
     * Filter for records that either don't have any instances or
     * the repeat config is such that it potentially can have more.
     *
     * @return $this
     */
    public function filter_by_possibly_has_subject_instances_to_create(): self {
        if (!$this->has_join(track::TABLE, 'fbat')) {
            $this->join([track::TABLE, 'fbat'], 'track_id', 'id');
        }

        // Create a subquery getting the count of subject_instances and the id of the most
        // recent one for each track_user_assignment.
        $grouped_instances_sub_query = builder::table(subject_instance::TABLE)
            ->select([
                'max(id) as max_id',
                'count(track_user_assignment_id) as instance_count'
            ])
            ->group_by('track_user_assignment_id');

        // Join that subquery to subject_instance table so we have only the records for most recent subject_instances.
        $instances_query = builder::table(subject_instance::TABLE)
            ->select([
                'grouped_si.instance_count',
                'track_user_assignment_id',
                'progress',
                'completed_at',
                'created_at',
                'id'
            ])
            ->join((new table($grouped_instances_sub_query))->as('grouped_si'), 'id', 'grouped_si.max_id');

        // We are interested in records that either don't have any subject instances
        // or have repeating enabled for the track without having hit the repeat-limit.
        $this->left_join((new table($instances_query))->as('si'), 'id', 'si.track_user_assignment_id')
            ->where(function (builder $builder) {
                $builder->where_null('si.id')
                    ->or_where(function (builder $builder) {
                        $builder->where('fbat.repeating_is_enabled', true)
                            ->where('fbat.repeating_is_limited', false);
                    })
                    ->or_where(function (builder $builder) {
                        $builder->where('fbat.repeating_is_enabled', true)
                            ->where('fbat.repeating_is_limited', true)
                            ->where_field('fbat.repeating_limit', '>', 'si.instance_count');
                    });
            });

        // Add some helpful fields to the result.
        $this->add_select([
            'si.created_at as instance_created_at',
            'si.progress as instance_progress',
            'si.instance_count',
            'si.completed_at as instance_completed_at',
            // Add relevant track columns since we're already joining track table (faster than eager loading track relation).
            'fbat.repeating_is_enabled',
            'fbat.repeating_type',
            'fbat.repeating_offset',
            'fbat.due_date_is_enabled',
            'fbat.due_date_is_fixed',
            'fbat.due_date_fixed',
            'fbat.due_date_offset',
        ]);

        return $this;
    }

    /**
     * Return all user assignments which do not have any subject instances
     *
     * @return $this
     */
    public function filter_by_no_subject_instances(): self {
        if (!$this->has_join(subject_instance::TABLE, 'fbnsi')) {
            $this->left_join([subject_instance::TABLE, 'fbnsi'], 'id', 'track_user_assignment_id')
               ->where_null('fbnsi.id');
        }

        return $this;
    }

    /**
     * Return all user assignment which have an active track and an active activity
     *
     * @return $this
     */
    public function filter_by_active_track_and_activity(): self {
        if (!$this->has_join(track::TABLE, 'fbat')) {
            $this->join([track::TABLE, 'fbat'], 'track_id', 'id');
        }
        if (!$this->has_join(activity::TABLE, 'fbaa')) {
            $this->join([activity::TABLE, 'fbaa'], 'fbat.activity_id', 'id');
        }

        $this->where('fbat.status', track_status::ACTIVE)
            ->where('fbaa.status', active::get_code());
        return $this;
    }

    /**
     * Exclude records waiting for schedule synchronisation.
     *
     * @return $this
     */
    public function filter_by_does_not_need_schedule_sync(): self {
        if (!$this->has_join(track::TABLE, 'fbat')) {
            $this->join([track::TABLE, 'fbat'], 'track_id', 'id');
        }
        $this->where('fbat.schedule_needs_sync', false);

        return $this;
    }
}
