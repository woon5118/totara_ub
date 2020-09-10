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
use container_workspace\notification\workspace_notification;
use container_workspace\tracker\tracker;
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
     *
     * @var workspace
     */
    private $workspace;

    /**
     * The user's id that act as an actor interact with the workspace.
     *
     * @var int
     */
    private $user_id;

    /**
     * actor constructor.
     *
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
     * User has the manage capability.
     *
     * @return bool
     */
    public function can_manage(): bool {
        // If you can administrate, you can manage
        if ($this->can_administrate()) {
            return true;
        }

        $context = $this->workspace->get_context();
        return has_capability('container/workspace:manage', $context, $this->user_id);
    }

    /**
     * Primary owner is the owner held against the workspace
     * @return bool
     */
    public function is_primary_owner(): bool {
        return $this->workspace->get_user_id() == $this->user_id;
    }

    /**
     * You are either the owner if you have manage capabilities, or you're the primary owner
     * @return bool
     */
    public function is_owner(): bool {
        // Primary is already an owner
        if ($this->is_primary_owner()) {
            return true;
        }
        $context = $this->workspace->get_context();
        $roles = get_user_roles($context, $this->user_id);
        $workspace_owner_roles = get_archetype_roles('workspaceowner');

        // Check to see if they've already got the role
        foreach ($roles as $role) {
            if (isset($workspace_owner_roles[$role->roleid])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Like manage, but across the whole category/site. Super admin privilege
     * @return bool
     */
    public function can_administrate(): bool {
        $context = \context_coursecat::instance($this->workspace->category);
        return has_capability('container/workspace:administrate', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function is_joined(): bool {
        // Owner is considered to automatically be joined
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
        $context = $this->workspace->get_context();
        return has_capability('container/workspace:update', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_delete(): bool {
        $context = $this->workspace->get_context();
        return has_capability('container/workspace:delete', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_invite(): bool {
        if (!$this->can_view_workspace()) {
            return false;
        }

        $context = $this->workspace->get_context();
        return has_capability('container/workspace:invite', $context, $this->user_id);
    }

    /**
     * A capability and logic check whether the actor is able to join the workspace or not.
     * It depends on different workspace type that perform different check.
     *
     * @return bool
     */
    public function can_join(): bool {
        if ($this->is_joined()) {
            // Nope, members cannot join again
            return false;
        }

        if (!$this->can_view_workspace()) {
            // Cannot view the workspace means that user should not be able to join the workspace.
            return false;
        }

        $context = $this->workspace->get_context();

        if ($this->workspace->is_public()) {
            // Anyone with the capability can join the workspace
            return has_capability('container/workspace:joinpublic', $context, $this->user_id);
        }

        // Admin can join anyworkspace.
        return $this->can_administrate();
    }

    /**
     * Ability to check whether the current user's actor had been able to request to join the workspace or not.
     *
     * @return bool
     */
    public function can_request_to_join(): bool {
        if ($this->is_joined()) {
            return false;
        }

        if ($this->workspace->is_public()) {
            return false;
        }

        if (!$this->can_view_workspace()) {
            // Unable to see the workspace meaning that user should not be able to request.
            return false;
        }

        // Hidden workspaces cannot be seen to get a request
        if ($this->workspace->is_hidden()) {
            return false;
        }

        // Anyone with the capability can join the workspace
        $context = $this->workspace->get_context();
        return has_capability('container/workspace:joinprivate', $context, $this->user_id);
    }

    /**
     * @return workspace
     */
    public function get_workspace(): workspace {
        return $this->workspace;
    }

    /**
     * Returning the user's id who is interacting with the workspace so far.
     *
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

        // Anyone who can add members can accept them
        $context = $this->workspace->get_context();
        return has_capability('container/workspace:addmember', $context, $this->user_id);
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

        // Anyone who can remove members can decline them
        return $this->can_remove_members();
    }

    /**
     * @return bool
     */
    public function can_remove_members(): bool {
        $context = $this->workspace->get_context();
        return has_capability('container/workspace:removemember', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_view_workspace(): bool {
        if ($this->can_administrate()) {
            return true;
        }

        // Check for tenancy first before checking whether you had been joined
        // the workspace or not.
        if (!$this->can_view_workspace_with_tenant_check()) {
            return false;
        }

        if ($this->is_joined()) {
            return true;
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
        if (!$CFG->tenantsenabled || $this->can_administrate()) {
            // Multi tenancy is not enabled - so we skip the rest.
            return true;
        }

        $context = $this->workspace->get_context();

        // Context check first of all, handles any mismatched tenancy checks.
        // You may be a member, but it's possible shifting tenancies has blocked your access
        if ($context->is_user_access_prevented($this->user_id)) {
            return false;
        }

        // Workspace extended logic rule.
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
        if (!$this->can_view_workspace()) {
            return false;
        }

        // Note that is_joined && is_public had already been used in can view workspace.
        // However it does not mean that when u can view workspace u can view the discussion,
        // it is just another layer check to say that if u cannot see a workspace, you can not go here.
        // If we remove is_joined and is_public in this check then user can be a normal member without
        // any capability to manage the workspace then user cannot see the discussion.
        // Hence these checks have to stay here.
        if ($this->can_manage() || $this->is_joined() || $this->workspace->is_public()) {
            return true;
        }

        // Otherwise you'll need the discussion manage capability (rare)
        $context = $this->workspace->get_context();
        return has_capability('container/workspace:discussionmanage', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_create_discussions(): bool {
        // You can create if you can see them + have the create capability.
        if (!$this->can_view_discussions()) {
            return false;
        }

        $context = $this->workspace->get_context();
        return has_capability('container/workspace:discussioncreate', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_view_library(): bool {
        if ($this->can_manage()) {
            return true;
        }

        $context = \context_user::instance($this->user_id);
        $view_library = has_capability('totara/engage:viewlibrary', $context, $this->user_id);

        return $view_library && ($this->workspace->is_public() || $this->is_joined());
    }

    /**
     * @return bool
     */
    public function can_view_members(): bool {
        if ($this->can_manage()) {
            return true;
        }

        return $this->workspace->is_public() || $this->is_joined();
    }

    /**
     * @return bool
     */
    public function can_share_resources(): bool {
        $context = $this->workspace->get_context();
        return has_capability('container/workspace:libraryadd', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function can_unshare_resources(): bool {
        $context = $this->workspace->get_context();
        return has_capability('container/workspace:libraryremove', $context, $this->user_id);
    }

    /**
     * @return bool
     */
    public function has_turned_off_notification(): bool {
        $workspace_id = $this->workspace->get_id();
        return workspace_notification::is_off($workspace_id, $this->user_id);
    }

    /**
     * A function to check whether the workspace had been updated ever since the last time user visited it.
     * The parameter $last_check_time is in place to help not refetching from the table "ttr_user_lastaccess".
     * If it is not provided - then we are  going to fetch from table "ttr_user_lastaccess"
     *
     * @param int|null $last_check_time
     * @return bool
     */
    public function has_seen(?int $last_check_time = null): bool {
        if (null === $last_check_time) {
            $workspace_id = $this->workspace->get_id();

            $tracker = new tracker($this->user_id);
            $last_check_time = $tracker->get_last_time_visit_workspace($workspace_id);

            if (null === $last_check_time) {
                // User had not access to this workspace yet.
                return false;
            }
        }

        // If the last check time is not greater than the workspace time stamp then it means that user
        // had not yet visit this very workspace.
        $workspace_timestamp = $this->workspace->get_timestamp();
        return $last_check_time >= $workspace_timestamp;
    }

    /**
     * @return bool
     */
    public function can_leave_workspace(): bool {
        if ($this->is_owner()) {
            // As long as this user is not an owner of the specific workspace
            // then user is able to leave the workspace.
            return false;
        }

        return $this->is_joined();
    }

    /**
     * Referesh workspace cache.
     *
     * @return void
     */
    public function reload_workspace(): void {
        $this->workspace->reload();
    }
}