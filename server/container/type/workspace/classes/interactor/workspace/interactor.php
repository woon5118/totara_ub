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

namespace container_workspace\interactor\workspace;

use container_workspace\entity\workspace_member_request;
use container_workspace\loader\member\loader;
use container_workspace\workspace;
use core_container\factory;

/**
 * A helper class that is constructed with workspace's id and the user's id, which helps to fetch
 * all the available actions that a user can interact with a workspace.
 *
 * Note that this will also include ability to fetch the state of this user against the workspace.
 */
final class interactor {
    /**
     * The workspace's id that we are going to check against.
     * @var workspace
     */
    private $workspace;

    /**
     * The user's id that act as an actor interact with the workspace.
     * @var int
     */
    private $user_id;

    /**
     * actor constructor.
     * @param workspace $workspace
     * @param int|null $user_id     If null is set for this field, then user in session will be used.
     */
    public function __construct(workspace $workspace, ?int $user_id = null) {
        global $USER;

        if (null === $user_id || 0 === $user_id) {
            $user_id = $USER->id;
        }

        $this->workspace = $workspace;
        $this->user_id = $user_id;
    }

    /**
     * @param int $workspace_id
     * @param int|null $user_id
     *
     * @return interactor
     */
    public static function from_workspace_id(int $workspace_id, ?int $user_id = null): interactor {
        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);

        if (!$workspace->is_typeof(workspace::get_type())) {
            throw new \coding_exception(
                "Cannot instantiate an object of interactor from a container that is not a workspace: {$workspace_id}"
            );
        }

