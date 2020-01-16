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

use totara_core\hook\base;

class competency_achievement_updated_bulk extends base {

    /**
     * @var array
     */
    protected $user_ids = [];

    /**
     * @var int
     */
    protected $competency_id;

    public function __construct(int $competency_id) {
        $this->competency_id = $competency_id;
    }

    public function add_user_id(int $user_id, int $proficient) {
        $this->user_ids[$user_id] = $proficient;
    }

    public function get_competency_id(): int {
        return $this->competency_id;
    }

    public function get_user_ids(): array {
        return array_keys($this->user_ids);
    }

    public function get_user_ids_proficient(): array {
        return $this->user_ids;
    }

}