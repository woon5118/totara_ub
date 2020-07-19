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

namespace tool_premigration\local;

/**
 * General helpers for Moodle pre-migration.
 */
final class util {
    /**
     * Returns list of backported Moodle plugins.
     *
     * @return array where key is plugin name and value is upstream version.
     */
    public static function get_backported_plugins() {
        static $backported = null;

        if ($backported === null) {
            $backported = [];
            $types = \core_component::get_plugin_types();
            foreach ($types as $type => $typedir) {
                $plugins = \core_component::get_plugin_list($type);
                foreach ($plugins as $name => $plugindir) {
                    $version = self::get_plugin_version_object($plugindir);
                    if (empty($version->backported)) {
                        continue;
                    }
                    $backported[$type . '_' . $name] = $version->version;
                }
            }
        }

        return $backported;
    }

    /**
     * Load list of core and plugin versions for given release.
     *
     * @param string $tag Moodle release tag
     * @return array|null
     */
    public static function load_release_versions(string $tag): ?array {
        $tag = clean_filename($tag);
        $versions = null;
        include(__DIR__ . '/../../releases/' . $tag . '.php');
        return $versions;
    }

    /**
     * Returns list of supported release version infos.
     *
     * return array
     */
    public static function get_supported_releases() {
        $releases = [];

        $files = scandir(__DIR__ . '/../../releases');
        foreach ($files as $file) {
            if (!preg_match('/^v.*\.php$/', $file)) {
                continue;
            }
            if ($file === 'v3.4.9.php') {
                continue;
            }
            $versions = [];
            include(__DIR__ . '/../../releases/' . $file);
            $releases[(string)$versions['version']] = $versions;
        }

        return $releases;
    }

    /**
     * Executes the pre-migration process.
     *
     * @param array $versions
     */
    public static function premigrate(array $versions) {
        global $CFG;
        require_once(__DIR__ . '/../../premigratelib.php');

        upgrade_log(UPGRADE_LOG_NORMAL, 'moodle', 'Starting Moodle pre-migration', get_upgrade_system_info());

        $plugins = $versions['plugins'];
        array_reverse($plugins);
        $targetversions = self::load_release_versions('v3.4.9');

        foreach ($plugins as $component => $info) {
            $plugindir = $CFG->dirroot . $info['relative_path'];
            if (!file_exists($plugindir . '/version.php')) {
                continue;
            }
            $oldversion = premigrate_get_plugin_version($info['type'], $info['name']);
            if ($oldversion === null) {
                // Not installed yet, weird.
                continue;
            }
            if (file_exists($plugindir . '/db/premigrate.php')) {
                require_once($plugindir . '/db/premigrate.php');
                $premigratefunction = 'xmldb_' . $component . '_premigrate';
                $premigratefunction();
            } else {
                if (!isset($targetversions['plugins'][$component])) {
                    // Not available in 3.4.9, the plugin will be uninstalled most likely.
                    continue;
                }
                $targetversion = $targetversions['plugins'][$component]['version'];
                premigrate_plugin_savepoint($targetversion, $info['type'], $info['name']);
            }
            $newversion = premigrate_get_plugin_version($info['type'], $info['name']);
            if ($oldversion === $newversion) {
                // Nothing changed, such as in a backported plugin that does not need any version change.
                continue;
            }

            // Do regular plugin post-upgrade stuff to fix caches.
            update_capabilities($component);
            log_update_descriptions($component);
            external_update_descriptions($component);
            \core\task\manager::reset_scheduled_tasks_for_component($component);
            message_update_providers($component);
            \core\message\inbound\manager::update_handlers_for_component($component);
            if ($info['type'] === 'message') {
                message_update_processors($info['name']);
            }
            upgrade_plugin_mnet_functions($component);
            \core_tag_area::reset_definitions_for_component($component);

            cli_write(str_pad($component . ':', 35));
            cli_writeln("{$oldversion} ---> {$newversion}");
        }

        require_once($CFG->dirroot . '/lib/db/premigrate.php');
        xmldb_core_premigrate();

        // Update cache stuff.
        update_capabilities('moodle');
        log_update_descriptions('moodle');
        external_update_descriptions('moodle');
        \core\task\manager::reset_scheduled_tasks_for_component('moodle');
        message_update_providers('moodle');
        \core\message\inbound\manager::update_handlers_for_component('moodle');
        \core_tag_area::reset_definitions_for_component('moodle');
        \cache_helper::update_definitions(true);

        unset_config('upgraderunning');
        unset_config('premigrateversion');

        // Do a brute force cache purging, the regular reset may complain a lot.
        remove_dir($CFG->cachedir, true);
        remove_dir($CFG->localcachedir, true);

        upgrade_log(UPGRADE_LOG_NORMAL, 'moodle', 'Finished Moodle pre-migration');
    }

    /**
     * Return plugin version.
     *
     * @param string $fulldir
     * @return \stdClass
     */
    protected static function get_plugin_version_object(string $fulldir): \stdClass {
        $plugin = new \stdClass();
        $plugin->version = null;
        $module = $plugin;
        require($fulldir . '/version.php');

        return $plugin;
    }
}
