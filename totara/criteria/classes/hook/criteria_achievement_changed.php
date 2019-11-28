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
 * @package totara_criteria
 */

namespace totara_criteria\hook;

use totara_core\hook\base;

/**
 * Event to be triggered when a user's achievement of a specific criterion changed
 * The change can be either that the user now satisfies the criteria, or no longer satisfy the criteria
 */
class criteria_achievement_changed extends base {

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var array
     */
    protected $criteria_ids;

    public function __construct(int $user_id, array $criteria_ids) {
        $this->user_id = $user_id;
        $this->criteria_ids = $criteria_ids;
    }

    public function get_user_id(): int {
        return $this->user_id;
    }

    public function get_criteria_ids(): array {
        return $this->criteria_ids;
    }
}
