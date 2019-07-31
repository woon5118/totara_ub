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
namespace container_workspace\interactor\discussion;

use container_workspace\discussion\discussion;
use container_workspace\interactor\workspace\interactor as workspace_interactor;
use container_workspace\workspace;
use totara_reaction\loader\reaction_loader;

/**
 * Discussion interactor. Note that the interactor class should not be calling to the resolver class.
 * As this should be a low level API before the resolver.
 */
final class interactor {
    /**
     * @var discussion
     */
    private $discussion;

    /**
     * @var int
     */
    private $actor_id;

    /**
     * interactor constructor.
     * @param discussion    $discussion
     * @param int|null      $actor_id
     */
    public function __construct(discussion $discussion, ?int $actor_id = null) {
        global $USER;

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $this->discussion = $discussion;
        $this->actor_id = $actor_id;
    }

    /**
     * @param int $discussion_id
     * @param null|int $actor_id
     *
     * @return interactor
     */
    public static function from_discussion_id(int $discussion_id, ?int $actor_id = null): interactor {
        $discussion = discussion::from_id($discussion_id);
        return new static($discussion, $actor_id);
    }

    /**
     * The logics are quite simple:
     * + Site admin user is a super user
     * + If you left the workspace, then you are not eligible to update the discussion despite of yoy are the author.
     * + If you are still within the workspace, and you are the owner of this very discussion then you are good.
     *
     * @return bool
     */
    public function can_update(): bool {
        if (is_siteadmin($this->actor_id)) {
            // Site admin is a super power actor
            return true;
        }

        $workspace_id = $this->discussion->get_workspace_id();
        $workspace_interactor = workspace_interactor::from_workspace_id($workspace_id, $this->actor_id);

        if (!$workspace_interactor->is_joined()) {
            return false;
        }

        $discussion_owner_id = $this->discussion->get_user_id();
        if ($discussion_owner_id == $this->actor_id) {
            return true;
        }

        // Everyone else cannot do the update.
        return false;
    }

    /**
     * The logics are quite simple:
     * + Site admin user is a super user
     * + If you left the workspace, then you are not eligible to delete the discussion despite of you are the author.
     * + If you are still within the workspace, and you are the owner of this very discussion then you are good.
     * + If you are the owner of the workspace then you are able to delete the discussion.
     *
     * @return bool
     */
    public function can_delete(): bool {
        if (is_siteadmin($this->actor_id)) {
            // Site admin is a super power actor
            return true;
        }

        $workspace = $this->discussion->get_workspace();
        $workspace_interactor = new workspace_interactor($workspace, $this->actor_id);

        if (!$workspace_interactor->is_joined()) {
            return false;
        }

        $discussion_owner_id = $this->discussion->get_user_id();
        if ($discussion_owner_id == $this->actor_id) {
            // You are the author of the discussion. Keep moving on then.
            return true;
        }

        // Other than that workspace owner can do this
        return $workspace_interactor->is_owner();
    }

    /**
     * @return int
     */
    public function get_discussion_id(): int {
        return $this->discussion->get_id();
    }

    /**
     * @return int
     */
    public function get_workspace_id(): int {
        return $this->discussion->get_workspace_id();
    }

    /**
     * At this point, we are only to check if the actor is a member of a workspace or not.
     * @return bool
     */
    public function can_comment(): bool {
        $workspace_id = $this->discussion->get_workspace_id();
        $interactor = workspace_interactor::from_workspace_id($workspace_id, $this->actor_id);

        return $interactor->is_joined();
    }

    /**
     * Returning the actor's id
     * @return int
     */
    public function get_user_id(): int {
        return $this->actor_id;
    }

    /**
     * @return bool
     */
    public function reacted(): bool {
        $area = discussion::AREA;
        $instance_id = $this->get_discussion_id();
        $component = workspace::get_type();

        return reaction_loader::exist($instance_id, $component, $area, $this->actor_id);
    }

    /**
     * Only member of the workspace is able to react to the discussion
     *
     * @return bool
     */
    public function can_react(): bool {
        // Creator of the post should not be able to like the post itself.
        $owner_id = $this->discussion->get_user_id();
        if ($owner_id == $this->actor_id) {
            return false;
        }

        $workspace_id = $this->discussion->get_workspace_id();
        $workspace_interactor = workspace_interactor::from_workspace_id($workspace_id, $this->actor_id);

        return $workspace_interactor->is_joined();
    }

    /**
     * Only site admin or the owner of the workspace has the ability to pin for now.
     *
     * @return bool
     */
    public function can_pin(): bool {
        if (is_siteadmin($this->actor_id)) {
            return true;
        }

        $workspace_id = $this->discussion->get_workspace_id();
        $interactor = workspace_interactor::from_workspace_id($workspace_id, $this->actor_id);

        return $interactor->is_owner();
    }
}