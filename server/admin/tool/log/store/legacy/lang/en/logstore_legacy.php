<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Legacy log reader lang strings.
 *
 * @package    logstore_legacy
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['eventlegacylogged'] = 'Legacy event logged';
$string['loglegacy'] = 'Log legacy data';
$string['loglegacy_help'] = 'This plugin records log data to the legacy log table (mdl_log). This functionality has been replaced by newer, richer and more efficient logging plugins, so you should only run this plugin if you have old custom reports that directly query the old log table. Writing to the legacy logs will increase load, so it is recommended that you disable this plugin for performance reasons when it is not needed.';
$string['pluginname'] = 'Legacy log (deprecated)';
$string['pluginname_desc'] = 'A log plugin that stores log entries in the legacy log table.';
$string['taskcleanup'] = 'Legacy log table cleanup';
$string['userdataitemlegacy_log'] = 'Legacy log';
$string['userdataitemlegacy_log_help'] = 'When purging, this will anonymise all log entries but won\'t delete them.';
