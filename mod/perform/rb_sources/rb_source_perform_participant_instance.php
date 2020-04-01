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
use totara_core\advanced_feature;
use totara_job\rb\source\report_trait;

/**
 * Performance participant instance report.
 *
 * Class rb_source_perform_participant_instance
 */
class rb_source_perform_participant_instance extends rb_base_source {
    use report_trait;
    use participant_subject_instance_source;

    private $resolvers = null;
    /**
     * Constructor.
     *
     * @param mixed $groupid
     * @param rb_global_restriction_set|null $globalrestrictionset
     */
    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('base', 'user_id');

        $this->resolvers = self::get_resolvers();

        $this->sourcetitle = get_string('sourcetitle', 'rb_source_perform_participant_instance');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_perform_participant_instance');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_perform_participant_instance');

        $this->base = '{perform_participant_instance}';
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
        $joinlist = array(
            new rb_join(
                'subject_instance',
                'INNER',
                "{perform_subject_instance}",
                "subject_instance.id = base.subject_instance_id",
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            // new rb_join(
            //     'subject_instance_user',
            //     'INNER',
            //     "{perform_subject_instance}",
            //     "subject_instance_user.subject_user_id = auser.id",
            //     REPORT_BUILDER_RELATION_ONE_TO_ONE,
            //     ['subject_instance_user', 'auser']
            // ),
            new rb_join(
                'perform_relationship',
                'LEFT',
                "{perform_relationship}",
                "perform_relationship.id = base.activity_relationship_id",
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            // new rb_join(
            //     'totara_relationship',
            //     'INNER',
            //     'relationship',
            //     "{totara_core_relationship}",
            //     "totara_relationship.id = perform_relationship.relationship_id",
            //     REPORT_BUILDER_RELATION_ONE_TO_ONE,
            //     'perform_relationship'
            // ),
            // new rb_join(
            //     'totara_relationship_resolver',
            //     'INNER',
            //     "{totara_core_relationship_resolver}",
            //     'totara_relationship_resolver.relationship_id = totara_relationship.id',
            //     REPORT_BUILDER_RELATION_ONE_TO_ONE,
            //     ['totara_relationship', 'perform_relationship']
            // ),
            new rb_join(
                'totara_relationship_resolver',
                'LEFT',
                "{totara_core_relationship_resolver}",
                'totara_relationship_resolver.relationship_id = perform_relationship.core_relationship_id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                ['perform_relationship']
            ),

        );

        $this->add_to_joinlist($joinlist, 'subject_instance');
        $this->add_core_user_tables($joinlist, 'base', 'participant_id');
        $this->add_core_user_tables($joinlist, 'subject_instance', 'subject_user_id', 'subject_user');
        //        $this->add_totara_job_tables($joinlist, 'subject_instance', 'subject_user_id');

        return $joinlist;
    }

    /**
     * Define the column options available for this report.
     *
     * @return array
     */
    protected function define_columnoptions() {
        global $DB;
        $usednamefields = totara_get_all_user_name_fields_join('subject_user', null, true);
        $allnamefields = totara_get_all_user_name_fields_join('subject_user');

        $columnoptions = [
            new rb_column_option(
                'participant_instance',
                'participant_progress',
                get_string('participant_status', 'rb_source_perform_participant_instance'),
                'base.progress',
                [
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'participant_progress'
                ]
            ),
            new rb_column_option(
                'participant_instance',
                'created_at',
                get_string('date_created', 'rb_source_perform_participant_instance'),
                'base.created_at',
                [
                    'dbdatatype' => 'timestamp',
                    'displayfunc' => 'nice_date'
                ]
            ),
            // Subject of the activity
            new rb_column_option(
                'subject_instance',
                'activity_subject',
                get_string('activity_subject', 'rb_source_perform_participant_instance'),
                "CASE WHEN subject_user.id IS NULL THEN NULL ELSE " . $DB->sql_concat_join("' '", $usednamefields) . " END",
                [
                    'joins' => 'subject_user',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                    'extrafields' => array_merge(['id' => 'subject_user.id'], $allnamefields),
                    'displayfunc' => 'user',
                ]
            ),
            new rb_column_option(
                'perform_relationship',
                'class_name',
                get_string('relationship_in_activity', 'rb_source_perform_participant_instance'),
                'totara_relationship_resolver.class_name',
                [
                    //'joins' => ['totara_relationship_resolver', 'totara_relationship', 'perform_relationship'],
                    'joins' => ['totara_relationship_resolver', 'perform_relationship'],
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                    'displayfunc' => 'perform_relationship',
                ]
            ),
            new rb_column_option(
                'perform_relationship',
                'core_relationship_id',
                get_string('relationship_id', 'rb_source_perform_participant_instance'),
                'totara_relationship_resolver.relationship_id',
                [
                    'joins' => ['totara_relationship_resolver', 'perform_relationship'],
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'integer',
                    'outputformat' => 'integer',
                    'hidden' => 1,
                    'noexport' => true
                ]
            )

            // new rb_column_option(
            //     'perform',
            //     'type_name',
            //     get_string('activity_type', 'mod_perform'),
            //     'perform_type.name',
            //     [
            //         'joins' => ['perform_type', 'perform', 'track', 'track_user_assignment'],
            //         'dbdatatype' => 'text',
            //         'outputformat' => 'text',
            //         'displayfunc' => 'format_string'
            //     ]
            // ),
            // new rb_column_option(
            //     'participant_instance',
            //     'participant_date_completion',
            //     get_string('date_completion', 'rb_source_perform_subject_instance'),
            //     'base.???',
            //     [
            //         'dbdatatype' => 'timestamp',
            //         'displayfunc' => 'nice_date'
            //     ]
            // ),
        ];

        $this->add_fields_to_columns($columnoptions, 'subject_instance');
        $this->add_core_user_columns($columnoptions);

        // ATTENTION: Remove Subject of the activity rb_column_option first
        // $this->add_core_user_columns($columnoptions, 'subject_user', 'activity_subject');

        // $this->add_totara_job_columns($columnoptions);

        return $columnoptions;
    }

    /**
     * Define the filter options available for this report.
     *
     * @return array
     */
    protected function define_filteroptions() {
        $options = [
            '20' => get_string('user_activities_status_complete', 'mod_perform'),
            '10' => get_string('user_activities_status_in_progress', 'mod_perform'),
            '0' => get_string('user_activities_status_not_started', 'mod_perform'),
        ];
        $filteroptions = [
            // Participant name
            new rb_filter_option(
                'user',
                'namelink',
                get_string('participant_name', 'rb_source_perform_participant_instance'),
                'text'
            ),
            // Date instance created
            new rb_filter_option(
                'participant_instance',
                'created_at',
                get_string('date_created', 'rb_source_perform_participant_instance'),
                'date'
            ),
            // Status of participant instance
            new rb_filter_option(
                'participant_instance',
                'participant_progress',
                get_string('participant_status', 'rb_source_perform_participant_instance'),
                'select',
                [
                    'selectchoices' => $options,
                    'simplemode' => true
                ]
            ),
        ];
        unset($options);

        // Relationship in activity
        $options = [];
        foreach ($this->resolvers as $resolver) {
            $options[$resolver->relationship_id] = $resolver->class_name::get_name();
        }
        if ($this->resolvers) {
            $filteroptions[] = new rb_filter_option(
                'perform_relationship',
                'core_relationship_id',
                get_string('relationship_in_activity', 'rb_source_perform_participant_instance'),
                'select',
                [
                    'selectchoices' => $options,
                    'simplemode' => true,
                ]
            );
        } else {
            $filteroptions[] = new rb_filter_option(
                'perform_relationship',
                'class_name',
                get_string('relationship_in_activity', 'rb_source_perform_participant_instance'),
                'text'
            );
        }
        unset($options);

        // Subject of the activity
        $filteroptions[] = new rb_filter_option(
            'subject_instance',
            'activity_subject',
            get_string('activity_subject', 'rb_source_perform_participant_instance'),
            'text'
        );

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
            // Participant name
            [
                'type' => 'user',
                'value' => 'namelink',
                'heading' => get_string('participant_name', 'rb_source_perform_participant_instance'),
            ],
            // Performance activity name
            [
                'type' => 'perform',
                'value' => 'name',
                'heading' => get_string('activity_name', 'mod_perform'),
            ],

            // TODO: Activity type
            // [
            //     'type' => 'perform',
            //     'value' => 'type',
            //     'heading' => 'Activity type',
            // ],

            // Date instance created
            [
                'type' => 'participant_instance',
                'value' => 'created_at',
                'heading' => get_string('date_created', 'rb_source_perform_participant_instance'),
            ],
            // Relationship in activity
            [
                'type' => 'perform_relationship',
                'value' => 'class_name',
                'heading' => get_string('relationship_in_activity', 'rb_source_perform_participant_instance'),
            ],
            // Subject of the activity
            [
                'type' => 'subject_instance',
                'value' => 'activity_subject',
                'heading' => get_string('activity_subject', 'rb_source_perform_participant_instance'),
            ],
            // Status of participant instance
            [
                'type' => 'participant_instance',
                'value' => 'participant_progress',
                'heading' => get_string('participant_status', 'rb_source_perform_participant_instance'),
            ],
            // TODO: Date participant instance completed
            // [
            //     'type' => 'participant_instance',
            //     'value' => 'completed_at',
            //     'heading' => 'Date participant instance completed',
            // ],
        ];
    }

    /**
     * The default filters for this and embedded reports.
     *
     * @return array
     */
    public static function get_default_filters() {
        $default_filters = [
            [
                'type' => 'user',
                'value' => 'namelink',
            ],
            [
                'type' => 'participant_instance',
                'value' => 'created_at',
            ],
            [
                'type' => 'subject_instance',
                'value' => 'activity_subject',
            ]
        ];
        if (self::get_resolvers()) {
            $default_filters[] = [
                'type' => 'perform_relationship',
                'value' => 'core_relationship_id',
            ];
        } else {
            $default_filters[] = [
                'type' => 'perform_relationship',
                'value' => 'class_name',
            ];
        }
        return $default_filters;
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
                'subject_instance_id',
                'base.subject_instance_id'
            )
        ];
        return $paramoptions;
    }

    /**
     * Disable participant reports if the performance activities feature is disabled.
     *
     * @return bool
     */
    public static function is_source_ignored() {
        return advanced_feature::is_disabled('performance_activities');
    }

    /**
     * Get relationship resolvers.
     *
     * @return array
     * @throws dml_exception
     */
    private static function get_resolvers() {
        global $DB;
        $sql = "SELECT tcrr.class_name, tcrr.relationship_id
                  FROM {totara_core_relationship_resolver} tcrr
                 WHERE tcrr.relationship_id IN (
                        SELECT DISTINCT pr2.core_relationship_id
                          FROM {perform_relationship} pr2
                        )";
        return $DB->get_records_sql($sql);
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
        ]);
    }
}