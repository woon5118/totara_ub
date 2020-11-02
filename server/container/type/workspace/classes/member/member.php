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
namespace container_workspace\member;

use container_workspace\exception\enrol_exception;
use container_workspace\interactor\workspace\interactor;
use container_workspace\tracker\tracker;
use container_workspace\workspace;
use core\entity\user_enrolment;
use core_container\factory;
use totara_core\visibility_controller;
use core\task\manager as task_manager;
use container_workspace\task\notify_added_to_workspace_task;
use stdClass;
use core_user;
use coding_exception;

/**
 * A model class for workspace's member.
 */
final class member {
    /**
     * Need to cache workspace_id that this member is belonging to.
     *
     * @var int|null
     */
    private $workspace_id;

    /**
     * @var user_enrolment
     */
    private $user_enrolment;

    /**
     * @var stdClass|null
     */
    private $user_record;

    /**
     * member constructor.
     * @param user_enrolment $user_enrolment
     */
    private function __construct(user_enrolment $user_enrolment) {
        if (!$user_enrolment->exists()) {
            throw new \coding_exception("Cannot construct a class of member that is not already exist");
        }

        $this->user_enrolment = $user_enrolment;
        $this->workspace_id = null;
        $this->user_record = null;
    }

    /**
     * @param int $user_id
     * @param int $workspace_id
     *
     * @return member
     */
    public static function from_user(int $user_id, int $workspace_id): member {
        global $DB;

        $sql = '
            SELECT ue.* FROM "ttr_user_enrolments" ue
            INNER JOIN "ttr_enrol" e ON ue.enrolid = e.id
            WHERE ue.userid = :user_id
            AND e.courseid = :workspace_id
        ';

        $params = [
            'user_id' => $user_id,
            'workspace_id' => $workspace_id
        ];

        $record = $DB->get_record_sql($sql, $params, MUST_EXIST);
        $entity = new user_enrolment($record);

        $member = new static($entity);
        $member->workspace_id = $workspace_id;

        return $member;
    }

    /**
     * @param \stdClass $record
     *
     * @return member
     */
    public static function from_record(\stdClass $record): member {
        $workspace_id = null;

        if (property_exists($record, 'workspace_id')) {
            $workspace_id = $record->workspace_id;
            unset($record->workspace_id);
        }

        $entity = new user_enrolment($record);

        $member = new static($entity);
        $member->workspace_id = $workspace_id;

        return $member;
    }

    /**
     * If the actor is match with the owner of the workspace, then the actor will be enrolled as workspaceowner
     * role. Otherwise student role will be used for any other user.
     *
     * @param workspace $workspace
     * @param int|null $actor_id If this is null, then $user in session will be used.
     *
     * @return member
     */
    public static function join_workspace(workspace $workspace, ?int $actor_id = null): member {
        global $USER, $CFG;

        if (empty($actor_id)) {
            $actor_id = $USER->id;
        }

        $owner_id = $workspace->get_user_id();
        $is_owner = ($actor_id == $owner_id);

        $interactor = new interactor($workspace, $actor_id);

        if (!$workspace->is_public() && (!$is_owner && !$interactor->can_join())) {
            throw new \coding_exception("Cannot join the non-public workspace");
        }

        if ($CFG->tenantsenabled) {
            if (!$interactor->can_view_workspace_with_tenant_check()) {
                throw new \coding_exception("Cannot join the workspace that is not in the same tenant");
            }
        }

        // Join as a learner/student, unless they're the owner
        $archetype = 'student';
        if ($is_owner) {
            $archetype = 'workspaceowner';
        }

        $roles = get_archetype_roles($archetype);

        if (empty($roles)) {
            throw new \coding_exception("No role for archetype '{$archetype}'");
        }

        $role = reset($roles);

        $manager = $workspace->get_enrolment_manager();
        $manager->self_enrol_user($actor_id, $role->id);

        // Assign view hidden capability for hidden workspace.
        static::assign_view_hidden_capability($workspace, $role->id);

        $workspace_id = $workspace->get_id();
        return static::from_user($actor_id, $workspace_id);
    }

    /**
     * Target user is being added to the workspace by the actor.
     *
     * @param workspace $workspace
     * @param int       $user_id
     * @param bool      $trigger_notification
     * @param int|null  $actor_id
     *
     * @return member
     */
    public static function added_to_workspace(workspace $workspace, int $user_id,
                                              bool $trigger_notification = true, ?int $actor_id = null): member {
        global $USER;
        if (empty($actor_id)) {
            $actor_id = $USER->id;
        }

        $owner_id = $workspace->get_user_id();
        if ($user_id == $owner_id) {
            throw enrol_exception::on_manual_enrol();
        }

        $roles = get_archetype_roles('student');
        if (empty($roles)) {
            throw new \coding_exception("No roles for archetype 'student'");
        }

        $role = reset($roles);
        $member = static::do_add_to_workspace($workspace, $user_id, $role->id, $actor_id);

        if ($trigger_notification) {
            // Queue adhoc task to send the message out to the target user.
            $task = notify_added_to_workspace_task::from_member($member);
            task_manager::queue_adhoc_task($task);
        }

        return $member;
    }

