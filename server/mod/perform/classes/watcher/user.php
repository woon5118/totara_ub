<?php
/*
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\watcher;

use container_perform\perform;
use context_user;
use core\orm\entity\repository;
use core\orm\query\field;
use core\tenant_orm_helper;
use core_user\hook\allow_view_profile;
use core_user\hook\allow_view_profile_field;
use core_user\profile\display_setting;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\manual_relationship_selection;
use mod_perform\entities\activity\manual_relationship_selection_progress;
use mod_perform\entities\activity\manual_relationship_selector;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_static_instance;
use mod_perform\util;
use totara_core\hook\base;

class user {

    protected const ALLOWED_FIELDS = [
        'id',
        'fullname',
        'profileimageurl',
        'profileimageurlsmall',
        'profileimagealt'
    ];

    /**
     * User access hook to check if one user can view another users profile field in the context of mod perform.
     *
     * @param allow_view_profile_field $hook
     */
    public static function allow_view_profile_field(allow_view_profile_field $hook): void {
        if ($hook->has_permission()) {
            return;
        }

        // We allow a combination of fields we use for the participation
        // forms, activities list and user selectors
        if (!in_array($hook->field, self::ALLOWED_FIELDS, true)
            && !in_array($hook->field, display_setting::get_display_fields())
        ) {
            return;
        }

        self::allow_view($hook);
    }

    /**
     * User access hook to check if one user can view another users profile in the context of mod perform.
     *
     * @param allow_view_profile $hook
     */
    public static function allow_view_profile(allow_view_profile $hook): void {
        self::allow_view($hook);
    }

    /**
     * User access hook to check if one user can view another users profile field in the context of mod perform.
     *
     * @param base|allow_view_profile|allow_view_profile_field $hook
     */
    private static function allow_view(base $hook): void {
        if ($hook->has_permission()) {
            return;
        }

        $course = $hook->get_course();
        // Ignore anything other than perform containers
        if (!$course || $course->containertype !== perform::get_type()) {
            return;
        }

        // Handle site admins explicitly
        if (is_siteadmin()) {
            $hook->give_permission();
            return;
        }

        // If both users are both participants in an activity or the target
        // user is the subject and the viewing user a participant.
        if (participant_instance::repository()
            ::user_can_view_other_users_profile($hook->viewing_user_id, $hook->target_user_id)
        ) {
            $hook->give_permission();
            return;
        }

        if (self::is_involved_in_manual_instance_progress($hook)) {
            $hook->give_permission();
            return;
        }

        if (util::can_report_on_user($hook->target_user_id, $hook->viewing_user_id)) {
            $hook->give_permission();
            return;
        }

        // This is just a small safety check. The query should have taken care of the checks
        // whether the user can be shown.
        if (util::can_potentially_manage_participants($hook->viewing_user_id)) {
            $hook->give_permission();
            return;
        }

        // Is the user a manager or appraiser in one of the users subject static instance records.
        if (subject_static_instance::repository()
            ::user_can_view_other_users_profile($hook->viewing_user_id, $hook->target_user_id)) {
            $hook->give_permission();
            return;
        }
    }

    /**
     * Check if the viewing user is currently assigned as a selector for a subject instance
     * of the given activity. This means he would be allowed to see all users on the site
     * or in the same tenant (if multi tenancy is enabled).
     *
     * @param base|allow_view_profile|allow_view_profile_field $hook
     * @return bool
     */
    private static function is_involved_in_manual_instance_progress(base $hook): bool {
        return manual_relationship_selector::repository()
            ->join([manual_relationship_selection_progress::TABLE, 'mrsp'], 'manual_relation_select_progress_id', 'id')
            ->join([manual_relationship_selection::TABLE, 'mrs'], 'mrsp.manual_relation_selection_id', 'id')
            ->join([activity_entity::TABLE, 'a'], 'mrs.activity_id', 'id')
            ->where('a.course', $hook->get_course()->id)
            ->where('mrsp.status', manual_relationship_selection_progress::STATUS_PENDING)
            ->where('user_id', $hook->viewing_user_id)
            ->when(true, function (repository $repository) use ($hook) {
                // This make sure this query is multi tenancy compatible
                // and both users are in the same tenant
                tenant_orm_helper::restrict_users(
                    $repository,
                    new field('user_id', $repository->get_builder()),
                    context_user::instance($hook->target_user_id)
                );
            })
            ->exists();
    }

}