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

/*
 * This script in intended for Totara core developers only!
 *
 * Builds new file in releases directory using Moodle release checkout in current directory.
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    exit(1);
}

$cwd = getcwd();
if (!file_exists($cwd . '/TRADEMARK.txt') || !file_exists($cwd . '/config.php')) {
    echo "Invalid dev script use\n";
    exit(1);
}

define('CLI_SCRIPT', true);

// Include Moodle config from current directory, the code in this script
// must work in Moodle 3.5 and later - this is NOT Totara code.
require($cwd . '/config.php');

chdir($cwd);

$output = null;
exec('git --version', $output, $code);
if ($code !== 0) {
    echo $output;
    echo("Error executing git\n");
    exit(1);
}

$output = null;
exec('git tag --points-at HEAD', $output, $code);
if ($code !== 0) {
    echo("Error finding current tags\n");
    exit(1);
}

$releasetag = null;
foreach ($output as $tag) {
    if (preg_match('/^v3\.\d\.\d+(\.\d)?$/', $tag)) {
        $releasetag = $tag;
        break;
    }
}

if (!$releasetag) {
    echo("Error finding Moodle release tag\n");
    exit(1);
}

$versions = [];
$mainversion = dev_get_main_version_object($cwd);
$versions['tag'] = $releasetag;
$versions['version'] = $mainversion->version;
$versions['release'] = $mainversion->release;

$types = core_component::get_plugin_types();
foreach ($types as $type => $typedir) {
    $plugins = core_component::get_plugin_list($type);
    foreach ($plugins as $name => $plugindir) {
        $info = ['type' => $type, 'name' => $name];
        $plugin = dev_get_plugin_version_object($plugindir);
        $info['version'] = $plugin->version;
        $info['has_install'] = file_exists($plugindir . '/db/install.xml');
        $info['has_upgrade'] = false;
        if (file_exists($plugindir . '/db/upgrade.php')) {
            $upgrade = file_get_contents($plugindir . '/db/upgrade.php');
            $offset = (int)strpos($upgrade, 'Automatically generated Moodle v3.4.0 release upgrade line');
            if (strpos($upgrade, 'upgrade_plugin_savepoint', $offset) !== false) {
                $info['has_upgrade'] = true;
            }
        }
        $info['relative_path'] = substr($plugindir, strlen($CFG->dirroot));
        $versions['plugins'][$type . '_' . $name] = $info;
    }
}

$content = "<?php\n\n\$versions = " . var_export($versions, true) . ';';
file_put_contents(__DIR__ . '/../releases/' . $releasetag . '.php', $content);

die;

// Utility functions - not not attempt to abstract or move elsewhere!!!

/**
 * @param string $fulldir
 * @return stdClass
 */
function dev_get_plugin_version_object(string $fulldir): stdClass {
    $plugin = new stdClass();
    $plugin->version = null;
    $module = $plugin;
    require($fulldir . '/version.php');

    return $plugin;
}

/**
 * @param string $fulldir
 * @return stdClass
 */
function dev_get_main_version_object(string $fulldir): stdClass {
    $version = null;
    $release = null;
    require($fulldir . '/version.php');
    $mainversion = new stdClass();
    $mainversion->version = $version;
    $mainversion->release = $release;

    return $mainversion;
}
