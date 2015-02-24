<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author  Petr Skoda <petr.skoda@totaralms.com>
 * @package tool_timezoneimport
 */

/**
 * This script builds lib/timezone.txt, it is not intended for admins.
 */
define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/olson.php');

$help =
    "This script builds lib/timezone.txt

Steps:
  1/ download tzdata file form http://www.iana.org/time-zones
  2/ extract the archive contents into $CFG->tempdir/tzdata/
  3/ run this tool

NOTE: This tool is intended for Totara developers only
      because lib/timezone.txt file is overridden by web updates.

Options:
-h, --help            Print out this help.

Example:
\$ sudo -u www-data /usr/bin/php admin/tool/timezoneimport/cli/build_timezone.php
";

list($options, $unrecognized) = cli_get_params(
    array('help' => false), array('h' => 'help'));

if ($options['help']) {
    echo $help;
    exit(0);
}

// Force debugging.
$CFG->debug        = (E_ALL | E_STRICT);
$CFG->debugdisplay = true;

$datadir = "$CFG->tempdir/tzdata/";

if (!file_exists($datadir)) {
    cli_error("$datadir is not present - this tool is not intended for admins");
}

$makefile = $datadir . 'Makefile';
if (!file_exists($makefile)) {
    cli_error("$datadir does not contain timezone data files, cannot continue");
}
preg_match('/^VERSION\s*=\s*([0-9a-zA-Z]+)\s*$/m', file_get_contents($makefile), $matches);
if (isset($matches[1])) {
    echo "Processing timezone data version: {$matches[1]}\n";
} else {
    echo "Unknown timezone data version\n";
}

$files = array('africa', 'antarctica', 'asia', 'australasia', 'europe', 'northamerica', 'southamerica', 'etcetera');
$concat = '';
foreach ($files as $file) {
    $path = $datadir . $file;
    if (!file_exists($path)) {
        cli_error("$path is not present, cannot continue");
    }
    $concat .= file_get_contents($path);
}

$olsonfile = $datadir . 'olson.txt';
@unlink($olsonfile);
file_put_contents($olsonfile, $concat);
unset($concat);

$timezones = olson_to_timezones($olsonfile);
unlink($olsonfile);

if (!$timezones) {
    cli_error('Error parsing olson file');
}

update_timezone_records($timezones);

$fields = array(
    'name',
    'year',
    'tzrule',
    'gmtoff',
    'dstoff',
    'dst_month',
    'dst_startday',
    'dst_weekday',
    'dst_skipweeks',
    'dst_time',
    'std_month',
    'std_startday',
    'std_weekday',
    'std_skipweeks',
    'std_time',
);

$records = $DB->get_records('timezone', null, 'id ASC');

$content = implode(',', $fields) . "\n";
foreach ($records as $record) {
    $line = array();
    foreach ($fields as $field) {
        $line[] = $record->$field;
    }
    $content .= implode(',', $line) . "\n";
}

file_put_contents($CFG->libdir . '/timezone.txt', $content);
echo "File updated: {$CFG->libdir}/timezone.txt\n";
