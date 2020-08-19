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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package container_workspace
 */

namespace container_workspace;

use totara_core\advanced_feature;

/**
 * Class workspace_helper
 *
 * @package container_workspace\local
 */
final class util {
    /**
     * Preventing this class from being constructed.
     */
    private function __construct() {
    }

    /**
     * Find our default workspace roles and add any that are missing
     *
     * Expect to see/create the following roles:
     * - workspacecreator - intended to control the ability to create new workspaces
     * - workspaceowner - intended to maintain/manage specific workspaces
     */
    public static function add_missing_workspace_roles(): void {
        global $DB;

        $systemcontext = \context_system::instance();
        $shortnames = [
            'workspacecreator',
            'workspaceowner',
        ];

        // This is taken from tenant/classes/local/util install script
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
                    $function = 'allow_' . $type;
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
}