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
defined('MOODLE_INTERNAL') || die();

/**
 * @param int $old_version
 * @return bool
 */
function xmldb_container_workspace_upgrade($old_version) {
    global $DB, $CFG;
    require_once("{$CFG->dirroot}/container/type/workspace/db/upgradelib.php");

    $db_manager = $DB->get_manager();

    if ($old_version < 2020100101) {
        // Define field to_be_deleted to be added to workspace.
        $table = new xmldb_table('workspace');
        $field = new xmldb_field('to_be_deleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timestamp');

        // Conditionally launch add field to_be_deleted.
        if (!$db_manager->field_exists($table, $field)) {
            $db_manager->add_field($table, $field);
        }

        // Workspace savepoint reached.
        upgrade_plugin_savepoint(true, 2020100101, 'container', 'workspace');
    }

    if ($old_version < 2020100105) {
        // Queue the creation of missing container records for the workspace container.
        \core\task\manager::queue_adhoc_task(new \container_workspace\task\create_missing_categories());

        upgrade_plugin_savepoint(true, 2020100105, 'container', 'workspace');
    }

    if ($old_version < 2020100110) {
        container_workspace_update_hidden_workspace_with_audience_visibility();
        upgrade_plugin_savepoint(true, 2020100110, 'container', 'workspace');
    }

    return true;
}