    /**
     * @param workspace $workspace
     * @param int $user_id
     * @param int $role_id
     * @param int $actor_id
     *
     * @return member
     */
    private static function do_add_to_workspace(workspace $workspace, int $user_id,
                                                    int $role_id, int $actor_id): member {
        global $CFG;
        if ($CFG->tenantsenabled) {
            // Only checking this if multi-tenancy is enabled.
            $target_workspace_interactor = new interactor($workspace, $user_id);
            if (!$target_workspace_interactor->can_view_workspace_with_tenant_check()) {
                // Check if the newly going-to-be-added user is able to see the workspace or not.
                throw new \coding_exception("Target user is not able to see the workspace");
            }
        }

        $manager = $workspace->get_enrolment_manager();
        $manager->manual_enrol_user($user_id, $role_id, $actor_id);

        static::assign_view_hidden_capability($workspace, $role_id);

        $workspace_id = $workspace->get_id();
        return static::from_user($user_id, $workspace_id);
    }

    /**
     * Hidden workspaces requires the viewhiddencourses capability in the course for the role.
     * Regular user has no specific hiddencourses capability, and there's no workspacemember role
     * yet. This will be removed when workspacemember role is introduced.
     *
     * @param workspace $workspace
     * @param int $role_id
     *
     * @return void
     */
    private static function assign_view_hidden_capability(workspace $workspace, int $role_id): void {
        if (!$workspace->is_hidden()) {
            // Skip if the workspace is not a hidden one.
            return;
        }

        $context = $workspace->get_context();
        assign_capability(
            'moodle/course:viewhiddencourses',
            CAP_ALLOW,
            $role_id,
            $context->id
        );

        // We need to rebuild the visibility
        $map = visibility_controller::course()->map();
        $map->recalculate_map_for_instance($workspace->get_id());
    }

    /**
     * @return int
     */
    public function get_workspace_id(): int {
        global $DB;

        if (null === $this->workspace_id || 0 === $this->workspace_id) {
            $enrol_id = $this->user_enrolment->enrolid;
            $this->workspace_id = $DB->get_field('enrol', 'courseid', ['id' => $enrol_id], MUST_EXIST);
        }

        return $this->workspace_id;
    }

