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

use core\event\base;
use tassign_competency\assignment_create_exception;
use tassign_competency\entities;
use tassign_competency\entities\assignment;
use tassign_competency\entities\competency;
use tassign_competency\entities\competency_assignment_user;
use tassign_competency\event\assignment_activated;
use tassign_competency\event\assignment_archived;
use tassign_competency\event\assignment_created;
use tassign_competency\event\assignment_deleted;
use tassign_competency\event\assignment_user_archived;
use tassign_competency\settings;
use totara_assignment\entities\hierarchy_item;
use totara_assignment\entities\user;
use totara_assignment\filter\hierarchy_item_visible;
use totara_assignment\user_groups;
use core\orm\query\builder;
use core\orm\collection;
use core\orm\entity\entity;

class assignment_actions {

    /**
     * Archive one or multiple assignments
     *
     * @param array|int $ids either one id or an array
     * @param bool $continue_tracking
     * @return array affected ids
     */
    public function archive($ids, bool $continue_tracking = false): array {
        $ids = self::sanitise_ids($ids);
        if (empty($ids)) {
            return [];
        }

        $affected_assignment_ids = [];

        // only active assignments can be archived
        $assignments = entities\assignment::repository()
            ->where('status', entities\assignment::STATUS_ACTIVE)
            ->where('id', $ids)
            ->get_lazy();

        /** @var base[] $events */

        // Doing this in a transaction to ensure that the whole bulk update is
        // either going through completely or throws an error
        $events = builder::get_db()->transaction(function () use ($assignments, $continue_tracking, & $affected_assignment_ids) {
            $system_assignments = [];
            $events = [];

            /** @var assignment $assignment */
            foreach ($assignments as $assignment) {
                $affected_assignment_ids[] = $assignment->id;

                // Get all user records for this assignment
                $assignment_users = competency_assignment_user::repository()
                    ->where('assignment_id', $assignment->id)
                    ->get_lazy();

                /** @var competency_assignment_user $assignment_user */
                foreach ($assignment_users as $assignment_user) {
                    $events[] = assignment_user_archived::create_from_assignment_user($assignment_user);

                    // if tracking should be continued create new system
                    // assignments for each user before archiving them
                    // We only ever create new tracking assignments for users in group assignments
                    if ($continue_tracking && $assignment->user_group_type !== user_groups::USER) {
                        $system_assignments[] = $assignment_user;
                    }
                }

                $events[] = assignment_archived::create_from_assignment($assignment);
            }

            // Update all assignments set status to archived
            $timestamp = time();
            assignment::repository()
                ->where('id', $affected_assignment_ids)
                ->update([
                    'status' => assignment::STATUS_ARCHIVED,
                    'updated_at' => $timestamp,
                    'archived_at' => $timestamp
                ]);

            // Delete all user records for those assignments
            competency_assignment_user::repository()
                ->where('assignment_id', $affected_assignment_ids)
                ->delete();

            // Create system assignments for continuous tracking
            foreach ($system_assignments as $assignment_user) {
                (new assignment_user($assignment_user->user_id))
                    ->create_system_assignment($assignment_user->competency_id);
            }

            return $events;
        });

        // Trigger all events after all the queries
        foreach ($events as $event) {
            $event->trigger();
        }

        return $affected_assignment_ids;
    }

    /**
     * Activate one or multiple assignments
     *
     * @param array|int $ids either one id or an array
     *
     * @return array
     */
    public function activate($ids): array {
        $ids = self::sanitise_ids($ids);
        if (empty($ids)) {
            return [];
        }

        $affected_assignment_ids = [];

        // Only active assignments can be archived
        $assignments = entities\assignment::repository()
            ->where('status', entities\assignment::STATUS_DRAFT)
            ->where('id', $ids)
            ->get_lazy();

        $events = [];
        /** @var assignment $assignment */
        foreach ($assignments as $assignment) {
            $affected_assignment_ids[] = $assignment->id;

            $events[] = assignment_activated::create_from_assignment($assignment);
        }

        // Update with one query and then trigger all events
        assignment::repository()
            ->where('id', $affected_assignment_ids)
            ->update([
                'status' => entities\assignment::STATUS_ACTIVE,
                'updated_at' => time()
            ]);

        foreach ($events as $event) {
            $event->trigger();
        }

        return $affected_assignment_ids;
    }

