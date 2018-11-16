<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author David Curry <david.curry@totaralms.com>
 * @package totara
 * @subpackage reportbuilder
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/totara/certification/lib.php');

class rb_source_certification_overview extends rb_base_source {
    use \core_course\rb\source\report_trait;
    use \totara_job\rb\source\report_trait;
    use \totara_certification\rb\source\certification_trait;

    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('base', 'userid');

        $this->base = '{prog_completion}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->requiredcolumns = $this->define_requiredcolumns();
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_certification_overview');
        $this->sourcewhere = $this->define_sourcewhere();
        $this->sourcejoins = $this->get_source_joins();
        $this->usedcomponents[] = 'totara_certification';
        $this->usedcomponents[] = 'totara_program';
        $this->usedcomponents[] = 'totara_cohort';

        $this->cacheable = false;

        // Add custom fields.
        $this->add_totara_customfield_component(
            'prog', 'certif', 'programid',
            $this->joinlist, $this->columnoptions, $this->filteroptions
        );

        parent::__construct();
    }

    /**
     * Hide this source if feature disabled or hidden.
     * @return bool
     */
    public function is_ignored() {
        return !totara_feature_visible('certifications');
    }

    /**
     * Global report restrictions are implemented in this source.
     * @return boolean
     */
    public function global_restrictions_supported() {
        return true;
    }


    protected function define_sourcewhere() {
        // Only consider whole certifications - not courseset completion.
        $sourcewhere = 'base.coursesetid = 0';

        return $sourcewhere;
    }

    protected function get_source_joins() {
        // This enforces the INNER join defined for 'certif' and filters out all non-certification stuff in prog table.
        return array('certif');
    }

    protected function define_joinlist() {
        global $CFG;

        $joinlist = array();

        $this->add_totara_certification_tables($joinlist, 'base', 'programid');

        $this->add_core_user_tables($joinlist, 'base', 'userid');
        $this->add_totara_job_tables($joinlist, 'base', 'userid');
        $this->add_core_course_category_tables($joinlist, 'course', 'category');

        /* Psuedo Explaination:
 *
 * if (window is open) {
 *      Use current record
 * } else {
 *      if (history record exists) {
 *          use history certifpath
 *      } else {
 *          default to certif
 *      }
 * }
 */
        $now = time();
        $path = CERTIFPATH_CERT;
        $joinlist[] = new rb_join(
            'prog_courseset',
            'INNER',
            '{prog_courseset}',
            "prog_courseset.programid = base.programid
            AND base.coursesetid = 0
            AND (
                   (certif_completion.timewindowopens < {$now} AND prog_courseset.certifpath = certif_completion.certifpath)
                OR (certif_completion.timewindowopens > {$now} AND history.certifpath IS NOT NULL AND prog_courseset.certifpath = history.certifpath)
                OR (certif_completion.timewindowopens > {$now} AND history.certifpath IS NULL AND prog_courseset.certifpath = {$path})
            )
            ",
            REPORT_BUILDER_RELATION_ONE_TO_MANY,
            array('base', 'certif_completion', 'history')
        );

        $joinlist[] = new rb_join(
            'prog_completion',
            'LEFT',
            '{prog_completion}',
            "prog_completion.programid = base.programid AND prog_completion.userid = base.userid AND prog_courseset.id = prog_completion.coursesetid",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'prog_courseset'
        );

        $joinlist[] = new rb_join(
            'prog_courseset_course',
            'INNER',
            '{prog_courseset_course}',
            "prog_courseset_course.coursesetid = prog_courseset.id",
            REPORT_BUILDER_RELATION_ONE_TO_MANY,
            'prog_courseset'
        );

        $joinlist[] = new rb_join(
            'course',
            'INNER',
            '{course} ', // Intentional space to stop report builder adding unwanted custom course fields.
            "prog_courseset_course.courseid = course.id",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'prog_courseset_course'
        );

        $joinlist[] = new rb_join(
            'course_completions',
            'LEFT',
            '{course_completions}',
            "course_completions.course = course.id AND course_completions.userid = base.userid",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'course'
        );

        $joinlist[] = new rb_join(
            'grade_items',
            'LEFT',
            '{grade_items}',
            "grade_items.itemtype = 'course' AND grade_items.courseid = course.id",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'course'
        );

        $joinlist[] = new rb_join(
            'grade_grades',
            'LEFT',
            '{grade_grades}',
            "grade_grades.itemid = grade_items.id AND grade_grades.userid = base.userid",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'grade_items'
        );

        require_once($CFG->dirroot . '/completion/criteria/completion_criteria.php');

        $joinlist[] = new rb_join(
            'criteria',
            'LEFT',
            '{course_completion_criteria}',
            "criteria.course = prog_courseset_course.courseid AND criteria.criteriatype = " . COMPLETION_CRITERIA_TYPE_GRADE,
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'prog_courseset_course'
        );

        $joinlist[] = new rb_join(
            'cplorganisation',
            'LEFT',
            '{org}',
            "cplorganisation.id = base.organisationid",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'base'
        );

        $joinlist[] = new rb_join(
            'cplorganisation_type',
            'LEFT',
            '{org}',
            "cplorganisation_type.id = cplorganisation.typeid",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'cplorganisation'
        );

        $joinlist[] = new rb_join(
            'cplposition',
            'LEFT',
            '{pos}',
            "cplposition.id = base.positionid",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'base'
        );

        $joinlist[] = new rb_join(
            'cplposition_type',
            'LEFT',
            '{pos_type}',
            "cplposition_type.id = cplposition.typeid",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'cplposition'
        );

        $joinlist[] = new rb_join(
            'certif_completion',
            'INNER',
            '{certif_completion}',
            "certif_completion.userid = base.userid AND certif_completion.certifid = certif.certifid",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'certif'
        );

        $joinlist[] = new rb_join(
            'history',
            'LEFT',
            '{certif_completion_history}',
            "certif_completion.userid = history.userid
             AND certif_completion.certifid = history.certifid
             AND history.timecompleted = (SELECT MAX(timecompleted)
                                            FROM {certif_completion_history} cch
                                           WHERE cch.userid = history.userid
                                             AND cch.certifid = history.certifid)",
            REPORT_BUILDER_RELATION_ONE_TO_ONE,
            'certif_completion'
        );

        return $joinlist;
    }

    protected function define_columnoptions() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/completion/completion_completion.php');

        $columnoptions = array();

        // Include some standard columns.
        $this->add_totara_certification_columns($columnoptions, 'certif');
        $this->add_core_user_columns($columnoptions);
        $this->add_totara_job_columns($columnoptions);

        // Programe completion cols.
        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'timedue',
            get_string('duedate', 'rb_source_program_overview'),
            'base.timedue',
            array(
                'joins' => 'base',
                'displayfunc' => 'nice_date',
                'dbdatatype' => 'timestamp',
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'timestarted',
            get_string('datestarted', 'rb_source_program_overview'),
            'base.timestarted',
            array(
                'joins' => 'base',
                'displayfunc' => 'nice_date',
                'dbdatatype' => 'timestamp',
                'extrafields' => array('prog_id' => 'certif.id')
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'timeassigned',
            get_string('dateassigned', 'rb_source_program_overview'),
            'base.timecreated',
            array(
                'joins' => 'base',
                'displayfunc' => 'nice_date',
                'dbdatatype' => 'timestamp',
                'extrafields' => array('prog_id' => 'certif.id')
            )
        );

        // Organisation Cols.
        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'orgshortname',
            get_string('completionorganisationshortname', 'rb_source_program_overview'),
            'cplorganisation.shortname',
            array(
                'joins' => 'cplorganisation',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'displayfunc' => 'plaintext'
            )

        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'orgfullname',
            get_string('completionorganisationfullname', 'rb_source_program_overview'),
            'cplorganisation.fullname',
            array(
                'joins' => 'cplorganisation',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'displayfunc' => 'format_string'
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'orgtype',
            get_string('completionorganisationtype', 'rb_source_program_overview'),
            'cplorganisation_type.fullname',
            array(
                'joins' => 'cplorganisation_type',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'displayfunc' => 'format_string'
            )
        );

        // Completion Position Cols.
        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'fullname',
            get_string('completionpositionfullname', 'rb_source_program_overview'),
            'cplposition.fullname',
            array(
                'joins' => 'cplposition',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'displayfunc' => 'format_string'
            )

        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'type',
            get_string('completionpositiontype', 'rb_source_program_overview'),
            'cplposition_type.fullname',
            array(
                'joins' => 'cplposition_type',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'displayfunc' => 'format_string'
            )

        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'idnumber',
            get_string('completionpositionidnumber', 'rb_source_program_overview'),
            'cplposition.idnumber',
            array(
                'joins' => 'cplposition',
                'displayfunc' => 'plaintext',
                'dbdatatype' => 'char',
                'outputformat' => 'text'
            )

        );

        $columnoptions[] = new rb_column_option(
            'course',
            'timeenrolled',
            get_string('coursecompletiontimeenrolled', 'rb_source_program_overview'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition($DB->sql_cast_2char('course_completions.timeenrolled'), 'certif'),
            array(
                'joins' => ['course_completions'],
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_newline_date',
                'style' => array('white-space' => 'pre'),
            )
        );

        $columnoptions[] = new rb_column_option(
            'course',
            'timestarted',
            get_string('coursecompletiontimestarted', 'rb_source_program_overview'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition($DB->sql_cast_2char('course_completions.timestarted'), 'certif'),
            array(
                'joins' => ['course_completions'],
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_newline_date',
                'style' => array('white-space' => 'pre'),
            )
        );

        $columnoptions[] = new rb_column_option(
            'course',
            'timecompleted',
            get_string('coursecompletiontimecompleted', 'rb_source_program_overview'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition($DB->sql_cast_2char('course_completions.timecompleted'), 'certif'),
            array(
                'joins' => ['course_completions'],
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_newline_date',
                'style' => array('white-space' => 'pre'),
            )
        );

        // Course grade.
        $columnoptions[] = new rb_column_option(
            'course',
            'finalgrade',
            get_string('finalgrade', 'rb_source_program_overview'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition($DB->sql_cast_2char('grade_grades.finalgrade'), 'certif'),
            array(
                'joins' => ['grade_grades', 'course'],
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'groupinginfo' => array(
                    'orderby' => array('prog_courseset.sortorder', 'prog_courseset_course.id'),
                ),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_newline',
                'style' => array('white-space' => 'pre'),
            )
        );

        $columnoptions[] = new rb_column_option(
            'course',
            'gradepass',
            get_string('gradepass', 'rb_source_program_overview'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition($DB->sql_cast_2char('criteria.gradepass'), 'certif'),
            array(
                'joins' => ['criteria', 'course'],
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_newline',
                'style' => array('white-space' => 'pre'),
            )
        );


        // Course category.
        $columnoptions[] = new rb_column_option(
            'course',
            'name',
            get_string('coursecategory', 'totara_reportbuilder'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition('course_category.name', 'certif'),
            array(
                'joins' => ['course_category'],
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_newline',
                'style' => array('white-space' => 'pre'),
                'dbdatatype' => 'char',
                'outputformat' => 'text'
            )
        );

        $columnoptions[] = new rb_column_option(
            'course',
            'namelink',
            get_string('coursecategorylinked', 'totara_reportbuilder'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition(
                $DB->sql_concat_join(
                    "'|'",
                    array(
                        $DB->sql_cast_2char('course_category.id'),
                        $DB->sql_cast_2char("course_category.visible"),
                        'course_category.name'
                    )
                ), 'certif'
            ),
            array(
                'joins' => 'course_category',
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'defaultheading' => get_string('category', 'totara_reportbuilder'),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_category_link_list',
                'style' => array('white-space' => 'pre'),
            )
        );

        $columnoptions[] = new rb_column_option(
            'course',
            'id',
            get_string('coursecategoryid', 'totara_reportbuilder'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition('course_category.idnumber', 'certif'),
            array(
                'joins' => array('course', 'course_category'),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_newline',
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'style' => array('white-space' => 'pre'),
                'dbdatatype' => 'char',
                'outputformat' => 'text'
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'timeduenice',
            get_string('duedateextra', 'rb_source_program_overview'),
            'base.timedue',
            array(
                'joins' => array('base', 'certif_completion'),
                'displayfunc' => 'programduedate',
                'extrafields' => array(
                    'status' => 'certif_completion.status',
                    'programid' => 'base.programid',
                    'certifpath' => 'certif_completion.certifpath',
                    'certifstatus' => 'certif_completion.status',
                    'userid' => 'base.userid',
                )
            )
        );

        // Certification path col.
        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'certifpath',
            get_string('certifpath', 'rb_source_certification_overview'),
            'certif_completion.certifpath',
            array(
                'joins' => 'certif_completion',
                'displayfunc' => 'certif_certifpath'
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'status',
            get_string('status', 'rb_source_dp_certification'),
            'certif_completion.status',
            array(
                'joins' => 'certif_completion',
                'displayfunc' => 'certif_status',
                'extrafields' => array(
                )
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'renewalstatus',
            get_string('renewalstatus', 'rb_source_dp_certification'),
            'certif_completion.renewalstatus',
            array(
                'joins' => 'certif_completion',
                'displayfunc' => 'certif_renewalstatus',
                'extrafields' => array(
                )
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'timewindowopens',
            get_string('timewindowopens', 'rb_source_dp_certification'),
            'certif_completion.timewindowopens',
            array(
                'joins' => 'certif_completion',
                'displayfunc' => 'nice_date',
                'extrafields' => array(
                    'status' => 'certif_completion.status'
                )
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'timeexpires',
            get_string('timeexpires', 'rb_source_dp_certification'),
            'certif_completion.timeexpires',
            array(
                'joins' => 'certif_completion',
                'displayfunc' => 'nice_date',
                'extrafields' => array(
                    'status' => 'certif_completion.status'
                )
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'timecompleted',
            get_string('timecompleted', 'rb_source_dp_certification'),
            'certif_completion.timecompleted',
            array(
                'joins' => 'certif_completion',
                'displayfunc' => 'nice_date'
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'progress',
            get_string('programcompletionprogress', 'rb_source_program_overview'),
            $DB->sql_concat('base.programid', "'|'", 'base.userid'),
            array(
                'extrafields' => array(
                    'completion' => "certif_completion.timecompleted",
                    'window' => "certif_completion.timewindowopens",
                    'histpath' => "history.certifpath",
                    'histcomp' => "history.timecompleted",
                    'stringexport' => 0,
                ),
                'displayfunc' => 'certif_completion_progress',
                'joins' => array('prog_completion', 'certif_completion', 'history'),
                'nosort' => true,
            )
        );

        $columnoptions[] = new rb_column_option(
            'certif_completion',
            'progresspercentage',
            get_string('programcompletionprogresspercentage', 'rb_source_program_overview'),
            $DB->sql_concat('base.programid', "'|'", 'base.userid'),
            array(
                'extrafields' => array(
                    'completion' => "certif_completion.timecompleted",
                    'window' => "certif_completion.timewindowopens",
                    'histpath' => "history.certifpath",
                    'histcomp' => "history.timecompleted",
                    'stringexport' => 1,
                ),
                'displayfunc' => 'certif_completion_progress',
                'joins' => array('certif_completion', 'history'),
                'nosort' => true,
            )
        );

        $columnoptions[] = new rb_column_option(
            'course',
            'shortname',
            get_string('courseshortname', 'rb_source_program_overview'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition('course.shortname', 'certif'),
            array(
                'joins' => ['course'],
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_name_list',
                'style' => array('white-space' => 'pre'),
            )

        );

        $columnoptions[] = new rb_column_option(
            'course',
            'status',
            get_string('coursecompletionstatus', 'rb_source_program_overview'),
            \totara_program\rb_course_sortorder_helper::get_column_field_definition($DB->sql_cast_2char('course_completions.status'), 'certif'),
            array(
                'joins' => ['course_completions'],
                'grouping' => 'sql_aggregate',
                'grouporder' => array(
                    'csorder'  => 'prog_courseset.sortorder',
                    'cscid'    => 'prog_courseset_course.id'
                ),
                'nosort' => true, // You can't sort concatenated columns.
                'displayfunc' => 'program_course_status_list',
                'style' => array('white-space' => 'pre'),
            )

        );

        return $columnoptions;
    }

    protected function define_filteroptions() {
        $filteroptions = array();

        $this->add_core_user_filters($filteroptions);
        $this->add_totara_certification_filters($filteroptions);
        $this->add_totara_job_filters($filteroptions, 'base', 'userid');

        $filteroptions[] = new rb_filter_option(
            'certif',
            'id',
            get_string('programnameselect', "rb_source_certification_overview"),
            'select',
            array(
                'selectfunc' => 'certification_list',
                'attributes' => rb_filter_option::select_width_limiter(),
                'simplemode' => true,
                'noanychoice' => true,
            )
        );

        $filteroptions[] = new rb_filter_option(
            'certif_completion',
            'timedue',
            get_string('duedate', 'rb_source_program_overview'),
            'date'
        );

        $filteroptions[] = new rb_filter_option(
            'certif_completion',
            'timecompleted',
            get_string('completeddate', 'rb_source_program_overview'),
            'date'
        );

        $filteroptions[] = new rb_filter_option(
            'certif_completion',
            'status',
            get_string('status', 'rb_source_dp_certification'),
            'select',
            array(
                'selectfunc' => 'certif_completion_status',
                'attributes' => rb_filter_option::select_width_limiter(),
            )
        );

        $filteroptions[] = new rb_filter_option(
            'certif_completion',
            'renewalstatus',
            get_string('renewalstatus', 'rb_source_dp_certification'),
            'select',
            array(
                'selectfunc' => 'certif_completion_renewalstatus',
                'attributes' => rb_filter_option::select_width_limiter(),
            )
        );

        return $filteroptions;
    }

    protected function define_contentoptions() {
        $contentoptions = array();

        // Add the manager/position/organisation content options.
        $this->add_basic_user_content_options($contentoptions);

        $contentoptions[] = new rb_content_option(
            'completed_org',
            get_string('orgwhencompleted', 'rb_source_course_completion_by_org'),
            'cplorganisation.path',
            'cplorganisation'
        );

        $contentoptions[] = new rb_content_option(
            'date',
            get_string('completeddate', 'rb_source_program_overview'),
            'base.timecompleted'
        );

        return $contentoptions;
    }

    protected function define_paramoptions() {
        $paramoptions = array(
            new rb_param_option(
                'programid',
                'base.programid'
            ),
            new rb_param_option(
                'visible',
                'certif.visible',
                'certif'
            ),
        );
        return $paramoptions;
    }

    protected function define_defaultcolumns() {
        $defaultcolumns = array();
        $defaultcolumns[] = array('type' => 'cerif', 'value' => 'shortname');
        $defaultcolumns[] = array('type' => 'job_assignment', 'value' => 'allorganisationnames');
        $defaultcolumns[] = array('type' => 'job_assignment', 'value' => 'allpositionnames');
        $defaultcolumns[] = array('type' => 'user', 'value' => 'namelink');
        $defaultcolumns[] = array('type' => 'certif_completion', 'value' => 'timedue');
        $defaultcolumns[] = array('type' => 'course', 'value' => 'shortname');
        $defaultcolumns[] = array('type' => 'course', 'value' => 'status');
        $defaultcolumns[] = array('type' => 'course', 'value' => 'finalgrade');
        $defaultcolumns[] = array('type' => 'certif_completion', 'value' => 'certifpath');

        return $defaultcolumns;
    }

    protected function define_defaultfilters() {
        $defaultfilters = array(
            array(
                'type' => 'certif',
                'value' => 'id',
                'advanced' => 0,
            ),
        );
        return $defaultfilters;
    }

    protected function define_requiredcolumns() {
        $requiredcolumns = array();
        $requiredcolumns[] = new rb_column(
            'certif',
            'groupbycol',
            '',
            "certif.id",
            array(
                'joins' => 'certif',
                'hidden' => true,
            )
        );
        return $requiredcolumns;
    }

    // Source specific filter display methods.
    function rb_filter_certification_list() {
        global $CFG;

        require_once($CFG->dirroot . '/totara/program/lib.php');

        $list = array();

        if ($progs = prog_get_programs('all', 'p.fullname', 'p.id, p.fullname', 'certification')) {
            foreach ($progs as $prog) {
                $list[$prog->id] = format_string($prog->fullname);
            }
        }
        return ($list);
    }

    /**
     * Certification completion status filter options
     */
    function rb_filter_certif_completion_status() {
        global $CERTIFSTATUS;

        $out = array();
        foreach ($CERTIFSTATUS as $key => $status) {
            $out[$key] = get_string($status, 'totara_certification');
        }

        return $out;
    }

    /**
     * Certification renewal status filter options
     */
    function rb_filter_certif_completion_renewalstatus() {
        global $CERTIFRENEWALSTATUS;

        $out = array();
        foreach ($CERTIFRENEWALSTATUS as $key => $status) {
            $out[$key] = get_string($status, 'totara_certification');
        }

        return $out;
    }

    /**
     * Returns expected result for column_test.
     * @param rb_column_option $columnoption
     * @return int
     */
    public function phpunit_column_test_expected_count($columnoption) {
        if (!PHPUNIT_TEST) {
            throw new coding_exception('phpunit_column_test_expected_count() cannot be used outside of unit tests');
        }
        if ($columnoption->type === 'course' or "{$columnoption->type}_{$columnoption->value}" === 'certif_completion_progress') {
            return 0;
        }
        return parent::phpunit_column_test_expected_count($columnoption);
    }
}
