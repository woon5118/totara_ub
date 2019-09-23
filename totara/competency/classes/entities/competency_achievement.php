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
 * @package totara_competency
 */

namespace totara_competency\entities;


use core\orm\collection;
use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;

/**
 * Class competency_achievement
 *
 * @property-read int $id ID
 * @property int $comp_id
 * @property int $user_id
 * @property int $assignment_id
 * @property int $scale_value_id
 * @property int $proficient
 * @property int $status
 * @property int $time_created
 * @property int $time_status
 * @property int $time_proficient
 * @property int $time_scale_value
 * @property int $last_aggregated
 */
class competency_achievement extends entity {

    public const TABLE = 'totara_competency_achievement';

    /** @var int Status when this is the latest record for an active assignment */
    public const ACTIVE_ASSIGNMENT = 0;

    /** @var int Status when this is the latest record for an archived assignment */
    public const ARCHIVED_ASSIGNMENT = 1;

    /** @var int Status when this record is not the latest for an assignment (either active or archived) */
    public const SUPERSEDED = 2;

    /**
     * Scale value
     *
     * @return belongs_to
     */
    public function value(): belongs_to {
        return $this->belongs_to(scale_value::class, 'scale_value_id');
    }

    /**
     * Get the pathway achievements that led to this competency achievement initially being created.
     *
     * @return pathway_achievement[]
     */
    public function get_achieved_via(): collection {
        return pathway_achievement::repository()
            ->join('totara_competency_achievement_via', 'id', '=', 'pathway_achievement_id')
            ->where('totara_competency_achievement_via.comp_achievement_id', $this->id)
            ->get();
    }
}
