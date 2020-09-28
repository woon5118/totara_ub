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

        $data_path = environment::get_data_path();

        mtrace("Import directory " . $data_path);
        environment::enforce_data_path_sanity();

        flag::must_not_in_progress(flag::ML);
        flag::must_not_in_progress(flag::IMPORT);

        flag::must_start(flag::IMPORT);

        mtrace('Starting import...');
        try {
            $tenants = [null];
            if ($CFG->tenantsenabled) {
                $tenants = $DB->get_records('tenant', ['suspended' => 0]);
            }

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
            flag::complete(flag::IMPORT);
            throw $e;
        }

        flag::must_complete(flag::IMPORT);

        mtrace('Import completed.');
    }
}