<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_engage
 */

define('CLI_SCRIPT', true);
global $CFG, $USER, $DB;

// Include necessary libs
require(__DIR__ . "/../../../config.php");
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot.'/lib/testing/classes/util.php');

$help = "
Options:
    -u, --user_id               The actor user
    --number                    Number of instances
    --component                 The component of that instance
    -h, --help                  Print out this help.

Example:
    \$sudo -u www-data /usr/bin/php totara/engage/cli/generate_contributions.php --number=3 --component=totara_playlist
";

// now get cli options
[$options, $unrecognized] = cli_get_params(
    [
        'number'    => '1',
        'component' => null,
        'help'      => false,
        'user_id'   => null,
    ],
    [
        'h' => 'help',
        'u' => 'user_id'
    ]
);

if ($options['help']) {
    echo $help;
    return 0;
}

if (!isset($options['user_id'])) {
    // Set user to admin.
    $USER = get_admin();
} else {
    $USER = $DB->get_record('user', ['id' => $options['user_id']], '*', MUST_EXIST);
}

require_once("{$CFG->dirroot}/totara/engage/tests/fixtures/engage_generator_helper.php");
$generators = engage_generator_helper::get_generators();

if (isset($options['component'])) {
    $component = $options['component'];
    if (!isset($generators[$component])) {
        echo "No generator for component '{$component}'\n";
        return 1;
    }

    $generator = $generators[$component];
    $generators = [$generator];
}

foreach ($generators as $generator) {
    for ($i = 0; $i < $options['number']; $i++) {
        $generator->generate_random();
    }
}

return 0;