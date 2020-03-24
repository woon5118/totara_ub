<?php
/*
* This file is part of Totara Perform
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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package mod_perform
*/

namespace mod_perform\rb\traits;

use rb_column_option;
use rb_filter_option;
use rb_join;

defined('MOODLE_INTERNAL') || die();

trait participant_subject_instance_source {

    /**
     * Add joints.
     *
     * @param array $joinlist
     * @param string $join
     */
    protected function add_to_joinlist(array &$joinlist, string $join = 'base') {
        $joinlist[] = new rb_join(
            'track_user_assignment',
            'INNER',
            '{perform_track_user_assignment}',
            "track_user_assignment.id = {$join}.track_user_assignment_id",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            $join
        );
        $joinlist[] = new rb_join(
            'track',
            'INNER',
            '{perform_track}',
            'track.id = track_user_assignment.track_id',
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'track_user_assignment'
        );
        $joinlist[] = new rb_join(
            'perform',
            'INNER',
            "{perform}",
            "perform.id = track.activity_id",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'track'
        );
        // $joinlist[] = new rb_join(
        //     'perform_type',
        //     'INNER',
        //     "{perform_type}",
        //     "perform_type.id = perform.type_id",
        //     REPORT_BUILDER_RELATION_ONE_TO_ONE,
        //     'perform'
        // );
    }

    /**
     * Define the column options available for this report.
     *
     * @param array $columnoptions
     * @param string $join
     * @param string $global_restriction_join_su
     */
    protected function add_fields_to_columns(array &$columnoptions, string $join = 'base', string $global_restriction_join_su) {
        $columnoptions[] = new rb_column_option(
            'perform',
            'name',
            get_string('activity_name', 'mod_perform'),
            'perform.name',
            [
                'joins' => ['perform', 'track', 'track_user_assignment'],
                'dbdatatype' => 'text',
                'outputformat' => 'text',
                'displayfunc' => 'format_string'
            ]
        );
        $columnoptions[] = new rb_column_option(
            'track',
            'description',
            get_string('track_description', 'mod_perform'),
            'track.description',
            [
                'joins' => ['track', 'track_user_assignment'],
                'dbdatatype' => 'text',
                'outputformat' => 'text',
                'displayfunc' => 'format_string'
            ]
        );
        $columnoptions[] = new rb_column_option(
            'participant_instance',
            'count',
            get_string('participant_count', 'rb_source_perform_subject_instance'),
            "(SELECT COUNT('x')
                FROM {perform_participant_instance} ppi
                {$global_restriction_join_su}
                WHERE ppi.subject_instance_id = {$join}.id)",
            [
                'joins' => $join,
                'dbdatatype' => 'integer',
                'displayfunc' => 'participant_count',
                'iscompound' => true,
                'issubquery' => true,
                'extrafields' => [
                    'subject_instance_id' => "{$join}.id"
                ]
            ]
        );
        // $columnoptions[] = new rb_column_option(
        //     'perform',
        //     'type',
        //     get_string('activity_type', 'mod_perform'),
        //     'perform_type.name',
        //     [
        //         'joins' => ['perform_type', 'perform', 'track', 'track_user_assignment'],
        //         'dbdatatype' => 'text',
        //         'outputformat' => 'text',
        //         'displayfunc' => 'format_string'
        //     ]
        // );
    }

    /**
     * Define the filter options available for this report.
     *
     * @param array $filteroptions
     */
    protected function add_fields_to_filters(array &$filteroptions) {
        $filteroptions[] = new rb_filter_option(
            'user',
            'namelink',
            get_string('subject_name', 'rb_source_perform_subject_instance'),
            'text'
        );
        $filteroptions[] = new rb_filter_option(
            'perform',
            'name',
            get_string('activity_name', 'mod_perform'),
            'text'
        );
        $filteroptions[] = new rb_filter_option(
            'track',
            'description',
            get_string('track_description', 'mod_perform'),
            'text'
        );
        // $filteroptions[] = new rb_filter_option(
        //     'perform',
        //     'type',
        //      get_string('perform_type', 'mod_perform'),
        //      'text'
        //  );
    }
}