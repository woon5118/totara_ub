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

/*
 * This script in intended for Totara core developers only!
 *
 * Creates missing db/premigrate.php files when adding support for new releases
 * using guessing based on contents of db/upgrade.php files.
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $params) = cli_get_params(['domagic' => false]);

if (empty($options['domagic'])) {
    cli_error('Invalid dev script use');
}

define('DEV_MIGRATION_TAG', 'v3.4.9');
define('DEV_RELEASE_TAG', 'v3.5.10'); // NOTE: edit when creating premigration files for later releases.


$versions = util::load_release_versions(DEV_RELEASE_TAG);
$targetversions = util::load_release_versions(DEV_MIGRATION_TAG);
$backported = util::get_backported_plugins();

foreach ($versions['plugins'] as $component => $info) {
    if (!$info['has_upgrade'] && !isset($backported[$component])) {
        continue;
    }
    if (!isset($targetversions['plugins'][$component])) {
        continue;
    }
    $plugindir = $CFG->dirroot . $info['relative_path'];
    if (!file_exists($plugindir . '/version.php')) {
        continue;
    }
    if (file_exists($plugindir . '/db/premigrate.php')) {
        continue;
    }
    if (!file_exists($plugindir . '/db')) {
        mkdir($plugindir . '/db');
    }
    $content = dev_get_premigrate_file($info['type'], $info['name'], $info['version'], $targetversions['plugins'][$component]['version']);
    file_put_contents($plugindir . '/db/premigrate.php', $content);
    cli_writeln($info['relative_path'] . '/db/premigrate.php');
}

die;

// Utility functions - not not attempt to abstract or move elsewhere!!!

function dev_get_premigrate_file(string $type, string $plugin, $maxversion, $targetversion) {
    return <<<EOT
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
 * @package {$type}_{$plugin}
 */

/**
 * Transforms plugin data to Moodle data format supported in migration.
 */
function xmldb_{$type}_{$plugin}_premigrate() {
    global \$DB;
    \$dbman = \$DB->get_manager();

    \$version = premigrate_get_plugin_version('{$type}', '{$plugin}');

    if (\$version > {$maxversion}) {
        throw new coding_exception("Invalid plugin ({$type}_{$plugin}) version (\$version) for pre-migration");
    }


// TODO
throw new Exception('TODO implement {$type}_{$plugin} db/premigrate.php');


    // Plugin is ready for migration from Moodle 3.4.9 to Totara 13.
    if (\$version > {$targetversion}) {
        \$version = premigrate_plugin_savepoint({$targetversion}, '{$type}', '{$plugin}');
    }
}
EOT;
}
