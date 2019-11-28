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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\hook;

use totara_competency\entities\competency_achievement;
use totara_core\hook\base;

class competency_achievement_updated extends base {

    /**
     * @var array
     */
    protected $achievement;

    /**
     * @param competency_achievement $achievement
     */
    public function __construct(competency_achievement $achievement) {
        // Do not store the reference to the achievement directly
        // as this should be read-only
        $this->achievement = $achievement->to_array();
    }

    public function get_achievement(): array {
        return $this->achievement;
    }

}