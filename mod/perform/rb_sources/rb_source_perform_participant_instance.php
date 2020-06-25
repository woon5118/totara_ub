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
use mod_perform\state\participant_instance\closed;
use mod_perform\state\participant_instance\complete;
use mod_perform\state\participant_instance\participant_instance_availability;
use mod_perform\state\participant_instance\participant_instance_progress;
use mod_perform\state\state_helper;
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
            new rb_join(
                'core_relationship',
                'LEFT',
                "{totara_core_relationship}",
                'core_relationship.id = base.core_relationship_id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            new rb_join(
                'core_relationship_resolver',
                'LEFT',
                "{totara_core_relationship_resolver}",
                'core_relationship_resolver.relationship_id = core_relationship.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                ['core_relationship']
            ),
        );

        $this->add_to_joinlist($joinlist, 'subject_instance');
        $this->add_core_user_tables($joinlist, 'base', 'participant_id');
        $this->add_core_user_tables($joinlist, 'subject_instance', 'subject_user_id', 'subject_user');

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
                get_string('progress', 'mod_perform'),
                'base.progress',
                [
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'state_display_name',
                    'extracontext' => [
                        'object_type' => 'participant_instance',
                        'state_type' => participant_instance_progress::get_type(),
                    ],
                ]
            ),
            new rb_column_option(
                'participant_instance',
                'participant_availability',
                get_string('availability', 'mod_perform'),
                'base.availability',
                [
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'state_display_name',
                    'extracontext' => [
                        'object_type' => 'participant_instance',
                        'state_type' => participant_instance_availability::get_type(),
                    ],
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
                'core_relationship',
                'class_name',
                get_string('relationship_in_activity', 'rb_source_perform_participant_instance'),
                'core_relationship_resolver.class_name',
                [
                    'joins' => ['core_relationship_resolver'],
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                    'displayfunc' => 'core_relationship',
                ]
            ),
            new rb_column_option(
                'core_relationship',
                'core_relationship_id',
                get_string('core_relationship_id', 'rb_source_perform_participant_instance'),
                'base.core_relationship_id',
                [
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'integer',
                    'outputformat' => 'integer',
                    'hidden' => 1,
                    'noexport' => true
                ]
            ),
            new rb_column_option(
                'participant_instance',
                'overdue',
                get_string('overdue', 'mod_perform'),
                "CASE 
                    WHEN
                        subject_instance.due_date <= " . time() . "
                        AND NOT (
                            base.progress = " . complete::get_code() . "
                            OR base.availability = " . closed::get_code() . "
                        )
                    THEN 1
                    ELSE 0
                END",
                [
                    'joins' => 'subject_instance',
                    'dbdatatype' => 'boolean',
                    'displayfunc' => 'yes_or_no',
                ]
            ),
        ];

        $this->add_fields_to_columns($columnoptions, 'subject_instance');
        $this->add_core_user_columns($columnoptions);
        // ATTENTION: Remove Subject of the activity rb_column_option first
        // $this->add_core_user_columns($columnoptions, 'subject_user', 'activity_subject');

        return $columnoptions;
    }

    /**
     * Define the filter options available for this report.
     *
     * @return array
     */
    protected function define_filteroptions() {
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
            // Progress of participant instance
            new rb_filter_option(
                'participant_instance',
                'participant_progress',
                get_string('progress', 'mod_perform'),
                'select',
                [
                    'selectchoices' => state_helper::get_all_display_names(
                        'participant_instance', participant_instance_progress::get_type()
                    ),
                ]
            ),
            // Availability of participant instance
            new rb_filter_option(
                'participant_instance',
                'participant_availability',
                get_string('availability', 'mod_perform'),
                'select',
                [
                    'selectchoices' => state_helper::get_all_display_names(
                        'participant_instance', participant_instance_availability::get_type()
                    ),
                    'simplemode' => true
                ]
            ),
            // Overdue status
            new rb_filter_option(
                'participant_instance',
                'overdue',
                get_string('overdue', 'mod_perform'),
                'multicheck',
                [
                    'simplemode' => true,
                    'selectfunc' => 'yesno_list',
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
                'core_relationship',
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
                'core_relationship',
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
            // Activity type
            [
                'type' => 'perform',
                'value' => 'type',
                'heading' => get_string('activity_type', 'mod_perform'),
            ],
            // Date instance created
            [
                'type' => 'participant_instance',
                'value' => 'created_at',
                'heading' => get_string('date_created', 'rb_source_perform_participant_instance'),
            ],
            // Relationship in activity
            [
                'type' => 'core_relationship',
                'value' => 'class_name',
                'heading' => get_string('relationship_in_activity', 'rb_source_perform_participant_instance'),
            ],
            // Subject of the activity
            [
                'type' => 'subject_instance',
                'value' => 'activity_subject',
                'heading' => get_string('activity_subject', 'rb_source_perform_participant_instance'),
            ],
            // Progress of participant instance
            [
                'type' => 'participant_instance',
                'value' => 'participant_progress',
                'heading' => get_string('progress', 'mod_perform'),
            ],
            // Availability of participant instance
            [
                'type' => 'participant_instance',
                'value' => 'participant_availability',
                'heading' => get_string('availability', 'mod_perform'),
            ],
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
            ],
            [
                'type' => 'participant_instance',
                'value' => 'participant_progress',
            ],
            [
                'type' => 'participant_instance',
                'value' => 'participant_availability',
            ],
        ];
        if (self::get_resolvers()) {
            $default_filters[] = [
                'type' => 'core_relationship',
                'value' => 'core_relationship_id',
            ];
        } else {
            $default_filters[] = [
                'type' => 'core_relationship',
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
                  FROM {totara_core_relationship_resolver} tcrr";
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