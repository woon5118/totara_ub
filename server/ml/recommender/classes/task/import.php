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
use ml_recommender\local\flag;
use ml_recommender\local\importer;

/**
 * Class import performs all import logic
 */
class import extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('importdatatask', 'ml_recommender');
    }

    public function execute() {
        global $CFG, $DB;

        $data_path = rtrim(environment::get_data_path(), '/\\') . '/';

        mtrace("Import directory " . $data_path);
        if (strlen(trim($data_path, '/\\')) < 3) {
            debugging('Recommenders data path (ml_recommender/data_path) must be 3 or more characters length');
            return;
        }

        if ($data_path == $CFG->dataroot) {
            debugging('Recommenders data_path cannot be the same as site data root');
            return;
        }

        if (!is_dir($data_path) || !is_readable($data_path)) {
            debugging('Cannot read directory: ' . $data_path);
            return;
        }

        if (!file_exists($data_path . flag::ML_COMPLETED)) {
            flag::problem(
                'Machine Learning processing is not completed',
                $data_path . flag::ML_COMPLETED
            );
            return;
        }
        if (file_exists($data_path . flag::IMPORT_STARTED)) {
            flag::problem(
                'Import is already started',
                $data_path . flag::IMPORT_STARTED
            );
            return;
        }

        if (!file_put_contents($data_path . flag::IMPORT_STARTED, time())) {
            debugging("Could not put import started lock. This can cause issues later.");
        }

        $tenants = [null];
        if ($CFG->tenantsenabled) {
            $tenants = $DB->get_records('tenant', ['suspended' => 0]);
        }

        mtrace('Starting import...');

        try {
            $importer = new importer($data_path, time());
            $tenants_csv = $importer->load_tenants();
            foreach ($tenants as $tenant) {
                if ($tenant) {
                    mtrace('Importing for tenant ' . $tenant->name);
                    if (!in_array($tenant->id, $tenants_csv)) {
                        debugging("Tenant {$tenant->name} not found in CSV. Skipping.");
                        continue;
                    }
                    $importer->set_tenant($tenant);
                }
                $importer->import();
            }
            mtrace("Cleaning up old recommendations...");
            $importer->clean();
        } catch (\Exception $e) {
            debugging($e->getMessage());
            return;
        }

        if (!file_put_contents($data_path . flag::IMPORT_COMPLETED, time())) {
            debugging("Could not put import completed lock. This might cause more issues later.");
        }

        mtrace('Import completed.');
    }
}