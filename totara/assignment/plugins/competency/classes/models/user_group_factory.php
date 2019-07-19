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

namespace tassign_competency\models;

/**
 * Factory to return an instance of a specific user group model identified by a type
 */
class user_group_factory {

    /**
     * @param string $type
     * @param int $id
     * @return user_group
     * @throws \coding_exception
     */
    public static function create(string $type, int $id): user_group {
        /** @var user_group $class_name */
        $class_name = "\\tassign_competency\\models\\user_group\\{$type}";
        if (class_exists($class_name) && is_subclass_of($class_name, user_group::class)) {
            return $class_name::load_by_id($id);
        }
        throw new \coding_exception('Unknown user group given!');
    }

}