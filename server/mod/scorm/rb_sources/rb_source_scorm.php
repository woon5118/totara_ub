<?php

defined('MOODLE_INTERNAL') || die();

class rb_source_scorm extends rb_base_source {
    use \core_course\rb\source\report_trait;
    use \core_tag\rb\source\report_trait;
    use \totara_job\rb\source\report_trait;
    use \totara_cohort\rb\source\report_trait;

    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Apply global user restrictions.
        $this->add_global_report_restriction_join('base', 'userid', 'auser');

        // scorm base table is a sub-query
        $this->base = '(SELECT max(id) as id, userid, scormid, scoid, attempt ' .
            "from {scorm_scoes_track} " .
            'GROUP BY userid, scormid, scoid, attempt)';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->requiredcolumns = $this->define_requiredcolumns();
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_scorm');
        $this->sourcesummary = get_string('sourcesummary', 'rb_source_scorm');
        $this->sourcelabel = get_string('sourcelabel', 'rb_source_scorm');
        $this->usedcomponents[] = 'totara_cohort';

        parent::__construct();
    }

    /**
     * Global report restrictions are implemented in this source.
     * @return boolean
     */
    public function global_restrictions_supported() {
        return true;
    }

    //
    //
    // Methods for defining contents of source
    //
    //

    protected function define_joinlist() {
        global $DB;
        $moduleid = $DB->get_field('modules', 'id', ['name' => 'scorm']);

        $joinlist = array(
            new rb_join(
                'scorm',
                'LEFT',
                '{scorm}',
                'scorm.id = base.scormid',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            new rb_join(
                'sco',
                'LEFT',
                '{scorm_scoes}',
                'sco.id = base.scoid',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            new rb_join(
                'cmdl',
                'LEFT',
                '{course_modules}',
                "(cmdl.module = {$moduleid} AND cmdl.instance = scorm.id)",
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'scorm'
            ),
        );

        // because of SCORMs crazy db design we have to self-join the table every
        // time we want a field - horribly inefficient, but should be okay until
        // scorm gets redesigned
        $elements = array(
            'starttime' => "'x.start.time'",
            'totaltime' => "'cmi.core.total_time', 'cmi.total_time'",
            'status' => "'cmi.core.lesson_status', 'cmi.completion_status'",
            'scoreraw' => "'cmi.core.score.raw', 'cmi.score.raw'",
            'scoremin' => "'cmi.core.score.min', 'cmi.score.min'",
            'scoremax' => "'cmi.core.score.max', 'cmi.score.max'",
        );
        foreach ($elements as $name => $element) {
            $key = "sco_$name";
            $joinlist[] = new rb_join(
                $key,
                'LEFT',
                '{scorm_scoes_track}',
                "($key.userid = base.userid AND $key.scormid = base.scormid" .
                " AND $key.scoid = base.scoid AND $key.attempt = " .
                " base.attempt AND $key.element IN ($element))",
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            );
        }

        // include some standard joins
        $this->add_core_user_tables($joinlist, 'base', 'userid');
        $this->add_core_course_tables($joinlist, 'scorm', 'course');
        // requires the course join
        $this->add_core_course_category_tables($joinlist,
            'course', 'category');
        $this->add_totara_job_tables($joinlist, 'base', 'userid');
        $this->add_core_tag_tables('core', 'course', $joinlist, 'scorm', 'course');
        $this->add_core_tag_tables('core', 'course_modules', $joinlist, 'cmdl', 'id');
        $this->add_totara_cohort_course_tables($joinlist, 'scorm', 'course');

        return $joinlist;
    }

    protected function define_columnoptions() {
        global $DB;

        $columnoptions = array(
            /*
            // array of rb_column_option objects, e.g:
            new rb_column_option(
                '',         // type
                '',         // value
                '',         // name
                '',         // field
                array()     // options
            )
            */
            new rb_column_option(
                'scorm',
                'title',
                get_string('scormtitle', 'rb_source_scorm'),
                'scorm.name',
                array('joins' => 'scorm',
                      'dbdatatype' => 'char',
                      'outputformat' => 'text',
                      'displayfunc' => 'format_string')
            ),
            new rb_column_option(
                'sco',
                'title',
                get_string('title', 'rb_source_scorm'),
                'sco.title',
                array('joins' => 'sco',
                      'dbdatatype' => 'char',
                      'outputformat' => 'text',
                      'displayfunc' => 'format_string')
            ),
            new rb_column_option(
                'sco',
                'starttime',
                get_string('time', 'rb_source_scorm'),
                $DB->sql_cast_char2int('sco_starttime.value', true),
                array(
                    'joins' => 'sco_starttime',
                    'displayfunc' => 'nice_datetime', 'dbdatatype' => 'timestamp',
                )
            ),
            new rb_column_option(
                'sco',
                'status',
                get_string('status', 'rb_source_scorm'),
                $DB->sql_compare_text('sco_status.value', 1024),
                array(
                    'joins' => 'sco_status',
                    'displayfunc' => 'ucfirst',
                    'dbdatatype' => 'text',
                    'outputformat' => 'text'
                )
            ),
            new rb_column_option(
                'sco',
                'totaltime',
                get_string('totaltime', 'rb_source_scorm'),
                $DB->sql_compare_text('sco_totaltime.value', 1024),
                array('joins' => 'sco_totaltime',
                      'displayfunc' => 'plaintext')
            ),
            new rb_column_option(
                'sco',
                'scoreraw',
                get_string('score', 'rb_source_scorm'),
                $DB->sql_cast_char2float('sco_scoreraw.value'),
                array(
                    'joins' => 'sco_scoreraw',
                    'displayfunc' => 'round2',
                    'dbdatatype' => 'decimal'
                )
            ),
            new rb_column_option(
                'sco',
                'statusmodified',
                get_string('statusmodified', 'rb_source_scorm'),
                'sco_status.timemodified',
                array(
                    'joins' => 'sco_status',
                    'displayfunc' => 'nice_datetime', 'dbdatatype' => 'timestamp'
                )
            ),
            new rb_column_option(
                'sco',
                'scoremin',
                get_string('minscore', 'rb_source_scorm'),
                $DB->sql_cast_char2float('sco_scoremin.value'),
                array(
                    'joins' => 'sco_scoremin',
                    'displayfunc' => 'round2',
                    'dbdatatype' => 'decimal'
                )
            ),
            new rb_column_option(
                'sco',
                'scoremax',
                get_string('maxscore', 'rb_source_scorm'),
                $DB->sql_cast_char2float('sco_scoremax.value'),
                array(
                    'joins' => 'sco_scoremax',
                    'displayfunc' => 'round2',
                    'dbdatatype' => 'decimal'
                )
            ),
            new rb_column_option(
                'sco',
                'attempt',
                get_string('attemptnum', 'rb_source_scorm'),
                'base.attempt',
                array('dbdatatype' => 'integer',
                      'displayfunc' => 'integer')
            ),
        );

        // include some standard columns
        $this->add_core_user_columns($columnoptions);
        $this->add_core_course_columns($columnoptions);
        $this->add_core_course_category_columns($columnoptions);
        $this->add_totara_job_columns($columnoptions);
        $this->add_core_tag_columns('core', 'course', $columnoptions);
        $this->add_core_tag_columns('core', 'course_modules', $columnoptions);
        $this->add_totara_cohort_course_columns($columnoptions);

        return $columnoptions;
    }

    protected function define_filteroptions() {
        $filteroptions = array(
            /*
            // array of rb_filter_option objects, e.g:
            new rb_filter_option(
                '',       // type
                '',       // value
                '',       // label
                '',       // filtertype
                array()   // options
            )
            */
            new rb_filter_option(
                'scorm',
                'title',
                get_string('scormtitle', 'rb_source_scorm'),
                'text'
            ),
            new rb_filter_option(
                'sco',
                'title',
                get_string('title', 'rb_source_scorm'),
                'text'
            ),
            new rb_filter_option(
                'sco',
                'starttime',
                get_string('attemptstart', 'rb_source_scorm'),
                'date'
            ),
            new rb_filter_option(
                'sco',
                'attempt',
                get_string('attemptnum', 'rb_source_scorm'),
                'number'
            ),
            new rb_filter_option(
                'sco',
                'status',
                get_string('status', 'rb_source_scorm'),
                'select',
                array('selectfunc' => 'scorm_status_list')
            ),
            new rb_filter_option(
                'sco',
                'statusmodified',
                get_string('statusmodified', 'rb_source_scorm'),
                'date'
            ),
            new rb_filter_option(
                'sco',
                'scoreraw',
                get_string('rawscore', 'rb_source_scorm'),
                'number'
            ),
            new rb_filter_option(
                'sco',
                'scoremin',
                get_string('minscore', 'rb_source_scorm'),
                'number'
            ),
            new rb_filter_option(
                'sco',
                'scoremax',
                get_string('maxscore', 'rb_source_scorm'),
                'number'
            ),
        );

        // include some standard filters
        $this->add_core_user_filters($filteroptions);
        $this->add_core_course_filters($filteroptions);
        $this->add_core_course_category_filters($filteroptions);
        $this->add_totara_job_filters($filteroptions, 'base', 'userid');
        $this->add_core_tag_filters('core', 'course', $filteroptions);
        $this->add_core_tag_filters('core', 'course_modules', $filteroptions);
        $this->add_totara_cohort_course_filters($filteroptions);

        return $filteroptions;
    }

    protected function define_contentoptions() {
        global $DB;

        $contentoptions = array();

        // Add the manager/position/organisation content options.
        $this->add_basic_user_content_options($contentoptions);

        $contentoptions[] = new rb_content_option(
            'date',
            get_string('thedate', 'rb_source_scorm'),
            $DB->sql_cast_char2int('sco_starttime.value', true),
            'sco_starttime'
        );

        return $contentoptions;
    }

    protected function define_paramoptions() {
        $paramoptions = array(
            new rb_param_option(
                'userid',       // parameter name
                'base.userid',  // field
                null            // joins
            ),
            new rb_param_option(
                'courseid',
                'scorm.course',
                'scorm'
            ),
        );
        return $paramoptions;
    }

    protected function define_defaultcolumns() {
        $defaultcolumns = array(
            array(
                'type' => 'user',
                'value' => 'namelink',
            ),
            array(
                'type' => 'scorm',
                'value' => 'title',
            ),
            array(
                'type' => 'sco',
                'value' => 'title',
            ),
            array(
                'type' => 'sco',
                'value' => 'attempt',
            ),
            array(
                'type' => 'sco',
                'value' => 'starttime',
            ),
            array(
                'type' => 'sco',
                'value' => 'totaltime',
            ),
            array(
                'type' => 'sco',
                'value' => 'status',
            ),
            array(
                'type' => 'sco',
                'value' => 'scoreraw',
            ),
        );

        return $defaultcolumns;
    }

    protected function define_defaultfilters() {
        $defaultfilters = array(
            array(
                'type' => 'user',
                'value' => 'fullname',
            ),
            array(
                'type' => 'job_assignment',
                'value' => 'allpositions',
                'advanced' => 1,
            ),
            array(
                'type' => 'job_assignment',
                'value' => 'allorganisations',
                'advanced' => 1,
            ),
            array(
                'type' => 'sco',
                'value' => 'status',
                'advanced' => 1,
            ),
            array(
                'type' => 'sco',
                'value' => 'starttime',
                'advanced' => 1,
            ),
            array(
                'type' => 'sco',
                'value' => 'attempt',
                'advanced' => 1,
            ),
            array(
                'type' => 'sco',
                'value' => 'scoreraw',
                'advanced' => 1,
            ),
        );

        return $defaultfilters;
    }

    protected function define_requiredcolumns() {
        $requiredcolumns = array(
            /*
            // array of rb_column objects, e.g:
            new rb_column(
                '',         // type
                '',         // value
                '',         // heading
                '',         // field
                array(),    // options
            )
            */
        );
        return $requiredcolumns;
    }

    //
    //
    // Source specific filter display methods
    //
    //

    /**
     * @deprecated since Totara 12.17
     */
    function rb_filter_scorm_attempt_list() {
        global $DB;

        debugging('rb_filter_scorm_attempt_list has been deprecated use a number type filter instead', DEBUG_DEVELOPER);

        if (!$max = $DB->get_field_sql('SELECT MAX(attempt) FROM {scorm_scoes_track}')) {
            $max = 10;
        }
        $attemptselect = array();
        foreach( range(1, $max) as $attempt) {
            $attemptselect[$attempt] = $attempt;
        }
        return $attemptselect;
    }

    function rb_filter_scorm_status_list() {
        // The list of statuses is defined by SCORM standard,
        // there is no reason to look for arbitrary values in database.

        // https://scorm.com/scorm-explained/technical-scorm/run-time/run-time-reference/?utm_source=google&utm_medium=natural_search#cmicorelessonstatus1112
        return array(
            'passed' => get_string('passed', 'rb_source_scorm'),
            'completed' => get_string('completed', 'rb_source_scorm'),
            'not attempted' => get_string('notattempted', 'rb_source_scorm'),
            'browsed' => get_string('browsed', 'rb_source_scorm'), // This is not used in Totara any more
            'incomplete' => get_string('incomplete', 'rb_source_scorm'),
            'failed' => get_string('failed', 'rb_source_scorm'),
        );
    }


} // end of rb_source_scorm class

