<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package tabexport_csv
 */

namespace tabexport_csv;

use \totara_core\tabexport_source;
use \totara_core\tabexport_writer;

/**
 * Export data in CSV format.
 *
 * @package tabexport_csv
 */
class writer extends tabexport_writer {
    /**
     * Constructor.
     *
     * @param tabexport_source $source
     */
    public function __construct(tabexport_source $source) {
        $source->set_format('text');
        parent::__construct($source);

        // Increasing the execution time and available memory.
        \core_php_time_limit::raise(60 * 60 * 2);
        raise_memory_limit(MEMORY_HUGE);
    }

    /**
     * Add all data to export.
     *
     * @param \csv_export_writer $export
     */
    protected function add_all_data(\csv_export_writer $export) {
        $row = array();
        foreach ($this->source->get_headings() as $heading) {
            $row[] = $heading;
        }

        $export->add_data($row);

        foreach ($this->source as $row) {
            $export->add_data($row);
        }

        $this->source->close();
    }

    /**
     * Send the file to browser.
     *
     * @param string $filename without extension
     * @return void serves the file and exits.
     */
    public function send_file($filename) {
        global $CFG;
        require_once("{$CFG->libdir}/csvlib.class.php");

        $export = new \csv_export_writer();
        $export->filename = $filename . '.' . self::get_file_extension();
        $this->add_all_data($export);

        $export->download_file();
        die;
    }

    /**
     * Save to file.
     *
     * @param string $file full file path
     * @return bool success
     */
    public function save_file($file) {
        global $CFG;
        require_once("{$CFG->libdir}/csvlib.class.php");

        @unlink($file);

        $export = new \csv_export_writer();

        $export->filename = 'export.csv';
        $this->add_all_data($export);

        $fp = fopen($file, "w");
        fwrite($fp, $export->print_csv_data(true));
        fclose($fp);

        @chmod($file, (fileperms(dirname($file)) & 0666));
        return file_exists($file);
    }

    /**
     * Returns the file extension.
     *
     * @return string
     */
    public static function get_file_extension() {
        return 'csv';
    }
}
