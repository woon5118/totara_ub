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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use totara_competency\entities\assignment;
use totara_competency\user_groups;
use totara_competency\entities\competency_achievement;
use totara_core\advanced_feature;
use totara_job\rb\source\report_trait;

defined('MOODLE_INTERNAL') || die();

class rb_source_competency_status extends rb_base_source {
    use report_trait;

    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('base', 'user_id');

        $this->base = '{totara_competency_achievement}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_competency_status');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_competency_status');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_competency_status');
        $this->usedcomponents[] = 'totara_plan';
        $this->usedcomponents[] = 'totara_hierarchy';
        $this->usedcomponents[] = 'totara_competency';

        parent::__construct();
    }

    /**
     * Global report restrictions are implemented in this source.
     * @return boolean
     */
    public function global_restrictions_supported() {
        return true;
    }

    public static function is_source_ignored() {
        return !advanced_feature::is_enabled('competencies');
    }

    //
    //
    // Methods for defining contents of source
    //
    //

    protected function define_joinlist() {

        $joinlist = array(
            new rb_join(
                'competency',
                'LEFT',
                '{comp}',
                'competency.id = base.comp_id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            new rb_join(
                'scale_values',
                'LEFT',
                '{comp_scale_values}',
                'scale_values.id = base.scale_value_id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            new rb_join(
                'assignment',
                'INNER',
                "{totara_competency_assignments}",
                "base.assignment_id = assignment.id",
                REPORT_BUILDER_RELATION_MANY_TO_ONE
            ),
            new rb_join(
                'assignment_cohorts',
                'LEFT',
                "{cohort}",
                "assignment.user_group_type = '".user_groups::COHORT."' AND assignment.user_group_id = assignment_cohorts.id",
                REPORT_BUILDER_RELATION_ONE_TO_MANY,
                'assignment'
            ),
            new rb_join(
                'assignment_positions',
                'LEFT',
                "{pos}",
                "assignment.user_group_type = '".user_groups::POSITION."' AND assignment.user_group_id = assignment_positions.id",
                REPORT_BUILDER_RELATION_ONE_TO_MANY,
                'assignment'
            ),
            new rb_join(
                'pos_type',
                'LEFT',
                '{pos_type}',
                'assignment_positions.typeid = pos_type.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'assignment_positions'
            ),
            new rb_join(
                'assignment_organisations',
                'LEFT',
                "{org}",
                "assignment.user_group_type = '".user_groups::ORGANISATION."' AND assignment.user_group_id = assignment_organisations.id",
                REPORT_BUILDER_RELATION_ONE_TO_MANY,
                'assignment'
            ),
            new rb_join(
                'org_type',
                'LEFT',
                '{org_type}',
                'assignment_organisations.typeid = org_type.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'assignment_organisations'
            ),
        );

        // include some standard joins
        $this->add_core_user_tables($joinlist, 'base', 'user_id');
        $this->add_totara_job_tables($joinlist, 'base', 'user_id');

        return $joinlist;
    }

    protected function define_columnoptions() {

        $columnoptions = array(
            new rb_column_option(
                'competency_status',  // Type.
                'scale_value_name',          // Value.
                get_string('scalevalue', 'rb_source_competency_status'), // Name.
                'scale_values.name',    // Field.
                [
                    'joins' => 'scale_values',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                    'displayfunc' => 'format_string'
                ] // Options.
            ),
            new rb_column_option(
                'competency_status',
                'time_proficient',
                get_string('proficientdate', 'rb_source_competency_status'),
                'base.time_proficient',
                [
                    'displayfunc' => 'nice_date',
                    'dbdatatype' => 'timestamp'
                ]
            ),
            new rb_column_option(
                'competency',
                'fullname',
                get_string('competencyname', 'rb_source_competency_status'),
                'competency.fullname',
                [
                    'joins' => 'competency',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                    'displayfunc' => 'format_string'
                ]
            ),
            new rb_column_option(
                'competency',
                'idnumber',
                get_string('competencyidnumber', 'rb_source_competency_status'),
                'competency.idnumber',
                [
                    'joins' => 'competency',
                    'displayfunc' => 'plaintext',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text'
                ]
            ),
            new rb_column_option(
                'competency',
                'id',
                get_string('competencyid', 'rb_source_competency_status'),
                'base.comp_id',
                [
                    'displayfunc' => 'integer'
                ]
            ),
        );

        if (advanced_feature::is_enabled('competency_assignment')) {
            $columnoptions = array_merge($columnoptions, [
                new rb_column_option(
                    'assignment',
                    'assignment_type',
                    get_string('label:assignment_type', 'rb_source_competency_assignment_users'),
                    "(
                        CASE WHEN type = '".assignment::TYPE_ADMIN."' AND user_group_type <> '".user_groups::USER."'
                        THEN user_group_type
                        ELSE type END
                    )",
                    [
                        'joins' => 'assignment',
                        'displayfunc' => 'display_assignment_type',
                    ]
                ),
                new rb_column_option(
                    'assignment',
                    'user_group',
                    get_string('label:user_group', 'rb_source_competency_assignment_users'),
                    'assignment.user_group_type',
                    [
                        'joins' => ['assignment_cohorts', 'assignment_positions', 'assignment_organisations', 'auser'],
                        'displayfunc' => 'display_user_group',
                        'extrafields' => [
                            'user_group_type' => 'assignment.user_group_type',
                            'user_id' => 'auser.id',
                            'user_firstname' => 'auser.firstname',
                            'user_lastname' => 'auser.lastname',
                            'user_firstnamephonetic' => 'auser.firstnamephonetic',
                            'user_lastnamephonetic' => 'auser.lastnamephonetic',
                            'user_middlename' => 'auser.middlename',
                            'user_alternatename' => 'auser.alternatename',
                            'user_idnumber' => 'auser.idnumber',
                            'pos_id' => 'assignment_positions.id',
                            'pos_name' => 'assignment_positions.fullname',
                            'pos_idnumber' => 'assignment_positions.idnumber',
                            'org_id' => 'assignment_organisations.id',
                            'org_name' => 'assignment_organisations.fullname',
                            'org_idnumber' => 'assignment_organisations.idnumber',
                            'coh_id' => 'assignment_cohorts.id',
                            'coh_name' => 'assignment_cohorts.name',
                            'coh_idnumber' => 'assignment_cohorts.idnumber',
                        ],
                    ]
                ),
                new rb_column_option(
                    'competency_status',
                    'status',
                    'Assignment status',
                    'base.status',
                    [
                        'displayfunc' => 'comp_record_status',
                    ]
                ),
            ]);
        }

        // include some standard columns
        $this->add_core_user_columns($columnoptions);
        $this->add_totara_job_columns($columnoptions);

        return $columnoptions;
    }

    protected function define_filteroptions() {
        $filteroptions = array(
            new rb_filter_option(
                'competency_status',  // type
                'time_proficient',        // value
                get_string('proficientdate', 'rb_source_competency_status'),       // label
                'date',                 // filtertype
                []                 // options
            ),
            new rb_filter_option(
                'competency',
                'fullname',
                get_string('competencyname', 'rb_source_competency_status'),
                'text'
            ),
            new rb_filter_option(
                'competency',
                'idnumber',
                get_string('competencyid', 'rb_source_competency_status'),
                'text'
            ),
        );
        // include some standard filters
        $this->add_core_user_filters($filteroptions);
        $this->add_totara_job_filters($filteroptions, 'base', 'user_id');

        return $filteroptions;
    }

    protected function define_contentoptions() {
        $contentoptions = array();

        // Add the manager/position/organisation content options.
        $this->add_basic_user_content_options($contentoptions);

        return $contentoptions;
    }

    protected function define_paramoptions() {
        $paramoptions = array(
            new rb_param_option(
                'userid',       // parameter name
                'base.user_id',  // field
                null            // joins
            ),
            new rb_param_option(
                'compid',
                'base.comp_id'
            ),
        );

        return $paramoptions;
    }

    protected function define_defaultcolumns() {
        $defaultcolumns = array(
            [
                'type'  => 'user',
                'value' => 'namelink'
            ],
            [
                'type'  => 'competency_status',
                'value' => 'scale_value_name',
            ],
        );
        return $defaultcolumns;
    }

    protected function define_defaultfilters() {
        $defaultfilters = array(
            [
                'type' => 'user',
                'value' => 'fullname',
                'advanced' => 0,
            ],
            [
                'type' => 'job_assignment',
                'value' => 'allorganisations',
                'advanced' => 1,
            ],
            [
                'type' => 'competency',
                'value' => 'fullname',
                'advanced' => 1,
            ],
        );
        return $defaultfilters;
    }

    //
    //
    // Source specific column display methods
    //
    //


    //
    //
    // Source specific filter display methods
    //
    //

    public function rb_filter_proficiency_list() {
        global $DB;

        $values = $DB->get_records_menu('comp_scale_values', null, 'scaleid, sortorder', 'id, name');

        $scales = [];
        foreach ($values as $index => $value) {
            $scales[$index] = format_string($value);
        }

        // include all possible scale values (from every scale)
        return $scales;
    }

    /**
     * Inject column_test data into database.
     * @param totara_reportbuilder_column_testcase $testcase
     */
    public function phpunit_column_test_add_data(totara_reportbuilder_column_testcase $testcase) {
        if (!PHPUNIT_TEST) {
            throw new coding_exception('phpunit_column_test_add_data() cannot be used outside of unit tests');
        }

        $now = time();

        $assignment = new assignment();
        $assignment->type = assignment::TYPE_OTHER;
        $assignment->competency_id = 100;
        $assignment->user_group_type = 'position';
        $assignment->user_group_id = 201;
        $assignment->optional = false;
        $assignment->created_by = 301;
        $assignment->created_at = $now;
        $assignment->updated_at = $now;
        $assignment->save();

        $achievement = new competency_achievement();
        $achievement->comp_id = 100;
        $achievement->user_id = 200;
        $achievement->assignment_id = $assignment->id;
        $achievement->scale_value_id = 400;
        $achievement->proficient = 1;
        $achievement->status = competency_achievement::ACTIVE_ASSIGNMENT;
        $achievement->time_created = $now;
        $achievement->time_proficient = $now;
        $achievement->time_scale_value = $now;
        $achievement->time_status = $now;
        $achievement->last_aggregated = $now;
        $achievement->save();
    }

}