        return new static($workspace, $user_id);
    }

    /**
     * Whether this interactor is an owner of the workspace or not.
     * @return bool
     */
    public function is_owner(): bool {
        $owner_id = $this->workspace->get_user_id();
        if (null === $owner_id) {
            // Workspace does not have owner.
            return false;
        }

        return $owner_id == $this->user_id;
    }

    /**
     * @return bool
     */
    public function is_joined(): bool {
        if ($this->is_owner()) {
            // Save us another cycle of fetching.
            return true;
        }

        $workspace_id = $this->workspace->get_id();
        $member = loader::get_for_user($this->user_id, $workspace_id);
        if (null !== $member && $member->is_active()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function can_update(): bool {
        if ($this->is_owner()) {
            return true;
        }

        $context = $this->workspace->get_context();
        return has_capability('container/workspace:update', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_delete(): bool {
        if ($this->is_owner()) {
            return true;
        }

        $context = $this->workspace->get_context();
        return has_capability('container/workspace:delete', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_invite(): bool {
        if ($this->is_owner()) {
            // Owner can invite other user.
            return true;
        }

        if (!$this->can_view_workspace()) {
            return false;
        }

        $context = $this->workspace->get_context();
        return has_capability('container/workspace:invite', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_join(): bool {
        if ($this->is_owner() || $this->is_joined()) {
            // Nope, owner and member is already part of the workspace.
            return false;
        }

        if (!$this->can_view_workspace()) {
            // Cannot view the workspace means that user should not be able to join the workspace.
            return false;
        }

        if (!$this->workspace->is_public()) {
            return false;
        }

        // Everyone is able to join the workspace so far now.
        return true;
    }

    /**
     * Ability to check whether the current user's actor had been able to request to join the workspace or not.
     *
     * @return bool
     */
    public function can_request_to_join(): bool {
        if ($this->is_owner() || $this->is_joined()) {
            return false;
        }

        if ($this->workspace->is_public()) {
            return false;
        } else if (!$this->can_view_workspace()) {
            // Unable to see the workspace meaning that user should not be able to request.
            return false;
        }

        if ($this->workspace->is_hidden()) {
            // Has to check if the user is able to see hidden workspace or not.
            $context = $this->workspace->get_context();
            if (!has_capability('moodle/course:viewhiddencourses', $context, $this->user_id)) {
                return false;
            }
        }

        // Every one has the ability to request to join.
        return true;
    }

    /**
     * @return workspace
     */
    public function get_workspace(): workspace {
        return $this->workspace;
    }

    /**
     * Returning the user's id who is interacting with the workspace so far.
     * @return int
     */
    public function get_user_id(): int {
        return $this->user_id;
    }

    /**
     * @return bool
     */
    public function can_accept_member_request(): bool {
        if ($this->workspace->is_public()) {
            return false;
        }

        if (is_siteadmin($this->user_id)) {
            return true;
        }

        $owner_id = $this->workspace->get_user_id();
        return $owner_id == $this->user_id;
    }

    /**
     * @return bool
     */
    public function can_decline_member_request(): bool {
        if ($this->workspace->is_public()) {
            return false;
        }

        if (is_siteadmin($this->user_id)) {
            return true;
        }

        $owner_id = $this->workspace->get_user_id();
        return $owner_id == $this->user_id;
    }

    /**
     * @return bool
     */
    public function can_view_workspace(): bool {
        if ($this->is_owner() || $this->is_joined()) {
            return true;
        }

        if (!$this->can_view_workspace_with_tenant_check()) {
            return false;
        }

        if ($this->workspace->is_public() || $this->workspace->is_private()) {
            return true;
        }

        if ($this->workspace->is_hidden()) {
            $context = $this->workspace->get_context();
            return has_capability('moodle/course:viewhiddencourses', $context, $this->user_id);
        }

        throw new \coding_exception("Unsupported visibility type");
    }

    /**
     * @return bool
     */
    public function can_view_workspace_with_tenant_check(): bool {
        global $CFG, $DB;
        if (!$CFG->tenantsenabled || is_siteadmin($this->user_id)) {
            // Multi tenancy is not enabled - so we skip the rest.
            return true;
        }

        $context = $this->workspace->get_context();
        $tenant_id = $context->tenantid;

        if (null !== $tenant_id) {
            // Check if the user is in the same tenant with the workspace or not.
            $check_sql = '
                    SELECT 1 FROM "ttr_cohort_members" cm
                    INNER JOIN "ttr_tenant" t ON t.cohortid = cm.cohortid
                    WHERE t.id = :tenant_id
                    AND cm.userid = :user_id
                ';

            $result = $DB->record_exists_sql(
                $check_sql,
                [
                    'tenant_id' => $tenant_id,
                    'user_id' => $this->user_id
                ]
            );

            if (!$result) {
                return false;
            }
        } else if ($CFG->tenantsisolated) {
            // Isolation mode is on - we just need to check if user is a part of tenant or not.
            $result = $DB->record_exists_sql(
                'SELECT 1 FROM "ttr_user" WHERE id = :user_id AND tenantid IS NOT NULL',
                ['user_id' => $this->user_id]
            );

            if ($result) {
                // This user is within a tenant - hence false to be returned.
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function has_requested_to_join(): bool {
        if ($this->is_owner()) {
            // Skip the fetching.
            return false;
        }

        $repository = workspace_member_request::repository();

        $workspace_id = $this->workspace->get_id();
        $entity = $repository->get_current_pending_request(
            $workspace_id,
            $this->user_id
        );

        return null !== $entity;
    }

    /**
     * @return bool
     */
    public function can_view_discussions(): bool {
        if (is_siteadmin($this->user_id)) {
            return true;
        }

        return $this->is_owner() || $this->is_joined() || $this->workspace->is_public();
    }

    /**
     * @return bool
     */
    public function can_view_library(): bool {
        if (is_siteadmin($this->user_id)) {
            return true;
        }

        return $this->is_owner() || $this->is_joined() || $this->workspace->is_public();
    }

    /**
     * @return bool
     */
    public function can_view_members(): bool {
        if (is_siteadmin($this->user_id)) {
            return true;
        }

        return $this->is_owner() || $this->is_joined() || $this->workspace->is_public();
    }
}