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
use mod_perform\models\activity\activity as activity_model;
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
            $this->join([track::TABLE, 'fbat'], 'track_id', 'id')
                ->join([activity::TABLE, 'fbaa'], 'fbat.activity_id', 'id')
                ->where('fbat.status', track_status::ACTIVE)
                ->where('fbaa.status', active::get_code());
        }

        return $this;
    }

}
