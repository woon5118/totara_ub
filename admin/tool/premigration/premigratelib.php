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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package tool_premigration
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/upgradelib.php');

/*
 * Collection of function used from db/premigrate.php scripts.
 */

/**
 * Core pre-migration savepoint.
 *
 * @param string|float|int $version main version
 * @return string new version in database
 */
function premigrate_main_savepoint($version) {
    global $CFG;

    // Main savepoint may be called from lib/db/upgrade.php and lib/upgradelib.php only.
    $debuginfo = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    $callee = str_replace(DIRECTORY_SEPARATOR, '/', $debuginfo[0]['file']);
    $dirroot = str_replace(DIRECTORY_SEPARATOR, '/', $CFG->dirroot);
    if ($callee !== $dirroot . '/lib/db/premigrate.php') {
        throw new coding_Exception('premigrate_main_savepoint() must be called from main premigration script only');
    }

    if ($CFG->version < $version) {
        // something really wrong is going on in main upgrade script
        throw new coding_Exception('Premigration cannot increase main version number');
    }

    set_config('version', $version);
    upgrade_log(UPGRADE_LOG_NORMAL, null, 'Pre-migration core savepoint reached');

    // reset upgrade timeout to default
    upgrade_set_timeout();

    return $CFG->version;
}

/**
 * Returns plugin version in database.
 *
 * @param string $type
 * @param string $plugin
 * @return string|null numeric plugin version, null of not installed
 */
function premigrate_get_plugin_version(string $type, string $plugin): ?string {
    $component = $type . '_' . $plugin;
    $version = get_config($component, 'version');
    if (!$version) {
        return null;
    }
    return $version;
}

/**
 * Plugins upgrade savepoint, marks end of blocks upgrade blocks
 * It stores plugin version, resets upgrade timeout
 * and abort upgrade if user cancels page loading.
 *
 * @category upgrade
 * @param string|float $version plugin version
 * @param string $type type of plugin
 * @param string $plugin name of plugin
 * @return string numeric plugin version, null of not installed
 */
function premigrate_plugin_savepoint($version, $type, $plugin): string {
    global $DB;

    upgrade_log(UPGRADE_LOG_NORMAL, '', 'Pre-migration plugin savepoint reached');

    // Reset upgrade timeout to default
    upgrade_set_timeout();

    if (!\core_component::is_valid_plugin_name($type, $plugin)) {
        throw new coding_exception('Invalid plugin name format');
    }
    $plugins =\core_component::get_plugin_list($type);
    if (!isset($plugins[$plugin])) {
        throw new coding_exception('Invalid plugin name');
    }

    $plugindir = $plugins[$plugin];
    $component = $type . '_' . $plugin;

    if ($type === 'mod') {
        if (!$DB->record_exists('modules', array('name' => $plugin))) {
            print_error('modulenotexist', 'debug', '', $plugin);
        }
    } else if ($type === 'block') {
        if (!$DB->record_exists('block', array('name' => $plugin))) {
            print_error('blocknotexist', 'debug', '', $plugin);
        }
    }

    // Unfortunately developers often copy/paste wrong parameters for this method,
    // so make sure to give them a warning when this function is called from wrong premigrate script.
    $upgradescript = $plugindir . '/db/premigrate.php';
    $bt = debug_backtrace();
    if (file_exists($upgradescript)) {
        $upgradescript = realpath($upgradescript);
        $found = false;
        foreach ($bt as $i => $trace) {
            if (realpath($trace['file']) === $upgradescript) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new coding_exception('savepoint was not called from appropriate premigrate.php file');
        }
    }

    $dbversion = $DB->get_field('config_plugins', 'value', array('plugin' => $component, 'name' => 'version'));

    if ($dbversion < $version) {
        // Something really wrong is going on in the upgrade script
        throw new coding_Exception('Plugin version cannot be increased during pre-migration');
    }

    set_config('version', $version, $component);
    upgrade_log(UPGRADE_LOG_NORMAL, $component, 'Pre-migration plugin savepoint reached');

    // Reset upgrade timeout to default
    upgrade_set_timeout();

    return premigrate_get_plugin_version($type, $plugin);
}
