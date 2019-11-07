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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package tassign_competency
 */

namespace totara_competency\models\user_group;

use totara_competency\models\user_group;
use totara_assignment\entities\position as position_entity;
use totara_assignment\user_groups;

class position extends user_group {

    public static function load_by_id(int $id): user_group {
        /** @var position_entity $position */
        $position = position_entity::repository()->find($id);
        if ($position) {
            $is_deleted = false;
            $name = $position->display_name;
        } else {
            $is_deleted = true;
            $name = get_string('deleted_audience', 'tassign_competency');
        }
        return new static($id, $name, $is_deleted);
    }

    public function can_view(): bool {
        $systemcontext = \context_system::instance();
        return has_capability('totara/hierarchy:viewposition', $systemcontext);
    }

    public function get_type(): string {
        return user_groups::POSITION;
    }

}