    /**
     * @param int|array $ids either one or multiple competency ids
     * @param bool $force Ignore assignment status when executing delete statement
     * @return array
     */
    public function delete_for_competency($ids, $force = false) {
        $ids = self::sanitise_ids($ids);
        if (empty($ids)) {
            return [];
        }

        $assignments = assignment::repository()
            ->select('id')
            ->where('competency_id', $ids)
            ->get()
            ->pluck('id');

        // No assignments found - nothing to delete.
        if (empty($assignments)) {
            return [];
        }

        return $this->delete($assignments, $force);
    }

    /**
     * Activate one or multiple assignments
     *
     * @param array|int $ids either one id or an array
     * @param bool $force Ignore assignment status when executing delete statement
     * @return array
     */
    public function delete($ids, $force = false): array {
        $ids = self::sanitise_ids($ids);
        if (empty($ids)) {
            return [];
        }

        $affected_assignment_ids = [];

        // only active assignments can be archived
        $assignments = entities\assignment::repository()
            ->where(function (builder $builder) use ($force) {
                if (!$force) {
                    $builder->where('status', [
                        entities\assignment::STATUS_DRAFT,
                        entities\assignment::STATUS_ARCHIVED
                    ]);
                }
            })
            ->where('id', $ids)
            ->get_lazy();

        /** @var assignment $assignment */
        foreach ($assignments as $assignment) {
            $affected_assignment_ids[] = $assignment->id;

            // Create the event as long as we still have an id in the instance
            $event = assignment_deleted::create_from_assignment($assignment);

            builder::get_db()->transaction(function () use ($assignment) {
                // Delete all related records for this assignment
                competency_assignment_user::repository()
                    ->where('assignment_id', $assignment->id)
                    ->delete();

                builder::table('totara_assignment_competencies_users_log')
                    ->where('assignment_id', $assignment->id)
                    ->delete();

                $assignment->delete();
            });

            $event->trigger();
        }

        return $affected_assignment_ids;
    }

    /**
     * 1st filtering the IDS as usual: Making sure these are numbers, stripping zeros and making it unique
     *
     * @param array|int $ids
     * @return array
     */
    private static function sanitise_ids($ids): array {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $ids = array_filter(
            array_map('intval', $ids),
            function ($id) {
                return $id > 0;
            }
        );
        return array_unique($ids);
    }

