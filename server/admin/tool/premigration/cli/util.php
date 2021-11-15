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

use tool_premigration\local\util;

define('CLI_SCRIPT', true);
define('MOODLE_PREMIGRATION_SCRIPT', true);
define('NO_UPGRADE_CHECK', true);
define('CACHE_DISABLE_ALL', true);

if (function_exists('opcache_reset')) {
    opcache_reset();
}

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

$help = "Prepare site for migration from Moodle to Totara 13.

This script transforms the database to Moodle 3.4.9 format,
please make sure to make a full site backup to prevent data loss.

Options:
--status              Diagnose current system state.
--releases            List supported Moodle releases.
--execute             Run Moodle pre-migration.
-h, --help            Print out this help.

Example:
\$ sudo -u www-data /usr/bin/php admin/tool/premigration/cli/util.php
";

list($options, $unrecognized) = cli_get_params(
    array(
        'status' => false,
        'releases' => false,
        'execute' => false,
        'help' => false,
    ),
    array(
        'h' => 'help',
    )
);

if ($options['help'] || (!$options['status'] && !$options['releases'] && !$options['execute'])) {
    echo $help;
    exit(0);
}

if ($options['status']) {
    if (isset($CFG->totara_release)) {
        cli_writeln('Site data format: Totara ' . $CFG->totara_release);
        cli_writeln('Migration not necessary');
        exit(0);
    } else {
        cli_writeln('Site data format: Moodle ' . $CFG->release);
        if ($CFG->version == MOODLE_MIGRATION_VERSION) {
            cli_writeln('Site is ready for migration, continue with regular upgrade');
            exit(0);
        }
        $releases = util::get_supported_releases();
        if (!isset($releases[$CFG->version])) {
            cli_error('Current version is not supported in pre-migration, Moodle site cannot be migrated');
            exit(1);
        }
        cli_writeln('Site is ready for pre-migration');
        exit(0);
    }
}

if ($options['releases']) {
    $releases = util::get_supported_releases();
    foreach ($releases as $release) {
        cli_writeln('Moodle ' . $release['release']);
    }
    exit(0);
}

if ($options['execute']) {
    if (isset($CFG->totara_release)) {
        cli_error('Site was already migrated to Totara');
    }
    if ($CFG->version != MOODLE_MIGRATION_VERSION) {
        if (!isset($CFG->premigrateversion)) {
            set_config('premigrateversion', $CFG->version);
        }
        $releases = util::get_supported_releases();
        if (!isset($releases[$CFG->premigrateversion])) {
            cli_error("Unsupported Moodle release detected {$CFG->premigrateversion} - {$CFG->release}");
        }
        util::premigrate($releases[$CFG->premigrateversion]);
    }
    cli_writeln('Site data format: Moodle ' . $CFG->release);
    cli_writeln('Site is ready for migration, continue with regular upgrade');
    exit(0);
}
