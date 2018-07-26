<?php
/*
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
 * @author Alastair Munro <alastair.munro@totaralearning.com>
 * @package totara_certification
 */

namespace totara_certification\rb\source;

defined('MOODLE_INTERNAL') || die();

trait report_trait {
    /**
     * Adds the certification table to the $joinlist array
     *
     * @param array &$joinlist Array of current join options
     *                         Passed by reference and updated to
     *                         include new table joins
     * @param string $join Name of the join that provides the
     *                     'certif id' field
     * @param string $field Name of table containing program id field to join on
     */
    protected function add_totara_certification_tables(&$joinlist, $join, $field) {

        $joinlist[] = new \rb_join(
            'certif',
            'inner',
            '{certif}',
            "certif.id = $join.$field",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            $join
        );
    }

    /**
     * Adds some common certification info to the $columnoptions array
     *
     * @param array &$columnoptions Array of current column options
     *                              Passed by reference and updated by
     *                              this method
     * @param string $join Name of the join that provides the 'program' table
     * @param string $langfile Source for translation, totara_program or totara_certification
     *
     * @return Boolean
     */
    protected function add_totara_certification_columns(&$columnoptions, $join = 'certif', $langfile = 'totara_certification') {
        $columnoptions[] = new \rb_column_option(
            'certif',
            'recertifydatetype',
            get_string('recertdatetype', 'totara_certification'),
            "$join.recertifydatetype",
            array(
                'joins' => $join,
                'displayfunc' => 'certif_recertify_date_type',
            )
        );

        $columnoptions[] = new \rb_column_option(
            'certif',
            'activeperiod',
            get_string('activeperiod', 'totara_certification'),
            "$join.activeperiod",
            array('joins' => $join,
                  'dbdatatype' => 'char',
                  'outputformat' => 'text')
        );

        $columnoptions[] = new \rb_column_option(
            'certif',
            'windowperiod',
            get_string('windowperiod', 'totara_certification'),
            "$join.windowperiod",
            array('joins' => $join,
                  'dbdatatype' => 'char',
                  'outputformat' => 'text')
        );

        return true;
    }

    /**
     * Adds some common certification filters to the $filteroptions array
     *
     * @param array &$filteroptions Array of current filter options
     *                              Passed by reference and updated by
     *                              this method
     * @param string $langfile Source for translation, totara_program or totara_certification
     * @return boolean
     */
    protected function add_totara_certification_filters(&$filteroptions, $langfile = 'totara_certification') {

        $filteroptions[] = new \rb_filter_option(
            'certif',
            'recertifydatetype',
            get_string('recertdatetype', 'totara_certification'),
            'select',
            array(
                'selectfunc' => 'recertifydatetype',
            )
        );

        $filteroptions[] = new \rb_filter_option(
            'certif',
            'activeperiod',
            get_string('activeperiod', 'totara_certification'),
            'text'
        );

        $filteroptions[] = new \rb_filter_option(
            'certif',
            'windowperiod',
            get_string('windowperiod', 'totara_certification'),
            'text'
        );

        return true;
    }
}
