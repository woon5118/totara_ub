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
namespace container_workspace\watcher;

use container_workspace\loader\member\loader;
use core_user\hook\allow_view_profile_field;
use core_container\factory;
use container_workspace\workspace;

/**
 * This is for resolving all the profile field view request.
 */
final class profile_watcher {
    /**
     * Constant for only valid user fields that this workspace allow other user to see.
     *
     * @var array
     */
    private const VALID_FIELDS = [
        'fullname',
        'email',
        'profileimageurl',
        'profileimagealt',
        'imagealt'
    ];

    /**
     * @param allow_view_profile_field $hook
     * @return void
     */
    public static function watch_allow_profile_field(allow_view_profile_field $hook): void {
        global $DB;

        $course = $hook->get_course();
        if (null === $course || $hook->has_permission()) {
            // Context is not appearing.
            return;
        }

        // We are only allowing several fields within workspace, but not all.
        $field = $hook->field;
        if (!in_array($field, static::VALID_FIELDS)) {
            return;
        }

        $workspace = factory::from_record($course);
        if (!$workspace->is_typeof(workspace::get_type())) {
            return;
        }

        // Note: this just a temporary solution to help by-pass the field fullname. So that
        // i can be unblocked from this access controller. The right way to fix it is to fix
        // it within access_controller class itself.
        if (is_siteadmin($hook->viewing_user_id)) {
            $hook->give_permission();
        }

        // So this course is a workspace and the actor is viewing the target user within the workspace.
        // We will have to resolve whether the current actor if the actor is still a member of the workspace or not.
        $actor_member = loader::get_for_user($hook->viewing_user_id, $workspace->get_id());
        if (null !== $actor_member && !$actor_member->is_suspended()) {
            // This user is still active within the workspace.
            if ('email' !== $field) {
                // We handle email differently.
                $hook->give_permission();
                return;
            }

            // We have to respect the mail display settings from user record.
            $mail_display = $DB->get_field('user', 'maildisplay', ['id' => $hook->target_user_id]);
            $valid_settings = [
                \core_user::MAILDISPLAY_EVERYONE,
                \core_user::MAILDISPLAY_COURSE_MEMBERS_ONLY
            ];

            if (in_array($mail_display, $valid_settings)) {
                $hook->give_permission();
            }
        }
    }
}