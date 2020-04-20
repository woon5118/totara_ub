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
}