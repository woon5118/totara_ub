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
use core\orm\collection;
use core\orm\query\builder;
use tassign_competency\assignment_create_exception;
use tassign_competency\entities;
use tassign_competency\entities\assignment;
use tassign_competency\entities\competency;
use tassign_competency\models\assignment as assignment_model;
use tassign_competency\settings;
use totara_assignment\filter\hierarchy_item_visible;

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

        /** @var assignment $assignment */
        foreach ($assignments as $assignment) {
            $affected_assignment_ids[] = $assignment->id;

            $model = assignment_model::load_by_entity($assignment);
            $model->archive($continue_tracking);
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

        /** @var assignment $assignment */
        foreach ($assignments as $assignment) {
            $affected_assignment_ids[] = $assignment->id;

            $model = assignment_model::load_by_entity($assignment);
            $model->activate();
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

            $model = assignment_model::load_by_entity($assignment);
            if ($force) {
                $model->force_delete();
            } else {
                $model->delete();
            }
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
     * @return collection|assignment[]
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

        $assignments = new collection();

        /*** @var competency $competency */
        foreach ($competencies as $competency) {
            foreach ($user_groups as $user_group_type => $ug_ids) {
                foreach ($ug_ids as $user_group_id) {
                    $assignment = assignment_model::create($competency->id, $type, $user_group_type, $user_group_id, $status);
                    if ($assignment) {
                        $assignments->append($assignment);
                    }
                }
            }
        }

        return $assignments;
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