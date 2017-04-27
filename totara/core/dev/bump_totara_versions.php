<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params(
    array(
        'run'    => false,
        'list'   => false,
        'help'    => false
    ),
    array(
        'h' => 'help'
    )
);

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if (!$options['run'] and !$options['list']) {
    $help =
        "Bump up all Totara plugin versions to today's date

Options:
-h, --help            Print out this help
--list                List all Totara plugins
--run                 Bump up all Totara plugin versions and requires

NOTE: you need to have git and 'releasemoodle' remote to use this script
";

    echo $help;
    exit(0);
}

if (dev_get_maturity() == MATURITY_STABLE) {
    cli_error('This tool is intended for Totara developers only!!!');
}

$output = null;
exec('git --version', $output, $code);
if ($code !== 0) {
    echo $output;
    cli_error('Error executing git');
}

$output = null;
exec('git remote', $output, $code);
if ($code !== 0) {
    echo $output;
    cli_error('Error executing git');
}
if (!in_array('releasemoodle', $output)) {
    cli_error('Branch \'releasemoodle\' does not exist, cannot continue');
}

list($moodleplugins, $totaraplugins) = dev_get_totara_and_moodle_plugins();

cli_heading('List of ' . count($totaraplugins) . ' Totara plugins');
$today = date('Ymd') . '00';
$requirement = dev_get_requires_version();
$error = false;
$todo = array();
foreach ($totaraplugins as $component => $fulldir) {
    $version = dev_get_plugin_version($fulldir);
    if ($version > $today) {
        cli_writeln(str_pad($component, 40, ' ', STR_PAD_RIGHT) . ' ' . $version . ' ERROR!');
        $error = true;
    } else {
        cli_writeln(str_pad($component, 40, ' ', STR_PAD_RIGHT) . ' ' . $version);
        $todo[] = array($component, $fulldir, $version);
    }
}

if ($error) {
    cli_error('Cannot bump versions, please check plugins with ERROR flag');
}

if (!$options['run']) {
    die;
}

$updated = array();
foreach ($todo as $data) {
    list($component, $fulldir, $version) = $data;
    $file = "$fulldir/version.php";
    $content = file_get_contents($file);
    $oldcontent = $content;
    if (preg_match('/(->version\s*=\s*)\'?[0-9\.]+\'?/', $content, $matches)) {
        $content = str_replace($matches[0], $matches[1] . $today, $content);
    }
    if (preg_match('/(->requires\s*=\s*)\'?[0-9\.]+\'?/', $content, $matches)) {
        $content = str_replace($matches[0], $matches[1] . $requirement, $content);
    }
    if ($oldcontent !== $content) {
        file_put_contents($file, $content);
        $updated[] = $component;
    }
}
cli_writeln('');
if (!$updated) {
    cli_heading("All plugins are already up-to-date");
} else {
    cli_heading("Updated ". count($updated) . " Totara plugins to $today with $requirement requirement");
    foreach ($updated as $component) {
        cli_writeln($component);
    }
}
die;


function dev_get_plugin_version($fulldir) {
    $plugin = new stdClass();
    $plugin->version = null;
    $module = $plugin;
    include($fulldir.'/version.php');

    return $plugin->version;
}

function dev_get_totara_and_moodle_plugins() {
    $totaraplugins = array();
    $moodleplugins = array();
    $types = core_component::get_plugin_types();
    foreach ($types as $type => $unused) {
        $plugins = core_component::get_plugin_list($type);
        foreach ($plugins as $name => $fulldir) {
            if (dev_is_moodle_plugin($type, $name, $fulldir)) {
                $moodleplugins[$type . '_' . $name] = $fulldir;
                continue;
            }
            $totaraplugins[$type . '_' . $name] = $fulldir;
        }
    }
    return array($moodleplugins, $totaraplugins);
}

function dev_is_moodle_plugin($type, $name, $fulldir) {
    if ($type === 'totara') {
        return false;
    }
    if (strpos($name, 'totara') !== false) {
        return false;
    }

    return dev_is_upstream_file("$fulldir/version.php");
}

/**
 * Get current Totara version from config.php.
 *
 * @return string
 */
function dev_get_totara_version() {
    global $CFG;
    $versionfile = $CFG->dirroot . '/version.php';
    $TOTARA = null;
    include($versionfile);
    return $TOTARA->version;
}

/**
 * Get current main version from config.php for 'requires',
 * the decimals are omitted.
 *
 * @return int
 */
function dev_get_requires_version() {
    global $CFG;
    $versionfile = $CFG->dirroot . '/version.php';
    $version = null;
    include($versionfile);
    return (int)floor($version);
}

/**
 * Get maturity
 *
 * @return int
 */
function dev_get_maturity() {
    global $CFG;
    $versionfile = $CFG->dirroot . '/version.php';
    $maturity = null;
    include($versionfile);
    return $maturity;
}

/**
 * Is the given file part of upstream Moodle?
 *
 * @param string $file
 * @return bool
 */
function dev_is_upstream_file($file) {
    global $CFG;
    $tag = '70fa678586c32a4710c62a59f71d63e02bd9e500'; // 3.2.2 - Totara 10
    $cwd = getcwd();
    chdir($CFG->dirroot);
    $file = substr($file, strlen($CFG->dirroot) +1);
    exec("git cat-file -e {$tag}:{$file} 2>/dev/null", $output, $status);
    chdir($cwd);
    return ($status === 0);
}

