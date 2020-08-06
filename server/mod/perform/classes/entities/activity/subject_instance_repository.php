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

class subject_instance_repository extends repository {

    /**
     * Filter by an activity_id
     *
     * @param int $activity_id
     * @return $this
     */
    public function filter_by_activity_id(int $activity_id): self {
        // Only filter if the
        if ($this->has_join(track_user_assignment::TABLE, 'fbai_tua')
            && $this->has_join(track::TABLE, 'fbai_tr')) {
            debugging('This filter function has already been applied to this builder instance.', DEBUG_DEVELOPER);
        }
        return $this->join([track_user_assignment::TABLE, 'fbai_tua'], 'track_user_assignment_id', 'id')
            ->join([track::TABLE, 'fbai_tr'], '"fbai_tua".track_id', 'id')
            ->where('"fbai_tr".activity_id', $activity_id);
    }

    /**
     * Filter by subject user id.
     *
     * @param int $subject_user_id
     * @return subject_instance_repository
     */
    public function filter_by_subject_user(int $subject_user_id) {
        return $this->where('subject_user_id', $subject_user_id);
    }

    /**
     * Filter to subject instances at or below a specified context.
     *
     * @param \context $context
     * @return $this
     */
    public function filter_by_context(\context $context): self {
        // No need for restrictions for system context.
        if (get_class($context) == 'context_system') {
            return $this;
        }
        if (!$this->has_join('context')) {
            $this->join('perform_track_user_assignment', 'perform_subject_instance.track_user_assignment_id', '=', 'id')
                ->join('perform_track', 'perform_track_user_assignment.track_id', '=', 'id')
                ->join('perform', 'perform_track.activity_id', '=', 'id')
                ->join('course', 'perform.course', '=', 'id')
                ->join('context', function (builder $joining) {
                    $joining->where_field('course.id', 'context.instanceid')
                        ->where('context.contextlevel', '=', CONTEXT_COURSE);
                });
        }
        return $this->where(function (builder $builder) use ($context) {
            $builder->where('context.id', $context->id)
            ->or_where_like_starts_with('context.path', "{$context->path}/");
        });
    }
}