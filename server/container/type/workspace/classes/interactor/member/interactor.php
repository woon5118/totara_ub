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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
namespace container_workspace\interactor\member;

use container_workspace\workspace;
use core_container\factory;
use container_workspace\member\member;

/**
 * A helper class that is constructed with {user_enrolment}'s id and the user's id, which helps to fetch
 * all the available actions that a user can interact with a membership of other.
 */
final class interactor {
    /**
     * @var member
     */
    private $member;

    /**
     * The actor's id.
     * @var int
     */
    private $user_id;

    /**
     * interactor constructor.
     * @param member    $member
     * @param int|null  $user_id
     */
    public function __construct(member $member, ?int $user_id = null) {
        global $USER;

        if (null === $user_id || 0 === $user_id) {
            $user_id = $USER->id;
        }

        $this->member = $member;
        $this->user_id = $user_id;
    }

    /**
     * @return bool
     */
    public function can_remove(): bool {
        $workspace_id = $this->member->get_workspace_id();

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);
        $owner_id = $workspace->get_user_id();

        if ($owner_id == $this->user_id) {
            // Actor is an owner. We need to check whether this member is an actor/owner or not.
            $member_user_id = $this->member->get_user_id();
            if ($this->user_id == $member_user_id) {
                return false;
            }

            // Owner can do anything except for deleting him/her-self out of the workspace.
            return true;
        }

        $context = $workspace->get_context();
        return has_capability('container/workspace:removemember', $context, $this->user_id);
    }

    /**
     * @return int
     */
    public function get_user_id(): int {
        return $this->user_id;
    }

    /**
     * @return member
     */
    public function get_member(): member {
        return $this->member;
    }
}