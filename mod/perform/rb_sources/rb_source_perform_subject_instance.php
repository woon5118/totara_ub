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
 * @author: Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package: mod_perform
 */

defined('MOODLE_INTERNAL') || die();

use mod_perform\rb\traits\participant_subject_instance_source;
use mod_perform\state\state_helper;
use totara_core\advanced_feature;
use totara_job\rb\source\report_trait;

/**
 * Performance subject instance report.
 *
 * Class rb_source_perform_subject_instance
 */
class rb_source_perform_subject_instance extends rb_base_source {
    use report_trait;
    use participant_subject_instance_source;

    /**
     * Constructor.
     *
     * @param mixed $groupid
     * @param rb_global_restriction_set|null $globalrestrictionset
     * @throws coding_exception
     */
    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('base', 'user_id');

        $this->sourcetitle = get_string('sourcetitle', 'rb_source_perform_subject_instance');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_perform_subject_instance');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_perform_subject_instance');

        $this->base = '{perform_subject_instance}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->usedcomponents[] = 'mod_perform';

        parent::__construct();
    }

    /**
     * Global report restrictions are implemented in this source.
     *
     * @return boolean
     */
    public function global_restrictions_supported() {
        return true;
    }

    /**
     * Define join table list.
     *
     * @return array
     */
    protected function define_joinlist() {
        $joinlist = [];
        $this->add_to_joinlist($joinlist);
        $this->add_core_user_tables($joinlist, 'base', 'subject_user_id');
        //$this->add_totara_job_tables($joinlist, 'base', 'job_assignment_id');

        return $joinlist;
    }

    /**
     * Define the column options available for this report.
     *
     * @return array
     */
    protected function define_columnoptions() {
        $global_restriction_join_su = $this->get_global_report_restriction_join('su', 'userid');

        $columnoptions = [
            new rb_column_option(
                'subject_instance',
                'created_at',
                get_string('date_created', 'rb_source_perform_subject_instance'),
                'base.created_at',
                [
                    'dbdatatype' => 'timestamp',
                    'displayfunc' => 'nice_date'
                ]
            ),
            new rb_column_option(
                'participant_instance',
                'count',
                get_string('participant_count', 'rb_source_perform_subject_instance'),
                "(SELECT COUNT('x')
                FROM {perform_participant_instance} ppi
                {$global_restriction_join_su}
                WHERE ppi.subject_instance_id = base.id)",
                [
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'participant_count',
                    'iscompound' => true,
                    'issubquery' => true,
                    'extrafields' => [
                        'subject_instance_id' => "base.id"
                    ]
                ]
            ),
            new rb_column_option(
                'subject_instance',
                'subject_status',
                get_string('subject_instance_status', 'rb_source_perform_subject_instance'),
                'base.progress',
                [
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'subject_progress'
                ]
            ),
            new rb_column_option(
                'subject_instance',
                'instance_count',
                get_string('instance_count', 'rb_source_perform_subject_instance'),
                // TODO: delete it
                '\'0\'', // "base.instance_count",
                [
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'integer'
                ]
            ),
            new rb_column_option(
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
            )

        // new rb_column_option(
            //     'subject_instance',
            //     'subject_date_completion',
            //     get_string('date_completion', 'rb_source_perform_subject_instance'),
            //     'base.???',
            //     [
            //         'dbdatatype' => 'timestamp',
            //         'displayfunc' => 'nice_date'
            //     ]
            // ),
        ];

        $this->add_fields_to_columns($columnoptions);
        $this->add_core_user_columns($columnoptions);
        //$this->add_totara_job_columns($columnoptions);

        return $columnoptions;
    }

    /**
     * Define the filter options available for this report.
     *
     * @return array
     */
    protected function define_filteroptions() {
        $filteroptions = [
            new rb_filter_option(
                'subject_instance',
                'created_at',
                get_string('date_created', 'rb_source_perform_subject_instance'),
                'date'
            ),
            new rb_filter_option(
                'user',
                'namelink',
                get_string('subject_name', 'rb_source_perform_subject_instance'),
                'text'
            ),
            new rb_filter_option(
                'subject_instance',
                "subject_status",
                get_string('subject_instance_status', 'mod_perform'),
                'select',
                [
                    'selectchoices' => state_helper::get_all_display_names('subject_instance'),
                    'simplemode' => true,
                ]
            ),
            new rb_filter_option(
                'track',
                'description',
                get_string('track_description', 'mod_perform'),
                'text'
            ),

        // $filteroptions[] = new rb_filter_option(
        //     'subject_instance',
        //     'subject_date_completion',
        // get_string('date_completion', 'rb_source_perform_subject_instance'),
        // 'date'
        // );

        ];

        $this->add_fields_to_filters($filteroptions);
        $this->add_core_user_filters($filteroptions);
        //$this->add_totara_job_filters($filteroptions, 'base', 'job_assignment_id');

        return $filteroptions;
    }

    /**
     * Define the default columns for this report.
     *
     * @return array
     */
    protected function define_defaultcolumns() {
        return self::get_default_columns();
    }

    /**
     * Define the default filters for this report.
     *
     * @return array
     */
    protected function define_defaultfilters() {
        return self::get_default_filters();
    }

    /**
     * The default columns for this and embedded reports.
     *
     * @return array
     */
    public static function get_default_columns() {
        return [
            [
                'type' => 'user',
                'value' => 'namelink',
                'heading' => get_string('subject_name', 'rb_source_perform_subject_instance')
            ],
            [
                'type' => 'perform',
                'value' => 'name',
                'heading' => get_string('activity_name', 'mod_perform')
            ],
            [
                'type' => 'perform',
                'value' => 'type',
                'heading' => get_string('activity_type', 'mod_perform')
            ],
            [
                'type' => 'track',
                'value' => 'description',
                'heading' => get_string('track_description', 'mod_perform')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'created_at',
                'heading' => get_string('date_created', 'rb_source_perform_subject_instance')
            ],
            [
                'type' => 'subject_instance',
                'value' => 'subject_status',
                'heading' => get_string('subject_instance_status', 'mod_perform')
            ],
            [
                'type' => 'participant_instance',
                'value' => 'count',
                'heading' => get_string('participant_count', 'rb_source_perform_subject_instance')
            ],
            // [
            //     'type' => 'subject_instance',
            //     'value' => 'subject_date_completion',
            //     'heading' => get_string('date_completion', 'rb_source_perform_subject_instance')
            // ],
            // [
            //     'type' => 'subject_instance',
            //     'value' => 'instance_count',
            //     'heading' => get_string('instance_count', 'rb_source_perform_subject_instance')
            // ]
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
                'type' => 'user',
                'value' => 'namelink',
            ],
            [
                'type' => 'track',
                'value' => 'description',
            ],
            [
                'type' => 'subject_instance',
                'value' => 'created_at',
            ],
            [
                'type' => 'perform',
                'value' => 'type'
            ],
            // [
            //     'type' => 'subject_instance',
            //     'value' => 'subject_status'
            // ],
            // [
            //     'type' => 'subject_instance',
            //     'value' => 'subject_date_completion'
            // ],
        ];
    }

    /**
     * Define the available content options for this report.
     *
     * @return array
     */
    protected function define_contentoptions() {
        $contentoptions = [];

        // Add the manager/position/organisation content options.
        $this->add_basic_user_content_options($contentoptions);

        return $contentoptions;
    }

    /**
     * Define the available param options for this report.
     *
     * @return array
     */
    protected function define_paramoptions() {
        $paramoptions = [
            new rb_param_option(
                'activity_id',
                'track.activity_id',
                'track'
            )
        ];
        return $paramoptions;
    }

    /**
     * Disable subject reports if the performance activities feature is disabled.
     *
     * @return bool
     */
    public static function is_source_ignored() {
        return advanced_feature::is_disabled('performance_activities');
    }

    /**
     * Inject column_test data into database.
     *
     * @param totara_reportbuilder_column_testcase $testcase
     */
    public function phpunit_column_test_add_data(totara_reportbuilder_column_testcase $testcase) {
        global $CFG;

        if (!PHPUNIT_TEST) {
            throw new coding_exception('phpunit_column_test_add_data() cannot be used outside of unit tests');
        }

        require_once($CFG->dirroot.'/lib/testing/generator/component_generator_base.php');
        require_once($CFG->dirroot.'/lib/testing/generator/data_generator.php');
        require_once(__DIR__ . '/../tests/generator/mod_perform_generator.class.php');

        $si = (new \mod_perform_generator(new testing_data_generator()))->create_subject_instance([
            'activity_name' => 'Weekly catchup',
            'subject_is_participating' => true,
            'subject_user_id' => \core\entities\user::repository()->get()->last()->id,
            'other_participant_id' => \core\entities\user::logged_in()->id,
        ]);
    }
}