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

namespace mod_facetoface\rb\traits;

use rb_column_option;
use rb_filter_option;

defined('MOODLE_INTERNAL') || die();

trait facilitator {

    /**
     * Add common facilitators column options (excluding custom fields)
     * @param array $columnoptions
     * @param string $join alias of join or table that provides facilitators fields
     */
    protected function add_facilitators_fields_to_columns(array &$columnoptions, $join = 'facilitator') {

        $columnoptions[] = new rb_column_option(
            'facilitator',
            'id',
            get_string('facilitatorid', 'mod_facetoface'),
            "$join.id",
            array(
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'integer',
            )
        );
        $columnoptions[] = new rb_column_option(
            'facilitator',
            'userid',
            get_string('facilitatoruserid', 'mod_facetoface'),
            "$join.userid",
            array(
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'integer',
            )
        );
        $columnoptions[] = new rb_column_option(
            'facilitator',
            'name',
            get_string('facilitatorname', 'mod_facetoface'),
            "$join.name",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'facilitator_name',
                'extrafields' => [
                    'userid' => "$join.userid",
                ],
            )
        );
        $columnoptions[] = new rb_column_option(
            'facilitator',
            'namelink',
            get_string('facilitatornamelink', 'mod_facetoface'),
            "$join.name",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'facilitator_name_link',
                'defaultheading' => get_string('facilitatorname', 'mod_facetoface'),
                'extrafields' => [
                    'id' => "$join.id",
                    'userid' => "$join.userid",
                ],
            )
        );
        $columnoptions[] = new rb_column_option(
            'facilitator',
            'published',
            get_string('published', 'mod_facetoface'),
            "CASE WHEN $join.custom > 0 THEN 1 ELSE 0 END",
            array(
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'f2f_no_yes',
            )
        );
        $columnoptions[] = new rb_column_option(
            'facilitator',
            'description',
            get_string('descriptionlabel', 'mod_facetoface'),
            "$join.description",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'facilitator_description',
                'extrafields' => [
                    'id' => "$join.id",
                ]
            )
        );
        $columnoptions[] = new rb_column_option(
            'facilitator',
            'visible',
            get_string('visible', 'mod_facetoface'),
            "$join.hidden",
            array(
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'f2f_no_yes',
            )
        );
        $columnoptions[] = new rb_column_option(
            'facilitator',
            'allowconflicts',
            get_string('allowfacilitatorconflicts', 'mod_facetoface'),
            "$join.allowconflicts",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'yes_or_no',
            )
        );
    }

    /**
     * Add common facilitator filter options (excluding custom fields)
     * @param array $filteroptions
     */
    protected function add_facilitators_fields_to_filters(array &$filteroptions) {

        $filteroptions[] = new rb_filter_option(
            'facilitator',
            'id',
            get_string('facilitatorid', 'mod_facetoface'),
            'number'
        );
        $filteroptions[] = new rb_filter_option(
            'facilitator',
            'name',
            get_string('facilitatorname', 'mod_facetoface'),
            'text'
        );
        $filteroptions[] = new rb_filter_option(
            'facilitator',
            'published',
            get_string('published', 'mod_facetoface'),
            'select',
            array(
                'simplemode' => true,
                'selectchoices' => array(
                    '0' => get_string('yes'),
                    '1' => get_string('no'),
                )
            )
        );
        $filteroptions[] = new rb_filter_option(
            'facilitator',
            'description',
            get_string('description', 'mod_facetoface'),
            'text'
        );
        $filteroptions[] = new rb_filter_option(
            'facilitator',
            'visible',
            get_string('visible', 'mod_facetoface'),
            'select',
            array(
                'simplemode' => true,
                'selectchoices' => array(
                    '0' => get_string('yes'),
                    '1' => get_string('no'),
                )
            )
        );
        $filteroptions[] = new rb_filter_option(
            'facilitator',
            'allowconflicts',
            get_string('allowfacilitatorconflicts', 'mod_facetoface'),
            'select',
            array(
                'simplemode' => true,
                'selectchoices' => array(
                    '1' => get_string('yes'),
                    '0' => get_string('no'),
                )
            )
        );
    }
}