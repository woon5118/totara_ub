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

namespace ml_recommender\local;

use ml_recommender\local\csv\writer;
use ml_recommender\local\export\export;

class exporter {
    /**
     * @var \stdClass
     */
    private $tenant = null;

    /**
     * Track of all processed tenants
     * @var int[]
     */
    private $tenantids = [];

    /**
     * @var export[]
     */
    private $exports = [];

    /**
     * @var string directory path for CSV files
     */
    private $data_path = '';

    /**
     * exporter constructor.
     *
     * @param string $data_path directory path for CSV files
     */
    public function __construct(string $data_path) {
        $this->data_path = $data_path;
    }

    /**
     * Get array of exporter instances
     * @return export[]
     */
    public function get_exports(): array {
        if (!empty($this->exports)) {
            return $this->exports;
        }

        $classes = \core_component::get_namespace_classes(
            'local\\export',
            export::class,
            'ml_recommender'
        );

        foreach ($classes as $class) {
            $this->exports[] = new $class();
        }

        return $this->exports;
    }

    /**
     * Limit export to one tenant only
     * @param \stdClass $tenant
     */
    public function set_tenant(\stdClass $tenant) {
        $this->tenant = $tenant;
    }

    /**
     * Run all exporters and save their results into CSV files in given folder
     */
    public function export() {
        $exporters = $this->get_exports();

        foreach ($exporters as $exporter) {
            $id = 0;
            if (!empty($this->tenant)) {
                $id = $this->tenant->id;
                $this->tenantids[$id] = $id;
            }

            $csv_path = $this->data_path . '/' . $exporter->get_name() . '_' . $id . '.csv';
            $csv_writer = new writer($csv_path);

            if (!empty($this->tenant)) {
                $exporter->set_tenant($this->tenant);
            }
            $exporter->export($csv_writer);
        }
    }

    /**
     * Save all registered tenants during export into CSV file
     */
    public function export_tenants() {
        $writer = new writer($this->data_path . '/tenants.csv');
        $writer->add_headings(['tenants']);
        foreach ($this->tenantids as $tenantid) {
            $writer->add_data([$tenantid]);
        }
        $writer->close();
    }
}