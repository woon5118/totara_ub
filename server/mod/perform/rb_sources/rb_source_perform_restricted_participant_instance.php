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
 * @author: Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package: mod_perform
 */

use mod_perform\rb\util;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/rb_source_perform_participant_instance.php');

/**
 * Restricted Performance participant instance report.
 *
 * Class rb_source_perform_restricted_participant_instance
 */
class rb_source_perform_restricted_participant_instance extends rb_source_perform_participant_instance {

    /**
     * Constructor.
     *
     * @param mixed $groupid
     * @param rb_global_restriction_set|null $globalrestrictionset
     * @throws coding_exception
     */
    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        parent::__construct($groupid, $globalrestrictionset);

        $this->sourcetitle = get_string('sourcetitle', 'rb_source_perform_restricted_participant_instance');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_perform_restricted_participant_instance');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_perform_restricted_participant_instance');
    }

    /**
     * Use post_config() to add restrictions based on the viewing user. Must be done here as viewing user not known until
     * after config is finalised.
     *
     * @param reportbuilder $report
     * @throws coding_exception
     * @throws dml_exception
     */
    public function post_config(reportbuilder $report) {
        // TODO multi-tenancy support in here too?
        $restrictions = util::get_manage_participation_sql($report->reportfor, "subject_instance.subject_user_id");
        $report->set_post_config_restrictions($restrictions);
    }

    /**
     * Define the column options available for this report.
     *
     * @return array
     */
    protected function define_columnoptions() {
        global $DB;

        $columnoptions = parent::define_columnoptions();

        // Add Restricted Participant Count
        $columnoptions[] = new rb_column_option(
            'participant_instance',
            'restricted_section_count',
            get_string('sections', 'rb_source_perform_restricted_participant_instance'),
            "(SELECT COUNT('x')
            FROM {perform_participant_section} pps
            WHERE pps.participant_instance_id = base.id)",
            [
                'dbdatatype' => 'integer',
                'displayfunc' => 'restricted_section_count',
                'iscompound' => true,
                'issubquery' => true,
                'joins' => 'track',
                'extrafields' => [
                    'activity_id' => 'track.activity_id',
                    'participant_instance_id' => 'base.id',
                ]
            ]
        );

        // Add actions
        $columnoptions[] = new rb_column_option(
            'participant_instance',
            'actions',
            get_string('actions', 'rb_source_perform_restricted_participant_instance'),
            "base.id",
            [
                'displayfunc' => 'restricted_participant_instance_actions',
                'noexport' => true,
                'nosort' => true,
                'joins' => 'track',
                'capability' => ['mod/perform:manage_subject_user_participation'],
                'extrafields' => [
                    'activity_id' => 'track.activity_id',
                    'participant_instance_id' => 'base.id',
                    'participant_availability' => 'base.availability',
                ],
            ]
        );

        // Add column for default sorting on participant user, subject user and participant instance created_at DESC
        $seq_sql = "(SELECT 99999999 - COUNT('x')
                FROM {perform_participant_instance} ppi
                WHERE ppi.subject_instance_id = base.subject_instance_id
                  AND ppi.created_at <= base.created_at)";
        $participant_usednamefields = totara_get_all_user_name_fields_join('participant_user', null, true);
        $subject_usednamefields = totara_get_all_user_name_fields_join('subject_user', null, true);
        $columnoptions[] = new rb_column_option(
            'participant_instance',
            'default_sort',
            'default_sort',
            $DB->sql_concat_join("' '", array_merge(
                array_values($participant_usednamefields),
                array_values($subject_usednamefields),
                [$seq_sql]
            )),
            [
                'displayfunc' => 'format_string',
                'noexport' => true,
                'nosort' => true,
                'hidden' => true,
                'joins' => ['subject_user', 'participant_user'],
                'selectable' => true,
            ]
        );

        return $columnoptions;
    }

    /**
     * The default columns for this and embedded reports.
     *
     * @return array
     */
    public static function get_default_columns() {
        return [
            [
                'type' => 'participant_user',
                'value' => 'namelink',
                'heading' => get_string('participant_name', 'rb_source_perform_participant_instance')
            ],
            [
                'type' => 'subject_user',
                'value' => 'namelink',
                'heading' => get_string('subject_name', 'rb_source_perform_subject_instance')
            ],
            [
                'type' => 'core_relationship',
                'value' => 'class_name',
                'heading' => get_string('relationship', 'mod_perform')
            ],
            [
                'type' => 'participant_instance',
                'value' => 'created_at',
                'heading' => get_string('date_created', 'mod_perform')
            ],
            [
                'type' => 'participant_instance',
                'value' => 'restricted_section_count',
                'heading' => get_string('sections', 'rb_source_perform_restricted_participant_instance')
            ],
            [
                'type' => 'participant_instance',
                'value' => 'progress',
                'heading' => get_string('progress', 'mod_perform')
            ],
            [
                'type' => 'participant_instance',
                'value' => 'availability',
                'heading' => get_string('availability', 'mod_perform')
            ],
            [
                'type' => 'participant_instance',
                'value' => 'actions',
                'heading' => get_string('actions', 'rb_source_perform_restricted_participant_instance')
            ],
            [
                'type' => 'participant_instance',
                'value' => 'default_sort',
                'heading' => 'default_sort',
                'hidden' => true,
            ],
        ];
    }

    /**
     * The default filters for this and embedded reports.
     *
     * @return array
     */
    public static function get_default_filters() {
        return [
            [
                'type' => 'participant_user',
                'value' => 'fullname',
            ],
            [
                'type' => 'core_relationship',
                'value' => 'core_relationship_id',
            ],
            [
                'type' => 'subject_user',
                'value' => 'fullname',
            ],
            [
                'type' => 'participant_instance',
                'value' => 'progress',
            ],
            [
                'type' => 'participant_instance',
                'value' => 'availability',
            ],
            [
                'type' => 'participant_instance',
                'value' => 'created_at',
            ],
        ];
    }
}
