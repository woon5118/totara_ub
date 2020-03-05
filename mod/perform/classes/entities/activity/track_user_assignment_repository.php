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

}
