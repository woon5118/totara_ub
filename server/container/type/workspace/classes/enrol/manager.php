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
namespace container_workspace\enrol;

use container_workspace\exception\enrol_exception;
use container_workspace\workspace;
use core\entity\enrol;
use core\entity\user_enrolment;

/**
 * Enrolment manager.
 */
final class manager {
    /**
     * @var workspace
     */
    private $workspace;

    /**
     * enrollment_manager constructor.
     * @param workspace $workspace
     */
    private function __construct(workspace $workspace) {
        $this->workspace = $workspace;
    }

    /**
     * @param workspace $workspace
     * @return manager
     */
    public static function from_workspace(workspace $workspace): manager {
        $id = $workspace->id;
        if (null === $id || 0 === (int) $id) {
            throw new \coding_exception("Cannot instantiate an instance when workspace is invalid");
        }

        return new static($workspace);
    }

    /**
     *
     * @param string $enrol_type
     * @return enrol
     */
    private function enable_enrolment(string $enrol_type): enrol {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/enrollib.php");

        if (!enrol_is_enabled($enrol_type)) {
            throw new \coding_exception("{$enrol_type} enrollment is not available");
        }

        $plugin = enrol_get_plugin($enrol_type);
        $record = $this->workspace->to_record();

        $instance_id = $plugin->add_instance($record, ['status' => ENROL_INSTANCE_ENABLED]);
        return new enrol($instance_id);
    }

    /**
     * @return enrol
     */
    public function enable_self_enrolment(): enrol {
        return $this->enable_enrolment('self');
    }

    /**
     * @return enrol
     */
    public function enable_manual_enrolment(): enrol {
        return $this->enable_enrolment('manual');
    }

    /**
     * First we need to check if this very user does have a record in enrolment or not. If there isn't
     * then we will create a new record and stop it from here. Otherwise, checking if it is the same
     * enrolment method or not.
     *
     * If it is the same enrolment method. Update the status, if it is not delete the record and create a new one.
     *
     * @param int $user_id
     * @param int $role_id
     * @param string $enrol
     *
     * @return void
     */
    private function enrol_user(int $user_id, int $role_id, string $enrol): void {
        global $CFG, $DB;
        require_once("{$CFG->dirroot}/lib/enrollib.php");

        $plugin = enrol_get_plugin($enrol);
        $workspace_id = $this->workspace->get_id();

        $record = $DB->get_record(
            'enrol',
            [
                'enrol' => $plugin->get_name(),
                'courseid' => $workspace_id,
                'status' => ENROL_INSTANCE_ENABLED
            ],
            '*',
            IGNORE_MISSING
        );

        if (null === $record || false === $record) {
            throw enrol_exception::on_self_enrol($this->workspace->fullname);
        }

        $context = $this->workspace->get_context();

        // Need to find out if the user is already enrolled.
        $sql = '
            SELECT ue.*, e.enrol 
            FROM "ttr_user_enrolments" ue
            INNER JOIN "ttr_enrol" e ON e.id = ue.enrolid
            WHERE ue.userid = :user_id
            AND e.courseid = :workspace_id
        ';

        // There MUST only one user enrolment record at a time despite of there are two many enrol instances
        // enabled for the workspace.
        $user_enrolment = $DB->get_record_sql(
            $sql,
            [
                'user_id' => $user_id,
                'workspace_id' => $workspace_id,
            ],
            IGNORE_MISSING
        );

        if (null === $user_enrolment || false === $user_enrolment) {
            // User does not have a record yet. Time to create a new record.
            $plugin->enrol_user($record, $user_id);
            role_assign($role_id, $user_id, $context->id, 'container_workspace');
            return;
        }

        // User does have the record, we need to check if it is the same enrol method. If it is
        // then we need to update the status. Otherwise, delete the user_enrolment record and create a new one.
        if ($plugin->get_name() === $user_enrolment->enrol) {
            if (ENROL_USER_ACTIVE == $user_enrolment->status) {
                // It is already active, leave it.
                return;
            }

            $plugin->update_user_enrol($record, $user_id, ENROL_USER_ACTIVE);

            // After updating the status, we might as well need to update the role. If it is already assigned,
            // then the function can just skip it.
            role_assign($role_id, $user_id, $context->id, 'container_workspace');
            return;
        }

        // Right, not the same enrolment method. Delete this record and create a new one.
        $DB->delete_records('user_enrolments', ['id' => $user_enrolment->id]);
        $plugin->enrol_user($record, $user_id);

        role_assign($role_id, $user_id, $context->id, 'container_workspace');
    }

    /**
     * @param int $user_id
     * @param int $role_id
     *
     * @return void
     */
    public function self_enrol_user(int $user_id, int $role_id): void {
        $this->enrol_user($user_id, $role_id, 'self');
    }

    /**
     * @param int $user_id
     * @param int $role_id
     * @param int|null $actor_id
     * @return void
     *
     * @throws enrol_exception
     */
    public function manual_enrol_user(int $user_id, int $role_id, ?int $actor_id = null): void {
        global $USER;

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $owner_id = $this->workspace->get_user_id();
        if ($actor_id != $owner_id) {
            // Not an actor, time to check for capability.
            $context = $this->workspace->get_context();

            if (!has_capability('container/workspace:invite', $context, $actor_id)) {
                throw enrol_exception::on_manual_enrol($this->workspace->fullname);
            }
        }

        $this->enrol_user($user_id, $role_id, 'manual');
    }

    /**
     * Note: this function  is not checking any capability in here, as it is being reused by self enrolment as well
     *
     * @param user_enrolment $user_enrolment
     * @return void
     */
    public function suspend_enrol(user_enrolment $user_enrolment): void {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/enrollib.php");

        $enrol = new enrol($user_enrolment->enrolid);
        $plugin = enrol_get_plugin($enrol->enrol);

        $instance = (object) $enrol->to_array();
        $user_id = $user_enrolment->userid;

        $plugin->update_user_enrol($instance, $user_id, ENROL_USER_SUSPENDED);
    }

    /**
     * @param int|null $actor_id
     * @return  void
     */
    public function delete_enrol_instances(?int $actor_id = null): void {
        global $CFG, $USER, $DB;
        require_once("{$CFG->dirroot}/lib/enrollib.php");

        if (null === $actor_id || 0 === $actor_id) {
            $actor_id = $USER->id;
        }

        $owner_id = $this->workspace->get_user_id();
        if ($actor_id != $owner_id && !is_siteadmin($actor_id)) {
            throw new \coding_exception("Cannot delete enrol instances");
        }

        $workspace_id = $this->workspace->get_id();
        $instances = $DB->get_records('enrol', ['courseid' => $workspace_id]);

        foreach ($instances as $instance) {
            $plugin = enrol_get_plugin($instance->enrol);
            $plugin->delete_instance($instance);
        }
    }
}