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

trait rooms {

    /**
     * Add common room column options (excluding custom fields)
     *
     * @param array $columnoptions
     * @param string $join alias of join or table that provides room fields
     */
    protected function add_rooms_fields_to_columns(array &$columnoptions, $join = 'room') {
        $columnoptions[] = new rb_column_option(
            'room',
            'id',
            get_string('roomid', 'rb_source_facetoface_rooms'),
            "$join.id",
            array(
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'integer'
            )
        );

        $columnoptions[] = new rb_column_option(
            'room',
            'name',
            get_string('name', 'rb_source_facetoface_rooms'),
            "$join.name",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'format_string'
            )
        );

        $columnoptions[] = new rb_column_option(
            'room',
            'namelink',
            get_string('namelink', 'rb_source_facetoface_rooms'),
            "$join.name",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'f2f_room_name_link',
                'defaultheading' => get_string('name', 'rb_source_facetoface_rooms'),
                'extrafields' => array('roomid' => "$join.id", 'custom' => "{$join}.custom")
            )
        );

        $columnoptions[] = new rb_column_option(
            'room',
            'published',
            get_string('published', 'rb_source_facetoface_rooms'),
            "CASE WHEN $join.custom > 0 THEN 1 ELSE 0 END",
            array(
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'f2f_no_yes',
            )
        );

        $columnoptions[] = new rb_column_option(
            'room',
            'description',
            get_string('description', 'rb_source_facetoface_rooms'),
            "$join.description",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'room_description',
                'extrafields' => array('roomid' => "$join.id")
            )
        );

        $columnoptions[] = new rb_column_option(
            'room',
            'visible',
            get_string('visible', 'rb_source_facetoface_rooms'),
            "$join.hidden",
            array(
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'f2f_no_yes'
            )
        );

        $columnoptions[] = new rb_column_option(
            'room',
            'capacity',
            get_string('capacity', 'rb_source_facetoface_rooms'),
            "$join.capacity",
            array(
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'integer'
            )
        );

        $columnoptions[] = new rb_column_option(
            'room',
            'allowconflicts',
            get_string('allowconflicts', 'rb_source_facetoface_rooms'),
            "$join.allowconflicts",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'yes_or_no',
            )
        );

        $columnoptions[] = new rb_column_option(
            'room',
            'url',
            get_string('roomlink', 'rb_source_facetoface_rooms'),
            "$join.url",
            array(
                'joins' => $join,
                'dbdatatype' => 'text',
                'displayfunc' => 'room_url',
            )
        );
    }

    /**
     * Add common room filter options (excluding custom fields)
     * @param array $filteroptions
     */
    protected function add_rooms_fields_to_filters(array &$filteroptions) {
        $filteroptions[] = new rb_filter_option(
            'room',
            'id',
            get_string('roomid', 'rb_source_facetoface_rooms'),
            'number'
        );

        $filteroptions[] = new rb_filter_option(
            'room',
            'name',
            get_string('name', 'rb_source_facetoface_rooms'),
            'text'
        );

        $filteroptions[] = new rb_filter_option(
            'room',
            'published',
            get_string('published', 'rb_source_facetoface_rooms'),
            'select',
            array(
                'simplemode' => true,
                'selectchoices' => array('0' => get_string('yes'), '1' => get_string('no'))
            )
        );

        $filteroptions[] = new rb_filter_option(
            'room',
            'description',
            get_string('description', 'rb_source_facetoface_rooms'),
            'text'
        );

        $filteroptions[] = new rb_filter_option(
            'room',
            'visible',
            get_string('visible', 'rb_source_facetoface_rooms'),
            'select',
            array(
                'simplemode' => true,
                'selectchoices' => array('0' => get_string('yes'), '1' => get_string('no'))
            )
        );

        $filteroptions[] = new rb_filter_option(
            'room',
            'allowconflicts',
            get_string('allowconflicts', 'rb_source_facetoface_rooms'),
            'select',
            array(
                'simplemode' => true,
                'selectchoices' => array(1 => get_string('yes'), 0 => get_string('no'))
            )
        );

        $filteroptions[] = new rb_filter_option(
            'room',
            'capacity',
            get_string('capacity', 'rb_source_facetoface_rooms'),
            'number'
        );
    }
}