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

use container_workspace\interactor\workspace\interactor;
use container_workspace\tracker\tracker;
use container_workspace\workspace;
use core\entity\user_enrolment;
use core_container\factory;

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
     * member constructor.
     * @param user_enrolment $user_enrolment
     */
    private function __construct(user_enrolment $user_enrolment) {
        if (!$user_enrolment->exists()) {
            throw new \coding_exception("Cannot construct a class of member that is not already exist");
        }

        $this->user_enrolment = $user_enrolment;
        $this->workspace_id = null;
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
     * If the actor is match with the owner of the workspace, then the actor will be enrolled as editingteacher
     * role. Otherwise student role will be used for any other user.
     *
     * @param workspace $workspace
     * @param int|null $actor_id    If this is null, then $user in session will be used.
     *
     * @return member
     */
    public static function join_workspace(workspace $workspace, ?int $actor_id = null): member {
        global $USER, $CFG;

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $owner_id = $workspace->get_user_id();

        if (!$workspace->is_public() && ($owner_id != $actor_id && !is_siteadmin($actor_id))) {
            throw new \coding_exception("Cannot join the non-public workspace");
        }

        if ($CFG->tenantsenabled) {
            $interactor = new interactor($workspace, $actor_id);
            if (!$interactor->can_view_workspace_with_tenant_check()) {
                throw new \coding_exception("Cannot join the workspace that is not in the same tenant");
            }
        }

        $is_owner = ($actor_id == $owner_id);
        $archetype = 'student';

        if ($is_owner) {
            $archetype = 'editingteacher';
        }

        $roles = get_archetype_roles($archetype);

        if (empty($roles)) {
            throw new \coding_exception("No role for archetype '{$archetype}'");
        }

        $role = reset($roles);

        $manager = $workspace->get_enrolment_manager();
        $manager->self_enrol_user($actor_id, $role->id);

        // Need to add capability viewhiddencourses to this workspace if it is a hidden one for the
        // user owner. This happened because authenticated user does not have any capabilities to see
        // any hidden workspaces/courses by default - therefore we have to add this cap for the specific context
        // of the course.
        if ($workspace->is_hidden() && $is_owner) {
            $context = $workspace->get_context();

            assign_capability(
                'moodle/course:viewhiddencourses',
                CAP_ALLOW,
                $role->id,
                $context->id
            );
        }

        $workspace_id = $workspace->get_id();
        return static::from_user($actor_id, $workspace_id);
    }

    /**
     * Target user is being added to the workspace by the actor.
     *
     * @param workspace $workspace
     * @param int $user_id
     * @param int|null $actor_id
     *
     * @return member
     */
    public static function added_to_workspace(workspace $workspace, int $user_id, ?int $actor_id = null): member {
        global $CFG;
        $owner_id = $workspace->get_user_id();
        if ($user_id == $owner_id) {
            throw new \coding_exception("Owner of a workspace should not be able to add self to a workspace");
        }

        $roles = get_archetype_roles('student');
        if (empty($roles)) {
            throw new \coding_exception("No roles for archetype 'student'");
        }

        $role = reset($roles);

        if ($CFG->tenantsenabled) {
            // Only checking this if multi-tenancy is enabled.
            $target_workspace_interactor = new interactor($workspace, $user_id);
            if (!$target_workspace_interactor->can_view_workspace_with_tenant_check()) {
                // Check if the newly going-to-be-added user is able to see the workspace or not.
                throw new \coding_exception("Target user is not able to see the workspace");
            }
        }

        $manager = $workspace->get_enrolment_manager();
        $manager->manual_enrol_user($user_id, $role->id, $actor_id);

        $workspace_id = $workspace->get_id();
        return static::from_user($user_id, $workspace_id);
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
        $owner_id = $workspace->get_user_id();

        if ($owner_id != $actor_id) {
            // Not the owner of the workspace. Time to check for capability.
            $context = $workspace->get_context();

            if (!has_capability('container/workspace:removemember', $context, $actor_id)) {
                throw new \coding_exception("No capability to remove the member");
            }
        }

        $manager = $workspace->get_enrolment_manager();
        $manager->suspend_enrol($this->user_enrolment);

        $this->user_enrolment->refresh();
        return $this->is_suspended();
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
     * @return \stdClass
     */
    public function get_user_record(): \stdClass {
        return \core_user::get_user($this->user_enrolment->userid);
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
     */
    public function get_member_id(): int {
        return $this->user_enrolment->id;
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
}