    /**
     * @return workspace
     */
    public function get_workspace(): workspace {
        $workspace_id = $this->get_workspace_id();

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);
        return $workspace;
    }

    /**
     * @return bool
     */
    public function is_suspended(): bool {
        return $this->user_enrolment->is_suspended();
    }

    /**
     * @return bool
     */
    public function is_active(): bool {
        return $this->user_enrolment->is_active();
    }

    /**
     * @param int|null $actor_id
     * @return bool
     */
    public function leave(?int $actor_id = null): bool {
        global $USER;

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $user_id = $this->user_enrolment->userid;
        if ($user_id != $actor_id) {
            throw new \coding_exception("Actor trying to leave and the user's enrolment is not sync");
        }

        if ($this->is_suspended()) {
            // Enrolment is already suspended.
            return true;
        }

        $workspace_id = $this->get_workspace_id();

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);
        $manager = $workspace->get_enrolment_manager();

        $manager->suspend_enrol($this->user_enrolment);
        $this->user_enrolment->refresh();

        // Remove the tracker for user's id.
        $tracker = new tracker($user_id);
        $tracker->clear($workspace_id);

        return $this->is_suspended();
    }

    /**
     * Remove a user from a workspace.
     * Note: There are no capability checks performed here.
     *
     * @param int|null $actor_id
     * @return bool
     */
    public function removed_from_workspace(?int $actor_id = null): bool {
        global $USER;

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $user_id = $this->user_enrolment->userid;
        if ($actor_id == $user_id) {
            // As the same actor with this enrolment should not be able to remove himself.
            throw new \coding_exception(
                "The actor's id and the user enrolment is the same. Should use leave method instead"
            );
        }

        if ($this->is_suspended()) {
            return true;
        }

        $workspace_id = $this->get_workspace_id();

        /** @var workspace $workspace */
        $workspace = factory::from_id($workspace_id);
        $interactor = interactor::from_workspace_id($workspace_id, $actor_id);
        // To remove another user you must either manage the workspace or can remove members
        if (!$interactor->can_manage() && !$interactor->can_remove_members()) {
            throw new \coding_exception("No capability to remove the member");
        }

        $manager = $workspace->get_enrolment_manager();
        $manager->suspend_enrol($this->user_enrolment);

        $this->user_enrolment->refresh();
        return $this->is_suspended();
    }

    /**
     * The function only changes the role assignment to user, from owner role to a learner role.
     * It does not remove the user's enrolment.
     *
     * @param int|null $actor_id
     * @return void
     */
    public function demote_from_owner(?int $actor_id = null): void {
        global $USER;

        if (empty($actor_id)) {
            $actor_id = $USER->id;
        }

        $workspace_id = $this->get_workspace_id();
        $actor_workspace_interactor = interactor::from_workspace_id($workspace_id, $actor_id);

        if (!$actor_workspace_interactor->can_manage()) {
            throw new \coding_exception("No capability to demote an owner");
        }

        $context = \context_course::instance($workspace_id);

        // Workspace's owner role.
        $owner_roles = get_archetype_roles('workspaceowner');
        if (empty($owner_roles)) {
            throw new \coding_exception("There are no workspace's owner roles");
        }

        $current_roles = get_user_roles($context, $this->user_enrolment->userid);
        foreach ($current_roles as $current_role) {
            if (!isset($owner_roles[$current_role->roleid])) {
                continue;
            }

            // Unassign the user's role for workspace owner.
            role_unassign(
                $current_role->roleid,
                $this->user_enrolment->userid,
                $context->id,
                'container_workspace'
            );
        }

        // Then assign the workspace member role.
        $learner_roles = get_archetype_roles('student');
        if (empty($learner_roles)) {
            throw new \coding_exception("There are no learner roles found");
        }

        $learner_role = reset($learner_roles);
        role_assign(
            $learner_role->id,
            $this->user_enrolment->userid,
            $context->id,
            'container_workspace'
        );
    }

    /**
     * @param int|null $actor_id
     * @return void
     */
    public function promote_to_owner(?int $actor_id = null): void {
        global $USER;
        if (empty($actor_id)) {
            $actor_id = $USER->id;
        }

        $workspace_id = $this->get_workspace_id();
        $actor_workspace_interactor = interactor::from_workspace_id($workspace_id, $actor_id);

        if (!$actor_workspace_interactor->can_manage()) {
            throw new \coding_exception("No capability to promote a member");
        }

        $context = \context_course::instance($workspace_id);

        // Workspace learner role - we need to remove them first.
        $learner_roles = get_archetype_roles('student');
        if (empty($learner_roles)) {
            throw new \coding_exception("There are no learner roles found");
        }

        $current_roles = get_user_roles($context, $this->user_enrolment->userid);
        foreach ($current_roles as $current_role) {
            if (isset($learner_roles[$current_role->roleid])) {
                continue;
            }

            // Unassign the user's role for workspace member.
            role_unassign(
                $current_role->roleid,
                $this->user_enrolment->userid,
                $context->id,
                'container_workspace'
            );
        }

        $owner_roles = get_archetype_roles('workspaceowner');
        if (empty($owner_roles)) {
            throw new \coding_exception("There are no workspace's owner roles");
        }

        $owner_role = reset($owner_roles);
        // Check if the user already had this role.
        $has_role = user_has_role_assignment(
            $this->user_enrolment->userid,
            $owner_role->id,
            $context->id
        );

        if (!$has_role) {
            // Only assign owner role if user does not have it yet.
            role_assign(
                $owner_role->id,
                $this->user_enrolment->userid,
                $context->id,
                'container_workspace'
            );
        }
    }

    /**
     * @return int
     */
    public function get_time_modified(): int {
        return $this->user_enrolment->timemodified;
    }

    /**
     * @return int
     */
    public function get_time_created(): int {
        return $this->user_enrolment->timecreated;
    }

    /**
     * @return stdClass
     */
    public function get_user_record(): stdClass {
        if (null === $this->user_record) {
            $this->user_record = core_user::get_user($this->user_enrolment->userid, '*', MUST_EXIST);
        }

        return $this->user_record;
    }

    public function set_user_record(stdClass $user_record): void {
        if (!property_exists($user_record, 'id')) {
            throw new coding_exception("The user's record does not have 'id' property");
        }

        if ($user_record->id != $this->user_enrolment->userid) {
            throw new coding_exception("User record is different from the user's enrolment");
        }

        $this->user_record = $user_record;
    }

    /**
     * @return int
     */
    public function get_user_id(): int {
        return $this->user_enrolment->userid;
    }

    /**
     * @param int|null $actor_id
     * @return void
     */
    public function delete(?int $actor_id = null): void {
        global $USER;

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $workspace_id = $this->get_workspace_id();


        if ($actor_id != $this->user_enrolment->userid) {
            // Not the same user with the enrolment. Time to check whether it is an owner or not

            /** @var workspace $workspace */
            $workspace = factory::from_id($workspace_id);
            $owner_id = $workspace->get_user_id();

            if ($owner_id != $actor_id && !is_siteadmin($actor_id)) {
                throw new \coding_exception("User cannot delete the user enrolment of someone else");
            }
        }

        $this->user_enrolment->delete();
    }

    /**
     * @return int
     */
    public function get_status(): int {
        return $this->user_enrolment->status;
    }

    /**
     * @return int
     * @deprecated Since Totara 13.2
     */
    public function get_member_id(): int {
        debugging(
            "Function 'get_member_id' had been deprecated, please use 'get_id' instead",
            DEBUG_DEVELOPER
        );

        return $this->get_id();
    }

    /**
     * Returning the member id
     * @return int
     */
    public function get_id(): int {
        return $this->user_enrolment->id;
    }

    /**
     * Returning the member user id.
     * @return int
     */
    public function get_member_user_id(): int {
        return $this->user_enrolment->userid;
    }

    /**
     * @return void
     */
    public function reload(): void {
        $this->user_enrolment->refresh();
    }
}