    /**
     * Create assignment(s) based on competency IDS and user groups
     *
     * @param int[] $competency_ids Competency IDS
     * @param array $user_groups ['ug type' => [ids]]
     * @param string $type type of assignment, admin, system, etc.
     * @param int $status Assignment activation status 0 - draft, 1 - active
     * @return collection
     * @throws \coding_exception
     */
    public function create_from_competencies(array $competency_ids, array $user_groups, string $type, int $status = assignment::STATUS_DRAFT) {
        // Validate assignment status
        if (!in_array($status, [assignment::STATUS_DRAFT, assignment::STATUS_ACTIVE], true)) {
            throw new \coding_exception('Invalid assignment status supplied');
        }

        // Validate assignment type
        if (!in_array($type, assignment::get_available_types(), true)) {
            throw new \coding_exception('Invalid assignment type supplied');
        }

        $competencies = competency::repository()
            ->set_filter((new hierarchy_item_visible())->set_value(true))
            ->where('id', $competency_ids)
            ->get();

        // Validating competencies
        $loaded_competency_ids = $competencies->pluck('id');

        if ((count($loaded_competency_ids) != count($competency_ids))
            || !empty(array_diff($loaded_competency_ids, $competency_ids))
        ) {
            throw new assignment_create_exception('Incorrect competency ids have been supplied');
        }

        $assignments = builder::get_db()->transaction(
            function () use ($competencies, $user_groups, $status, $type) {
                $assignments = new collection();
                /*** @var competency $competency */
                foreach ($competencies as $competency) {
                    foreach ($user_groups as $user_group_type => $ug_ids) {
                        if (!in_array($user_group_type, user_groups::get_available_types(), true)) {
                            throw new \coding_exception('Invalid user group has been passed');
                        }

                        $class = "\\totara_assignment\\entities\\{$user_group_type}";
                        if (!class_exists($class)) {
                            throw new \coding_exception('Invalid user group has been passed');
                        }

                        // Filter down the list of ids: Convert to int, unique and strip zeros
                        $ids = self::sanitise_ids($ug_ids);

                        /** @var entity $class */
                        $repo = $class::repository()
                            ->where('id', $ids);

                        if (is_a($class, user::class)) {
                            $repo->filter_by_not_deleted();
                        } else if (is_a($class, hierarchy_item::class)) {
                            $repo->set_filter((new hierarchy_item_visible())->set_value(true));
                        }

                        $loaded_user_groups = $repo->get();

                        // Validating user groups
                        $loaded_ug_ids = $loaded_user_groups->pluck('id');
                        if ((count($loaded_ug_ids) != count($ug_ids)) || !empty(array_diff($loaded_ug_ids, $ug_ids))) {
                            throw new assignment_create_exception('Incorrect user group ids have been supplied');
                        }

                        foreach ($loaded_user_groups as $group) {
                            $user = user::logged_in();

                            if ($this->has_existing_assignment(
                                $type,
                                $competency->id,
                                $user_group_type,
                                $group->id,
                                $user->id
                            )) {
                                // do not create duplicates
                                continue;
                            }

                            /** @var assignment $assignment */
                            $assignment = new assignment();
                            $assignment->type = $type;
                            $assignment->competency_id = $competency->id;
                            $assignment->user_group_type = $user_group_type;
                            $assignment->user_group_id = $group->id;
                            $assignment->optional = 0;
                            $assignment->status = $status;
                            $assignment->created_by = $user->id;
                            $assignment->created_at = time();
                            $assignment->updated_at = time();
                            $assignment->archived_at = null;
                            $assignment->save();

                            $assignments->append($assignment);
                        }
                    }
                }
                return $assignments;
            }
        );

        foreach ($assignments as $assignment) {
            assignment_created::create_from_assignment($assignment)->trigger();
        }

        return $assignments;
    }

    /**
     * @param string $type
     * @param int $competency_id
     * @param string $user_group_type
     * @param int $user_group_id
     * @param int $user_id
     * @return bool
     */
    private function has_existing_assignment(string $type, int $competency_id, string $user_group_type,
                                                int $user_group_id, int $user_id): bool {
        // Check for duplicate
        $assignment = assignment::repository()
            ->where('type', $type)
            ->where('competency_id', $competency_id)
            ->where('user_group_type', $user_group_type)
            ->where('user_group_id', $user_group_id);
        // There can be multiple other assignments from different creators
        if ($type === assignment::TYPE_OTHER) {
            $assignment->where('created_by', $user_id);
        }
        return $assignment->count() > 0;
    }


    /**
     * Archive assignments for the given user group
     *
     * @param string $user_group_type User group type to unassign
     * @param int|array $user_group_id One or more user group ids to unassign users from
     * @return array Affected assignment ids
     */
    public function archive_for_user_group(string $user_group_type, $user_group_id) {
        $ids = self::sanitise_ids($user_group_id);

        $assignments = assignment::repository()
            ->filter_by_user_group_type($user_group_type)
            ->filter_by_user_group_ids($ids)
            ->select('id')
            ->get()
            ->pluck('id');

        return $this->archive($assignments, settings::is_continuous_tracking_enabled());
    }

    /**
     * Delete assignments for the given user group
     *
     * @param string $user_group_type User group type to delete
     * @param int|array $user_group_id One or more user group ids to delete assignments for
     * @param bool $force Force delete assignments, e.g. regardless of type
     * @return array Affected assignment ids
     */
    public function delete_for_user_groups(string $user_group_type, $user_group_id, $force = false) {
        $ids = self::sanitise_ids($user_group_id);

        $assignments = assignment::repository()
            ->filter_by_user_group_type($user_group_type)
            ->filter_by_user_group_ids($ids)
            ->select('id')
            ->get()
            ->pluck('id');

        return $this->delete($assignments, $force);
    }

    /**
     * An inline constructor.
     *
     * @return assignment_actions
     */
    public static function create(): assignment_actions {
        return new static();
    }

}