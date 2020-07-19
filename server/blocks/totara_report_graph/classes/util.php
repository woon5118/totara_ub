<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2014 onwards Totara Learning Solutions LTD
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
 * @package block_totara_report_graph
 */

namespace block_totara_report_graph;

/**
 * Class util for report graph block.
 *
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package block_totara_report_graph
 */
class util {

    /**
     * Get the graph
     * @param \reportbuilder $report
     * @return string HTML for graph
     */
    public static function get_graph(\reportbuilder $report) {
        $graph = \totara_reportbuilder\local\graph\base::create_graph($report);
        return $graph->render(400,400);
    }

    /**
     * Get raw report record from database.
     * @param int $reportorsavedid
     * @return \stdClass
     */
    public static function get_report($reportorsavedid) {
        global $DB;

        // Fetch report even if type not set - users may fiddle with the setting in reportbuilder.

        if ($reportorsavedid > 0) {
            $sql = "SELECT r.id, r.fullname, r.timemodified AS rtimemodified, g.type,
                           NULL AS savedid, NULL AS userid, 0 AS gtimemodified, r.globalrestriction, r.contentmode
                     FROM {report_builder} r
                     JOIN {report_builder_graph} g ON g.reportid = r.id
                    WHERE r.id = :reportid";
            $report = $DB->get_record_sql($sql, array('reportid' => $reportorsavedid), IGNORE_MISSING);

        } else if ($reportorsavedid < 0) {
            $sql = "SELECT r.id, s.name AS fullname, r.timemodified AS rtimemodified, g.type,
                           s.id AS savedid, s.userid, g.timemodified AS gtimemodified, r.globalrestriction, r.contentmode
                      FROM {report_builder} r
                      JOIN {report_builder_graph} g ON g.reportid = r.id
                      JOIN {report_builder_saved} s ON s.reportid = r.id
                     WHERE s.id = :savedid AND s.ispublic <> 0";
            $report = $DB->get_record_sql($sql, array('savedid' => - $reportorsavedid), IGNORE_MISSING);

        } else {
            $report = false;
        }

        return $report;
    }

    /**
     * Cleans user input of max width and max height.
     *
     * @param string $input
     * @return null|string
     */
    public static function normalise_size_and_user_input($input) {
        if (trim($input) === '') {
            // Its empty, that is fine.
            return '';
        }
        $regex = '#^ *(?<size>\-?\d+(\.\d+)?|\-?\.\d+) *(?<unit>%|px|cm|em|em|in|mm|pc|pe|pt|px)? *$#i';
        if (preg_match($regex, $input, $matches)) {
            $size = (float)$matches['size'];
            if (empty($size)) {
                return '';
            }
            $unit = 'px';
            if (!empty($matches['unit'])) {
                $unit = \core_text::strtolower($matches['unit']);
            }
            return $size.$unit;
        }

        // Its not valid.
        return null;
    }
}
