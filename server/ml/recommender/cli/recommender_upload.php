<?php
/**
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
 * @author Vernon Denny <vernon.denny@totaralearning.com>
 * @package core
 */

use ml_recommender\local\environment;
use ml_recommender\local\import\bulk_item_predictions;
use ml_recommender\local\import\bulk_user_predictions;

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../server/config.php');
require_once($CFG->libdir.'/clilib.php');

$usage = "
Upload bulk user and item recommendations as delivered by recommender system prediction.

Example:

    # php admin/cli/ml_recommender_upload.php
        Uploads model generated predictions from csv file.
        Note: Needs to be run after a model predictions have been run.
";

list($options, $unrecognised) = cli_get_params([
    'help' => false,
], [
    'h' => 'help'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL.'  ', $unrecognised);
    cli_error('Unrecognised parameter: ' .  $unrecognised);
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}

// Assume things will end well.
$exit_status = 0;

// Check that required ML files are present.
$data_path = environment::get_data_path();
$mlfiles = [
    'i2u.csv',
    'i2i.csv'
];

foreach ($mlfiles as $filename) {
    $filepath = $data_path . '/' . $filename;
    if (!file_exists($filepath)) {
        cli_problem('Missing ML upload data file: ' . $filepath);
        $exit_status += 1;
    }
}

// Are we good to go?
if ($exit_status > 0) {
    // Nope, quit now.
    cli_error('Error - ML prerequisites not met.', $exit_status);
}

// Upload items per user.
$user_recommendations = new bulk_user_predictions('i2u');
$user_recommendations->upload();

// Upload items per item.
$item_recommendations = new bulk_item_predictions('i2i');
$item_recommendations->upload();

// And we're done.
exit($exit_status);
