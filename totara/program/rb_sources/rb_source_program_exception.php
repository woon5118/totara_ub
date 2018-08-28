<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
 * Copyright (C) 1999 onwards Martin Dougiamas
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
 * @author Simon Coggins <simon.coggins@totaralms.com>
 * @author Ben Lobo <ben.lobo@kineo.com>
 * @package totara
 * @subpackage reportbuilder
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/totara/program/lib.php');
require_once($CFG->dirroot . '/totara/cohort/lib.php');

class rb_source_program_exception extends rb_base_source {
    public $base, $joinlist, $columnoptions, $filteroptions;
    public $contentoptions, $paramoptions, $defaultcolumns;
    public $defaultfilters, $requiredcolumns, $sourcetitle;

    protected $instancetype = 'program';

    public function __construct() {
        $this->base = '{prog_exception}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->requiredcolumns = $this->define_requiredcolumns();
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_program_exception');
        $this->sourcewhere = $this->define_sourcewhere();
        parent::__construct();
    }

    /**
     * Hide this source if feature disabled or hidden.
     * @return bool
     */
    public function is_ignored() {
        return !totara_feature_visible('programs');
    }

    /**
     * Global report restrictions are implemented in this source.
     * @return boolean
     */
    public function global_restrictions_supported() {
        return false;
    }

    //
    //
    // Methods for defining contents of source
    //
    //

    protected function define_joinlist() {
        global $CFG;

        $joinlist = array(
            new rb_join(
                'prog',
                'INNER',
                '{prog}',
                "prog.id = base.programid",
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'base'
            ),
        );

        $this->add_user_table_to_joinlist($joinlist, 'base', 'userid');
        $this->add_course_category_table_to_joinlist($joinlist, 'prog', 'category');
        $this->add_cohort_program_tables_to_joinlist($joinlist, 'prog', 'id');

        return $joinlist;
    }

    protected function define_columnoptions() {

        $columnoptions[] = new rb_column_option(
            'exceptioninfo',
            'exceptiontype',
            get_string('exceptiontype','rb_source_program_exception'),
            "base.exceptiontype",
            array(
                'displayfunc' => 'program_exception_type'
            )
        );
        $columnoptions[] = new rb_column_option(
            'exceptioninfo',
            'timeraised',
            get_string('timeraised', 'rb_source_program_exception'),
            'base.timeraised',
            array('displayfunc' => 'nice_datetime', 'dbdatatype' => 'timestamp')
        );

        // include some standard columns
        $this->add_user_fields_to_columns($columnoptions);
        $this->add_job_assignment_fields_to_columns($columnoptions);
        $this->add_program_fields_to_columns($columnoptions, 'prog', "totara_{$this->instancetype}");
        $this->add_course_category_fields_to_columns($columnoptions, 'course_category', 'prog', 'programcount');

        return $columnoptions;
    }


    protected function define_filteroptions() {
        $filteroptions = array();


        $filteroptions[] = new rb_filter_option(
            'exceptioninfo',
            'exceptiontype',
            get_string('exceptiontype', 'rb_source_program_exception'),
            'select',
            array (
                'selectchoices' => $this->get_exception_options(),
                'attributes' => rb_filter_option::select_width_limiter(),
                'simplemode' => true,
            )
        );

        $filteroptions[] = new rb_filter_option(
            'exceptioninfo',
            'timeraised',
            get_string('timeraised', 'rb_source_program_exception'),
            'date'
        );

        // include some standard filters
        $this->add_program_fields_to_filters($filteroptions);
        $this->add_course_category_fields_to_filters($filteroptions, 'prog', 'category');

        return $filteroptions;
    }

    protected function define_contentoptions() {
        $contentoptions = array();

        // Add the manager/position/organisation content options.
        $this->add_basic_user_content_options($contentoptions);

        $contentoptions[] = new rb_content_option(
            'date',
            get_string('completeddate', 'rb_source_program_completion'),
            'base.timeraised'
        );

        return $contentoptions;
    }

    protected function define_paramoptions() {
        $paramoptions = array(
            new rb_param_option(
                'programid',
                'prog.id'
            ),
            new rb_param_option(
                'category',
                'prog.category'
            ),
        );
        return $paramoptions;
    }

    protected function define_defaultcolumns() {
        $defaultcolumns = array(
            array(
                'type' => 'prog',
                'value' => 'proglinkicon',
            ),
            array(
                'type' => 'user',
                'value' => 'fullname',
            ),
            array(
                'type' => 'exceptioninfo',
                'value' => 'exceptiontype',
            ),
            array(
                'type' => 'exceptioninfo',
                'value' => 'timeraised',
            ),
        );
        return $defaultcolumns;
    }

    protected function define_defaultfilters() {
        $defaultfilters = array(
            array(
                'type' => 'prog',
                'value' => 'fullname',
                'advanced' => 0,
            ),
            array(
                'type' => 'exceptioninfo',
                'value' => 'exceptiontype',
                'advanced' => 0,
            ),
            array(
                'type' => 'exceptioninfo',
                'value' => 'timeraised',
                'advanced' => 1,
            ),
        );
        return $defaultfilters;
    }

    protected function define_requiredcolumns() {
        $requiredcolumns = array();

        return $requiredcolumns;
    }

    protected function define_sourcewhere() {
        $sourcewhere = '(prog.certifid IS NULL)';

        return $sourcewhere;
    }

    public function post_config(reportbuilder $report) {
        $reportfor = $report->reportfor; // ID of the user the report is for.
    }

    /**
     * returns array with exception code as key and text label as value
     * @return array
     */
    private function get_exception_options(){
        $options = array();

        $options[EXCEPTIONTYPE_TIME_ALLOWANCE] = get_string('timeallowance', 'totara_program');
        $options[EXCEPTIONTYPE_ALREADY_ASSIGNED] = get_string('exceptiontypealreadyassigned', 'totara_program');
        $options[EXCEPTIONTYPE_COMPLETION_TIME_UNKNOWN] = get_string('completiontimeunknown', 'totara_program');
        $options[EXCEPTIONTYPE_UNKNOWN] = get_string('unknown', 'totara_program');
        $options[EXCEPTIONTYPE_DUPLICATE_COURSE] = get_string('exceptiontypeduplicatecourse', 'totara_program');

        return $options;
    }

    // Source specific column display methods.

    function rb_display_program_exception_type($type, $row) {
        $exceptions = $this->get_exception_options();

        if (!isset($exceptions[$type])) {
            return '';
        }

        return $exceptions[$type];
    }

    // Source specific filter display methods.

} // End of rb_source_courses class.

