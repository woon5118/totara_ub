<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Jonathan Newman <jonathan.newman@catalyst.net.nz>
 * @author Ciaran Irvine <ciaran.irvine@totaralms.com>
 * @package totara
 * @subpackage totara_core
 */

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_totara_core_upgrade($oldversion) {
    global $CFG, $DB;
    require(__DIR__ . '/upgradelib.php');

    $dbman = $DB->get_manager();

    // Totara 10 branching line.
    if ($oldversion < 2016111100) {
        // Delete all removed update and install settings.
        unset_config('disableupdatenotifications');
        unset_config('disableupdateautodeploy');
        unset_config('updateautodeploy');
        unset_config('updateautocheck');
        unset_config('updatenotifybuilds');
        unset_config('updateminmaturity');
        unset_config('updatenotifybuilds');

        // Uninstall deleted plugin.
        uninstall_plugin('tool', 'installaddon');

        upgrade_plugin_savepoint(true, 2016111100, 'totara', 'core');
    }

    if ($oldversion < 2016112201) {
        // Kiwifruit responsive was removed in Totara 10.
        // Clean up configuration if it has not been re-introduced.
        if (!file_exists("{$CFG->dirroot}/theme/kiwifruitresponsive/config.php")) {
            unset_all_config_for_plugin('theme_kiwifruitresponsive');
        }
        totara_upgrade_mod_savepoint(true, 2016112201, 'totara_core');
    }

    return true;
}
