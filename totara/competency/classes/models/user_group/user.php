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
use totara_assignment\entities\user as user_entity;
use totara_assignment\user_groups;

class user extends user_group {

    public static function load_by_id(int $id): user_group {
        /** @var user_entity $user */
        $user = new user_entity($id);
        if (!$user->deleted) {
            $name = fullname((object)$user->to_array());
        } else {
            $name = get_string('deleted_user', 'tassign_competency');
        }
        return new static($id, $name, $user->deleted);
    }

    public function can_view(): bool {
        $usercontext = \context_user::instance($this->id);
        return has_capability('moodle/user:viewdetails', $usercontext);
    }

    public function get_type(): string {
        return user_groups::USER;
    }
}