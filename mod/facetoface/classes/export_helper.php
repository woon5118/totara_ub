<?php
/*
* This file is part of Totara Learn
*
* Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_facetoface
*/

namespace mod_facetoface;

final class export_helper {

    /**
     * Download data in CSV format
     *
     * @param array $fields Array of column headings
     * @param string $datarows Array of data to populate table with
     * @param string $file Name of file for exportig
     * @return returns the CSV file
     */
    public static function download_csv($fields, $datarows, $file = null) {
        global $CFG;

        require_once($CFG->libdir . '/csvlib.class.php');

        $csvexport = new \csv_export_writer();
        $csvexport->set_filename($file);
        $csvexport->add_data($fields);

        $numfields = count($fields);
        foreach ($datarows as $record) {
            $row = array();
            for ($j = 0; $j < $numfields; $j++) {
                $row[] = (isset($record[$j]) ? $record[$j] : '');
            }
            $csvexport->add_data($row);
        }

        $csvexport->download_file();
        die;
    }

    /**
     * Download data in ODS format
     *
     * @param array $fields Array of column headings
     * @param string $datarows Array of data to populate table with
     * @param string $file Name of file for exportig
     * @return returns the ODS file
     */
    public static function download_ods($fields, $datarows, $file = null) {
        global $CFG;

        require_once("$CFG->libdir/odslib.class.php");
        $filename = clean_filename($file . '.ods');

        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");

        $workbook = new \MoodleODSWorkbook('-');
        $workbook->send($filename);

        $worksheet = array();

        $worksheet[0] = $workbook->add_worksheet('');
        $row = 0;
        $col = 0;

        foreach ($fields as $field) {
            $worksheet[0]->write($row, $col, strip_tags($field));
            $col++;
        }
        $row++;

        $numfields = count($fields);

        foreach ($datarows as $record) {
            for($col=0; $col<$numfields; $col++) {
                if (isset($record[$col])) {
                    $worksheet[0]->write($row, $col, html_entity_decode($record[$col], ENT_COMPAT, 'UTF-8'));
                }
            }
            $row++;
        }

        $workbook->close();
        die;
    }

    /**
     * Download data in XLS format
     *
     * @param array $fields Array of column headings
     * @param string $datarows Array of data to populate table with
     * @param string $file Name of file for exportig
     * @return returns the Excel file
     */
    public static function download_xls($fields, $datarows, $file = null) {
        global $CFG;

        require_once($CFG->libdir . '/excellib.class.php');
        $filename = clean_filename($file . '.xls');

        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
        header("Pragma: public");

        $workbook = new \MoodleExcelWorkbook('-');
        $workbook->send($filename);

        $worksheet = array();

        $worksheet[0] = $workbook->add_worksheet('');
        $row = 0;
        $col = 0;

        foreach ($fields as $field) {
            $worksheet[0]->write($row, $col, strip_tags($field));
            $col++;
        }
        $row++;

        $numfields = count($fields);

        foreach ($datarows as $record) {
            for ($col=0; $col<$numfields; $col++) {
                $worksheet[0]->write($row, $col, html_entity_decode($record[$col], ENT_COMPAT, 'UTF-8'));
            }
            $row++;
        }

        $workbook->close();
        die;
    }
}