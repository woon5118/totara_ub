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
 * @author: Simon Coggins <simon.coggins@totaralearning.com>
 * @package: mod_perform
 */

use mod_perform\rb\util;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/rb_source_perform_subject_instance.php');

/**
 * Subject instance manage participation report.
 *
 * Class rb_source_subject_instance_manage_participation
 */
class rb_source_subject_instance_manage_participation extends rb_source_perform_subject_instance {

    /**
     * Constructor.
     *
     * @param mixed $groupid
     * @param rb_global_restriction_set|null $globalrestrictionset
     * @throws coding_exception
     */
    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        parent::__construct($groupid, $globalrestrictionset);

        $this->selectable = false;

        $this->sourcetitle = get_string('sourcetitle', 'rb_source_subject_instance_manage_participation');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_subject_instance_manage_participation');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_subject_instance_manage_participation');
    }

    /**
     * Use post_config() to add restrictions based on the viewing user. Must be done here as viewing user not known until
     * after config is finalised.
     *
     * @param reportbuilder $report
     */
    public function post_config(reportbuilder $report) {
        // TODO multi-tenancy support in here too?
        $restrictions = util::get_manage_participation_sql($report->reportfor, "base.subject_user_id");
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

        // Add Participant Count
        $columnoptions[] = new rb_column_option(
            'subject_instance',
            'participant_count_manage_participation',
            get_string('participants', 'rb_source_subject_instance_manage_participation'),
            "(SELECT COUNT('x')
            FROM {perform_participant_instance} ppi
            WHERE ppi.subject_instance_id = base.id)",
            [
                'dbdatatype' => 'integer',
                'displayfunc' => 'participant_count_manage_participation',
                'iscompound' => true,
                'issubquery' => true,
                'extrafields' => [
                    'subject_instance_id' => "base.id",
                    'activity_id' => "perform.id",
                    'status' => 'base.status'
                ]
            ]
        );

        // Add actions
        $columnoptions[] = new rb_column_option(
            'subject_instance',
            'actions',
            get_string('actions', 'rb_source_subject_instance_manage_participation'),
            "base.id",
            [
                'displayfunc' => 'subject_instance_manage_participation_actions',
                'noexport' => true,
                'nosort' => true,
                'joins' => 'perform',
                'extrafields' => [
                    'activity_id' => 'perform.id',
                    'subject_instance_id' => "base.id",
                    'subject_instance_availability' => "base.availability",
                    'status' => 'base.status'
                ],
            ]
        );

        // Add column for default sorting on subject user and instance_number (desc)
        $seq_sql = "(SELECT 99999999 - COUNT('x')
                FROM {perform_subject_instance} psi
                WHERE psi.track_user_assignment_id = base.track_user_assignment_id
                  AND psi.created_at <= base.created_at)";
        $usednamefields = totara_get_all_user_name_fields_join('subject_user', null, true);
        $columnoptions[] = new rb_column_option(
            'subject_instance',
            'default_sort',
            get_string('default_sort', 'mod_perform'),
            $DB->sql_concat_join("' '", array_merge($usednamefields, [$seq_sql])),
            [
                'displayfunc' => 'format_string',
                'noexport' => true,
                'nosort' => true,
                'hidden' => true,
                'joins' => 'subject_user',
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
                'type' => 'subject_user',
                'value' => 'namelink',
                'heading' => get_string('subject_name', 'rb_source_perform_subject_instance')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'instance_number',
                'heading' => get_string('instance_number', 'mod_perform')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'created_at',
                'heading' => get_string('date_created', 'mod_perform')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'participant_count_manage_participation',
                'heading' => get_string('participants', 'rb_source_subject_instance_manage_participation')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'progress',
                'heading' => get_string('progress', 'mod_perform')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'availability',
                'heading' => get_string('availability', 'mod_perform')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'actions',
                'heading' => get_string('actions', 'rb_source_subject_instance_manage_participation')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'default_sort',
                'heading' => get_string('default_sort', 'mod_perform'),
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
                'type' => 'subject_user',
                'value' => 'fullname',
            ],
            [
                'type' => 'subject_instance',
                'value' => 'progress',
            ],
            [
                'type' => 'subject_instance',
                'value' => 'availability',
            ],
            [
                'type' => 'subject_instance',
                'value' => 'created_at',
            ],
        ];
    }
}
