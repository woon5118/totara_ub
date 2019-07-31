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
use ml_recommender\local\export\user_interactions_export;
use ml_recommender\local\export\user_data_export;
use ml_recommender\local\export\item_data_export;

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../server/config.php');
require_once($CFG->libdir.'/clilib.php');
require_once("{$CFG->dirroot}/lib/csvlib.class.php");

$usage = "
Export user, item and interactions data for recommendations processing.

Example:

    # php extensions/ml_recommender/cli/recommender_export.php
        Uses model generated through training on previously exported data.
        Note: Needs to be run before the model can be trained.
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

// List of exporters.
$exporters = [
    'user_interactions' => user_interactions_export::class,
    'user_data' => user_data_export::class,
    'item_data' => item_data_export::class,
];

// Prepare the ML data directory.
$data_path = environment::get_data_path();
if (!is_dir($data_path)) {
    if (!mkdir($data_path, $CFG->directorypermissions, true)) {
        cli_problem('Error creating ML data directory: ' . $data_path);
    }
}

// Run the data exports.
foreach ($exporters as $exportname => $exportclass) {
    // Delete old data.
    $csv_path = $data_path . '/' . $exportname . '.csv';
    @unlink($csv_path);

    // Instantiate data exporter.
    $export = new $exportclass();

    // Generate the csv content in temp file.
    $csv_writer = new \csv_export_writer('comma');
    $result = $export->export($csv_writer);

    // Copy exported data to data directory after successful completion.
    if ($result && isset($csv_writer->path) && file_exists($csv_writer->path)) {
        copy($csv_writer->path, $csv_path);
    } else {
        // Things are not ending well, flag it as such.
        $exit_status += 1;
    }
    unset($csv_writer);
}

exit($exit_status);
