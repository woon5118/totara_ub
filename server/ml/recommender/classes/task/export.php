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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package ml_recommender
 */

namespace ml_recommender\task;

use ml_recommender\local\environment;
use ml_recommender\local\exporter;
use ml_recommender\local\flag;

/**
 * Class export performs all export logic
 */
class export extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('exportdatatask', 'ml_recommender');
    }

    public function execute() {
        global $CFG, $DB;
        $data_path = rtrim(environment::get_data_path(), '/\\') . '/';
        $tmp_path =  rtrim(environment::get_data_path(), '/\\') . '_tmp/';
        $backup_path =  rtrim(environment::get_data_path(), '/\\') . '_deleteme/';

        mtrace("Export directory " . $data_path);
        if (strlen(trim($data_path, '/\\')) < 3) {
            debugging('Recommenders data path (ml_recommender/data_path) must be 3 or more characters length');
            return;
        }

        if ($data_path == $CFG->dataroot) {
            debugging('Recommenders data path (ml_recommender/data_path) cannot be the same as site data root');
            return;
        }

        if (!is_dir($data_path)) {
            if (!mkdir($data_path, $CFG->directorypermissions, true)) {
                debugging('Error creating ML data directory: ' . $data_path);
                return;
            }
        }

        // Check that ML is not in progress
        if (file_exists($data_path . flag::ML_STARTED) && !file_exists($data_path . flag::ML_COMPLETED)) {
            flag::problem(
                'Machine Learning is in progress',
                $data_path . flag::ML_STARTED
            );
            return;
        }

        // Check that import is not in progress
        if (file_exists($data_path . flag::IMPORT_STARTED) && !file_exists($data_path . flag::IMPORT_COMPLETED)) {
            flag::problem(
                'Import is in progress',
                $data_path . flag::IMPORT_STARTED
            );
            return;
        }

        // Cleaning up and prepare working space
        if (file_exists($tmp_path)) {
            // Check that there is not parallel exporting process
            if (file_exists($tmp_path . flag::EXPORT_STARTED) && !file_exists($tmp_path . flag::EXPORT_COMPLETED)) {
                flag::problem(
                    'Export is in progress',
                    $data_path . flag::EXPORT_STARTED
                );
                return;
            }

            if (!fulldelete($tmp_path)) {
                debugging('Could not delete temp directory ' . $tmp_path);
                return;
            }
        }
        if (!mkdir($tmp_path, $CFG->directorypermissions, true)) {
            debugging('Error creating temp directory: ' . $tmp_path);
            return;
        }

        if (!file_put_contents($tmp_path . flag::EXPORT_STARTED, time())) {
            debugging("Could not put export started flag: " . $tmp_path . flag::EXPORT_STARTED);
            return;
        }

        $tenants = [null];
        if ($CFG->tenantsenabled) {
            $tenants = $DB->get_records('tenant', ['suspended' => 0]);
        }

        mtrace('Starting export...');

        $exporter = new exporter($tmp_path);
        foreach ($tenants as $tenant) {
            if ($tenant) {
                mtrace('Exporting for tenant '. $tenant->name);
                $exporter->set_tenant($tenant);
            }
            $exporter->export();
        }
        $exporter->export_tenants();

        if (!file_put_contents($tmp_path . flag::EXPORT_COMPLETED, time())) {
            debugging("Could not write export complete flag: " . $tmp_path . flag::EXPORT_COMPLETED);
            return;
        }

        // Put the files in their expected location
        if (!fulldelete($backup_path)) {
            debugging("Could not remove previous backup of export: " . $backup_path);
            return;
        }
        if (file_exists($data_path)) {
            if (!rename($data_path, $backup_path)) {
                debugging("Could not move previous export to backup location: " . $backup_path);
                return;
            }
        }
        if (!rename($tmp_path, $data_path)) {
            debugging('Could not move current export to expected location: ' . $data_path);
            return;
        }

        fulldelete($backup_path);

        mtrace('Export completed.');
    }
}