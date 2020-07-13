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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform;

use container_perform\perform as perform_container;
use mod_perform\entities\activity\activity_type;

use context;
use context_coursecat;
use core_text;

class util {

    /**
     * TODO this was largely copy/pasted from totara_tenant\local\util::check_roles_exist(). Abstract to single location?
     * TODO if so, need to pull out enabled check and pass in specific roles to create.
     *
     * Ensure the required performance activity roles exist in the system:
     * - performanceactivitycreator - intended for users that can create performance activities, assigned to category context.
     * - performanceactivitymanager - intended for users to manage a specific performance activity, assigned to course context typically.
     */
    public static function create_performance_roles(): void {
        global $DB;

        // Ensure mod_perform enabled.
        if (!$DB->record_exists('modules', ['name' => 'perform', 'visible' => 1])) {
            return;
        }

        $systemcontext = \context_system::instance();

        $shortnames = ['performanceactivitycreator', 'performanceactivitymanager'];
        foreach ($shortnames as $shortname) {
            if ($DB->record_exists('role', ['shortname' => $shortname])) {
                continue;
            }
            $newroleid = create_role('', $shortname, '', $shortname);

            $role = $DB->get_record('role', ['id' => $newroleid], '*', MUST_EXIST);
            foreach (array('assign', 'override', 'switch') as $type) {
                $function = 'allow_' . $type;
                $allows = get_default_role_archetype_allows($type, $role->archetype);
                foreach ($allows as $allowid) {
                    $function($role->id, $allowid);
                }
                set_role_contextlevels($role->id, get_default_contextlevels($role->archetype));
            }
            $defaultcaps = get_default_capabilities($role->archetype);
            foreach ($defaultcaps as $cap => $permission) {
                assign_capability($cap, $permission, $role->id, $systemcontext->id);
            }

            // Add allow_* defaults related to the new role.
            foreach ($DB->get_records('role') as $role) {
                if ($role->id == $newroleid) {
                    continue;
                }
                foreach (array('assign', 'override', 'switch') as $type) {
                    $function = 'allow_'.$type;
                    $allows = get_default_role_archetype_allows($type, $role->archetype);
                    foreach ($allows as $allowid) {
                        if ($allowid == $newroleid) {
                            $function($role->id, $allowid);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the default category for performance activities.
     * If multi tenancy is turned on and the current user is part of a tenant
     * it will get the category of the tenant.
     *
     * If the category does not exist yet it will automatically create it.
     *
     * @return int
     */
    public static function get_default_category_id(): int {
        return perform_container::get_default_category_id();
    }

    /**
     * Creates a set of activity types.
     */
    public static function create_activity_types(): void {
        $predefined_types = [
            'appraisal',
            'check-in',
            'feedback'
        ];

        foreach ($predefined_types as $type) {
            $entity = new activity_type();
            $entity->name = $type;
            $entity->is_system = true;

            $entity->save();
        }
    }

    /**
     * @return context
     */
    public static function get_default_context(): context {
        $category_id = self::get_default_category_id();
        return context_coursecat::instance($category_id);
    }

    /**
     * Convenience function for adding a prefix/suffix to string. If necessary,
     * the original string is truncated to ensure the new string fits within a
     * length limit.
     *
     * @param string $text text to which to add a prefix and suffix.
     * @param int $max_chars maximum no of _characters_ (not bytes) allowed for
     *        the resultant string.
     * @param string $prefix text that leads the resultant string.
     * @param string $suffix text at the end of the resultant string.
     *
     * @return string the new string.
     */
    public static function augment_text(
        string $text,
        int $max_chars,
        string $prefix='',
        string $suffix=''
    ): string {
        // Note the use of _character_ (instead of byte) aware methods to get a
        // resultant string.
        $prefix_size = core_text::strlen($prefix);
        $suffix_size = core_text::strlen($suffix);
        $new_text_size = $prefix_size + core_text::strlen($text) + $suffix_size;

        if ($new_text_size <= $max_chars) {
            return "$prefix$text$suffix";
        }

        $ellipsis = "...";
        $ellipsis_size = core_text::strlen($ellipsis);
        $truncated_size = $max_chars - $prefix_size - $ellipsis_size - $suffix_size;

        $truncated = core_text::substr($text, 0, $truncated_size);
        return "$prefix$truncated$ellipsis$suffix";
    }


    /**
     * Returns an array of up to 1000 userids of users who the $for_user id holds
     * the $capability in the user's context. Useful for checking which users a
     * user is permitted to do some action on.
     *
     * TODO I've just chucked this in here for now, not sure the best place for it.
     * TODO this is prototype
     *
     *
     * @param int $for_user ID of user to check for.
     * @param string $capability Capability string to test.
     * @param int $offset Offset to apply before returning records, null for no offset.
     * @param int $limit Maximum number of userids to return, null for no limit.
     * @return array Array of userids
     * @throws \dml_exception
     */
    public static function get_permitted_users(int $for_user, string $capability, int $offset = 0, int $limit = 1000): array {
        global $DB;
        list($has_cap_sql, $has_cap_params) = access::get_has_capability_sql($capability, 'c.id', $for_user);
        $sql = "SELECT u.id AS key, u.id FROM {user} u
            JOIN {context} c ON c.contextlevel = " . CONTEXT_USER . " AND c.instanceid = u.id
            WHERE u.deleted = 0 AND ({$has_cap_sql})
            ORDER BY u.id
        ";
        return $DB->get_records_sql_menu($sql, $has_cap_params, $offset, $limit);
    }

    /**
     * Return SQL and params to apply to an SQL query in order to filter to only users where the viewing
     * user can manage those user's participation.
     *
     * TODO this is prototype
     *
     * @param int $report_for User ID of user who is viewing
     * @param string $user_id_field String referencing database column containing user ids to filter.
     * @return array Array containing SQL string and array of params
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_manage_participation_sql(int $report_for, string $user_id_field) {
        global $DB;

        // If user can manage participation across all users don't do the per-row restriction at all.
        $user_context = \context_user::instance($report_for);
        if (has_capability('mod/perform:manage_all_participation', $user_context, $report_for)) {
            return ['1=1', []];
        }

        $capability = 'mod/perform:manage_subject_user_participation';
        $permitted_users = \mod_perform\util::get_permitted_users($report_for, $capability);

        if (empty($permitted_users)) {
            // No access at all if not permitted to see any users.
            return ['1=0', []];
        }

        // Restrict to specific subject users.
        list($sourcesql, $sourceparams) = $DB->get_in_or_equal($permitted_users, SQL_PARAMS_NAMED);
        $wheresql = "$user_id_field {$sourcesql}";
        $whereparams = $sourceparams;
        return [$wheresql, $whereparams];
    }

}