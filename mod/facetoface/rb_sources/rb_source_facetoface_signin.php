<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @author Lee Campbell <lee@learningpool.com>
 * @package mod_facetoface
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/facetoface/rb_sources/rb_facetoface_base_source.php');

/**
 * FacetoFace downloadable sign in sheet report.
 *
 * Class rb_source_facetoface_signin
 */
class rb_source_facetoface_signin extends rb_facetoface_base_source {
    public $base, $joinlist, $columnoptions, $filteroptions;
    public $contentoptions, $paramoptions, $defaultcolumns;
    public $defaultfilters, $sourcetitle, $requiredcolumns;

    public $exportrowcount = false;

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
        $this->add_global_report_restriction_join('base', 'userid');

        $this->usedcomponents[] = 'mod_facetoface';
        $this->base = '{facetoface_signups}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->paramoptions = $this->define_paramoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->defaultfilters = $this->define_defaultfilters();
        $this->requiredcolumns = $this->define_requiredcolumns();
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_facetoface_signin');
        $this->add_customfields();
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

    /**
     * Define the joins available for this report.
     *
     * @return array
     */
    protected function define_joinlist() {
        global $CFG;
        require_once($CFG->dirroot .'/mod/facetoface/lib.php');

        $joinlist = array(
            new rb_join(
                'sessions',
                'INNER',
                '{facetoface_sessions}',
                'sessions.id = base.sessionid',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            new rb_join(
                'facetoface',
                'LEFT',
                '{facetoface}',
                'facetoface.id = sessions.facetoface',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'sessions'
            ),
            new rb_join(
                'sessiondate',
                'LEFT',
                '{facetoface_sessions_dates}',
                '(sessiondate.sessionid = base.sessionid AND sessions.datetimeknown = 1)',
                REPORT_BUILDER_RELATION_ONE_TO_MANY,
                'sessions'
            ),
            new rb_join(
                'status',
                'LEFT',
                '{facetoface_signups_status}',
                '(status.signupid = base.id AND status.superceded = 0)',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            new rb_join(
                'attendees',
                'LEFT',
                "(SELECT su.sessionid, count(ss.id) AS number
                  FROM {facetoface_signups} su
                  JOIN {facetoface_signups_status} ss
                      ON su.id = ss.signupid
                  WHERE ss.superceded=0 AND ss.statuscode >= 50
                  GROUP BY su.sessionid)",
                'attendees.sessionid = base.sessionid',
                REPORT_BUILDER_RELATION_ONE_TO_ONE
            ),
            new rb_join(
                'room',
                'LEFT',
                '{facetoface_room}',
                'sessions.roomid = room.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'sessions'
            ),
            new rb_join(
                'bookedby',
                'LEFT',
                '{user}',
                'bookedby.id = base.bookedby',
                REPORT_BUILDER_RELATION_MANY_TO_ONE
            ),
            new rb_join(
                'pos',
                'LEFT',
                '{pos}',
                'pos.id = base.positionid',
                REPORT_BUILDER_RELATION_MANY_TO_ONE
            ),
            new rb_join(
                'pos_assignment',
                'LEFT',
                '{pos_assignment}',
                'pos_assignment.id = base.positionassignmentid',
                REPORT_BUILDER_RELATION_MANY_TO_ONE
            ),
        );

        // Include some standard joins.
        $this->add_user_table_to_joinlist($joinlist, 'base', 'userid');
        $this->add_course_table_to_joinlist($joinlist, 'facetoface', 'course', 'INNER');
        $this->add_context_table_to_joinlist($joinlist, 'course', 'id', CONTEXT_COURSE, 'INNER');
        $this->add_course_category_table_to_joinlist($joinlist, 'course', 'category');
        $this->add_position_tables_to_joinlist($joinlist, 'base', 'userid');
        $this->add_manager_tables_to_joinlist($joinlist, 'position_assignment', 'reportstoid');
        $this->add_tag_tables_to_joinlist('course', $joinlist, 'facetoface', 'course');
        $this->add_facetoface_session_roles_to_joinlist($joinlist);
        $this->add_cohort_user_tables_to_joinlist($joinlist, 'base', 'userid');
        $this->add_cohort_course_tables_to_joinlist($joinlist, 'facetoface', 'course');

        return $joinlist;
    }

    /**
     * Define the column options available for this report.
     *
     * @return array
     */
    protected function define_columnoptions() {
        global $DB;
        $usernamefieldsbooked  = totara_get_all_user_name_fields_join('bookedby');

        $columnoptions = array(
            new rb_column_option(
                'session',
                'capacity',
                get_string('sesscapacity', 'rb_source_facetoface_signin'),
                'sessions.capacity',
                array('joins' => 'sessions', 'dbdatatype' => 'integer')
            ),
            new rb_column_option(
                'session',
                'numattendees',
                get_string('numattendees', 'rb_source_facetoface_signin'),
                'attendees.number',
                array('joins' => 'attendees', 'dbdatatype' => 'integer')
            ),
            new rb_column_option(
                'session',
                'details',
                get_string('sessdetails', 'rb_source_facetoface_signin'),
                'sessions.details',
                array(
                    'joins' => 'sessions',
                    'displayfunc' => 'tinymce_textarea',
                    'extrafields' => array(
                        'filearea' => '\'session\'',
                        'component' => '\'mod_facetoface\'',
                        'fileid' => 'sessions.id',
                        'context' => '\'context_module\'',
                        'recordid' => 'sessions.facetoface'
                    ),
                    'dbdatatype' => 'text',
                    'outputformat' => 'text'
                )
            ),
            new rb_column_option(
                'session',
                'duration',
                get_string('sessduration', 'rb_source_facetoface_signin'),
                'CASE WHEN sessions.datetimeknown = 1
                    THEN
                        (sessiondate.timefinish-sessiondate.timestart)/' . MINSECS . '
                    ELSE
                        sessions.duration/' . MINSECS . '
                    END',
                array(
                    'joins' => array('sessiondate','sessions'),
                    'dbdatatype' => 'decimal',
                    'displayfunc' => 'hours_minutes',
                )
            ),
            new rb_column_option(
                'status',
                'statuscode',
                get_string('status', 'rb_source_facetoface_signin'),
                'status.statuscode',
                array(
                    'joins' => 'status',
                    'displayfunc' => 'f2f_status',
                )
            ),
            new rb_column_option(
                'session',
                'discountcode',
                get_string('discountcode', 'rb_source_facetoface_signin'),
                'base.discountcode',
                array('dbdatatype' => 'text',
                    'outputformat' => 'text')
            ),
            new rb_column_option(
                'session',
                'normalcost',
                get_string('normalcost', 'rb_source_facetoface_signin'),
                'sessions.normalcost',
                array(
                    'joins' => 'sessions',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text'
                )
            ),
            new rb_column_option(
                'session',
                'discountcost',
                get_string('discountcost', 'rb_source_facetoface_signin'),
                'sessions.discountcost',
                array(
                    'joins' => 'sessions',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text'
                )
            ),
            new rb_column_option(
                'facetoface',
                'name',
                get_string('f2fname', 'rb_source_facetoface_signin'),
                'facetoface.name',
                array('joins' => 'facetoface',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text')
            ),
            new rb_column_option(
                'facetoface',
                'namelink',
                get_string('f2fnamelink', 'rb_source_facetoface_signin'),
                "facetoface.name",
                array(
                    'joins' => array('facetoface','sessions'),
                    'displayfunc' => 'link_f2f',
                    'defaultheading' => get_string('f2fname', 'rb_source_facetoface_signin'),
                    'extrafields' => array('activity_id' => 'sessions.facetoface'),
                )
            ),
            new rb_column_option(
                'date',
                'sessiondate',
                get_string('sessdate', 'rb_source_facetoface_signin'),
                'sessiondate.timestart',
                array(
                    'extrafields' => array('timezone' => 'sessiondate.sessiontimezone'),
                    'joins' =>'sessiondate',
                    'displayfunc' => 'nice_date_in_timezone',
                    'dbdatatype' => 'timestamp'
                )
            ),
            new rb_column_option(
                'date',
                'sessiondate_link',
                get_string('sessdatelink', 'rb_source_facetoface_signin'),
                'sessiondate.timestart',
                array(
                    'joins' => 'sessiondate',
                    'displayfunc' => 'link_f2f_session',
                    'defaultheading' => get_string('sessdate', 'rb_source_facetoface_signin'),
                    'extrafields' => array('session_id' => 'base.sessionid', 'timezone' => 'sessiondate.sessiontimezone'),
                    'dbdatatype' => 'timestamp'
                )
            ),
            new rb_column_option(
                'date',
                'datefinish',
                get_string('sessdatefinish', 'rb_source_facetoface_signin'),
                'sessiondate.timefinish',
                array(
                    'extrafields' => array('timezone' => 'sessiondate.sessiontimezone'),
                    'joins' => 'sessiondate',
                    'displayfunc' => 'nice_date_in_timezone',
                    'dbdatatype' => 'timestamp')
            ),
            new rb_column_option(
                'date',
                'timestart',
                get_string('sessstart', 'rb_source_facetoface_signin'),
                'sessiondate.timestart',
                array(
                    'extrafields' => array('timezone' => 'sessiondate.sessiontimezone'),
                    'joins' => 'sessiondate',
                    'displayfunc' => 'nice_time_in_timezone',
                    'dbdatatype' => 'timestamp'
                )
            ),
            new rb_column_option(
                'date',
                'timefinish',
                get_string('sessfinish', 'rb_source_facetoface_signin'),
                'sessiondate.timefinish',
                array(
                    'extrafields' => array('timezone' => 'sessiondate.sessiontimezone'),
                    'joins' => 'sessiondate',
                    'displayfunc' => 'nice_time_in_timezone',
                    'dbdatatype' => 'timestamp'
                )
            ),
            new rb_column_option(
                'session',
                'bookedby',
                get_string('bookedby', 'rb_source_facetoface_signin'),
                $DB->sql_concat_join("' '", $usernamefieldsbooked),
                array('joins' => 'bookedby', 'displayfunc' => 'link_f2f_bookedby',
                    'extrafields' => array_merge(array('user_id' => 'bookedby.id')), $usernamefieldsbooked)
            ),
            new rb_column_option(
                'room',
                'name',
                get_string('roomname', 'rb_source_facetoface_signin'),
                'room.name',
                array('joins' => 'room',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text')
            ),
            new rb_column_option(
                'room',
                'building',
                get_string('building', 'rb_source_facetoface_signin'),
                'room.building',
                array('joins' => 'room',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text')
            ),
            new rb_column_option(
                'room',
                'address',
                get_string('address', 'rb_source_facetoface_signin'),
                'room.address',
                array('joins' => 'room',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text')
            ),
            new rb_column_option(
                'room',
                'capacity',
                get_string('roomcapacity', 'rb_source_facetoface_signin'),
                'room.capacity',
                array('joins' => 'room', 'dbdatatype' => 'integer')
            ),
            new rb_column_option(
                'room',
                'description',
                get_string('roomdescription', 'rb_source_facetoface_signin'),
                'room.description',
                array('joins' => 'room',
                    'dbdatatype' => 'text',
                    'outputformat' => 'text')
            ),
            new rb_column_option(
                'session',
                'positionname',
                get_string('selectedposition', 'mod_facetoface'),
                'pos.fullname',
                array('joins' => 'pos',
                    'dbdatatype' => 'text',
                    'outputformat' => 'text')
            ),
            new rb_column_option(
                'session',
                'positionassignmentname',
                get_string('selectedpositionassignment', 'mod_facetoface'),
                'pos_assignment.fullname',
                array('joins' => 'pos_assignment',
                    'dbdatatype' => 'text',
                    'outputformat' => 'text')
            ),
            new rb_column_option(
                'session',
                'positiontype',
                get_string('selectedpositiontype', 'mod_facetoface'),
                'base.positiontype',
                array('dbdatatype' => 'text',
                    'outputformat' => 'text',
                    'displayfunc' => 'position_type')
            ),
            new rb_column_option(
                'user_signups',
                'signature',
                get_string('signature', 'mod_facetoface'),
                'base.id',
                array(
                    'displayfunc' => 'signature'
                )
            ),
        );

        // Include some standard columns.
        $this->add_user_fields_to_columns($columnoptions);
        $this->add_course_fields_to_columns($columnoptions);
        $this->add_course_category_fields_to_columns($columnoptions);
        $this->add_position_fields_to_columns($columnoptions);
        $this->add_manager_fields_to_columns($columnoptions);
        $this->add_tag_fields_to_columns('course', $columnoptions);
        $this->add_facetoface_session_roles_to_columns($columnoptions);
        $this->add_cohort_user_fields_to_columns($columnoptions);
        $this->add_cohort_course_fields_to_columns($columnoptions);

        return $columnoptions;
    }

    /**
     * Define the filter options available for this report.
     *
     * @return array
     */
    protected function define_filteroptions() {
        $filteroptions = array(
            new rb_filter_option(
                'facetoface',
                'name',
                get_string('f2fname', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'status',
                'statuscode',
                get_string('status', 'rb_source_facetoface_signin'),
                'select',
                array(
                    'selectfunc' => 'session_status_list',
                    'attributes' => rb_filter_option::select_width_limiter(),
                )
            ),
            new rb_filter_option(
                'date',
                'sessiondate',
                get_string('sessdate', 'rb_source_facetoface_signin'),
                'date'
            ),
            new rb_filter_option(
                'date',
                'timestart',
                get_string('sessstart', 'rb_source_facetoface_signin'),
                'date',
                array('includetime' => true)
            ),
            new rb_filter_option(
                'date',
                'timefinish',
                get_string('sessfinish', 'rb_source_facetoface_signin'),
                'date',
                array('includetime' => true)
            ),
            new rb_filter_option(
                'session',
                'capacity',
                get_string('sesscapacity', 'rb_source_facetoface_signin'),
                'number'
            ),
            new rb_filter_option(
                'session',
                'details',
                get_string('sessdetails', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'session',
                'discountcode',
                get_string('discountcode', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'session',
                'duration',
                get_string('sessduration', 'rb_source_facetoface_signin'),
                'number'
            ),
            new rb_filter_option(
                'session',
                'normalcost',
                get_string('normalcost', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'session',
                'discountcost',
                get_string('discountcost', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'session',
                'bookedby',
                get_string('bookedby', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'session',
                'reserved',
                get_string('reserved', 'rb_source_facetoface_signin'),
                'select',
                array(
                    'selectchoices' => array(
                        '0' => get_string('reserved', 'rb_source_facetoface_signin'),
                    )
                ),
                'base.userid'
            ),
            new rb_filter_option(
                'room',
                'name',
                get_string('roomname', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'room',
                'building',
                get_string('building', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'room',
                'address',
                get_string('address', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'room',
                'capacity',
                get_string('roomcapacity', 'rb_source_facetoface_signin'),
                'number'
            ),
            new rb_filter_option(
                'room',
                'description',
                get_string('roomdescription', 'rb_source_facetoface_signin'),
                'text'
            ),
            new rb_filter_option(
                'session',
                'positionname',
                get_string('selectedpositionname', 'mod_facetoface'),
                'select',
                array(
                    'selectfunc' => 'positions_list',
                    'attributes' => rb_filter_option::select_width_limiter(),
                ),
                'pos.id',
                'pos'
            ),
            new rb_filter_option(
                'session',
                'positionassignment',
                get_string('selectedpositionassignment', 'mod_facetoface'),
                'text',
                array(),
                'pos_assignment.fullname',
                'pos_assignment'
            ),
            new rb_filter_option(
                'session',
                'positiontype',
                get_string('selectedpositiontype', 'mod_facetoface'),
                'select',
                array(
                    'selectfunc' => 'position_types_list',
                    'attributes' => rb_filter_option::select_width_limiter(),
                ),
                'base.positiontype'
            ),
        );

        // Include some standard filters.
        $this->add_user_fields_to_filters($filteroptions);
        $this->add_course_fields_to_filters($filteroptions);
        $this->add_course_category_fields_to_filters($filteroptions);
        $this->add_position_fields_to_filters($filteroptions);
        $this->add_manager_fields_to_filters($filteroptions);
        $this->add_tag_fields_to_filters('course', $filteroptions);
        $this->add_facetoface_session_role_fields_to_filters($filteroptions);
        $this->add_cohort_user_fields_to_filters($filteroptions);
        $this->add_cohort_course_fields_to_filters($filteroptions);

        return $filteroptions;
    }

    /**
     * Position types filter.
     *
     * @return array
     */
    public function rb_filter_position_types_list() {
        global $CFG, $POSITION_TYPES;

        include_once($CFG->dirroot.'/totara/hierarchy/prefix/position/lib.php');

        return $POSITION_TYPES;
    }

    /**
     * Define the available content options for this report.
     *
     * @return array
     */
    protected function define_contentoptions() {
        $contentoptions = array(
            new rb_content_option(
                'current_pos',
                get_string('currentpos', 'totara_reportbuilder'),
                'position.path',
                'position'
            ),
            new rb_content_option(
                'current_org',
                get_string('currentorg', 'totara_reportbuilder'),
                'organisation.path',
                'organisation'
            ),
            new rb_content_option(
                'user',
                get_string('user', 'rb_source_facetoface_signin'),
                array(
                    'userid' => 'base.userid',
                    'managerid' => 'position_assignment.managerid',
                    'managerpath' => 'position_assignment.managerpath',
                    'postype' => 'position_assignment.type',
                ),
                'position_assignment'
            ),
            new rb_content_option(
                'date',
                get_string('thedate', 'rb_source_facetoface_signin'),
                'sessiondate.timestart',
                'sessiondate'
            ),
        );
        return $contentoptions;
    }

    /**
     * Define the available param options for this report.
     *
     * @return array
     */
    protected function define_paramoptions() {
        $paramoptions = array(
            new rb_param_option(
                'userid',
                'base.userid'
            ),
            new rb_param_option(
                'courseid',
                'course.id',
                'course'
            ),
            new rb_param_option(
                'status',
                'status.statuscode',
                'status'
            ),
            new rb_param_option(
                'sessionid',
                'base.sessionid'
            ),
            new rb_param_option(
                'hasbooked',
                'CASE WHEN status.statuscode >= 70 THEN 1 ELSE 0 END',
                'status'
            )
        );

        return $paramoptions;
    }

    /**
     * Define the default columns for this report.
     *
     * @return array
     */
    protected function define_defaultcolumns() {
        $defaultcolumns = array(
            array(
                'type' => 'user',
                'value' => 'namelink',
            ),
            array(
                'type' => 'user_signups',
                'value' => 'signature',
            )
        );

        return $defaultcolumns;
    }

    /**
     * Columns required for totara_visibility_where() to function correctly in post_config.
     *
     * @return array
     */
    protected function define_requiredcolumns() {
        $requiredcolumns = array();

        $requiredcolumns[] = new rb_column(
            'course',
            'coursevisible',
            '',
            "course.visible",
            array(
                'joins' => 'course',
                'required' => 'true',
                'hidden' => 'true'
            )
        );

        $requiredcolumns[] = new rb_column(
            'course',
            'courseaudiencevisible',
            '',
            "course.audiencevisible",
            array(
                'joins' => 'course',
                'required' => 'true',
                'hidden' => 'true')
        );

        $requiredcolumns[] = new rb_column(
            'ctx',
            'id',
            '',
            "ctx.id",
            array(
                'joins' => 'ctx',
                'required' => 'true',
                'hidden' => 'true'
            )
        );

        return $requiredcolumns;
    }

    /**
     * Define the default filters for this report.
     *
     * @return array
     */
    protected function define_defaultfilters() {
        $defaultfilters = array(
            array(
                'type' => 'user',
                'value' => 'fullname',
            ),
            array(
                'type' => 'course',
                'value' => 'fullname',
                'advanced' => 1,
            ),
            array(
                'type' => 'status',
                'value' => 'statuscode',
                'advanced' => 1,
            ),
            array(
                'type' => 'date',
                'value' => 'sessiondate',
                'advanced' => 1,
            ),
        );

        return $defaultfilters;
    }

    /**
     * Define the custom fields for this report.
     *
     * @return void
     */
    protected function add_customfields() {
        $this->add_custom_fields_for('facetoface_session', 'sessions', 'facetofacesessionid', $this->joinlist, $this->columnoptions, $this->filteroptions);
        $this->add_custom_fields_for('facetoface_signup', 'status', 'facetofacesignupid', $this->joinlist, $this->columnoptions, $this->filteroptions);
    }

    //
    //
    // Face-to-face specific display functions
    //
    //

    /**
     * Display function for signature column.
     *
     * @param $position
     * @param $row
     * @return string
     */
    public function rb_display_signature($position, $row) {
        return '';
    }

    /**
     * Display function for position type.
     *
     * @param $position
     * @param $row
     * @return string
     */
    public function rb_display_position_type($position, $row) {
        global $CFG, $POSITION_TYPES;

        include_once($CFG->dirroot.'/totara/hierarchy/prefix/position/lib.php');

        return get_string('type'.$POSITION_TYPES[$position], 'totara_hierarchy');
    }

    /**
     * Display function for the booking managers name (linked to
     * their profile).
     *
     * @param $name
     * @param $row
     * @return string
     */
    function rb_display_link_f2f_bookedby($name, $row) {
        $user = fullname($row);
        return $this->rb_display_link_user($user, $row, false);
    }

    /**
     * Display function for the actioning users name (linked to
     * their profile).
     *
     * @param $name
     * @param $row
     * @return string
     */
    function rb_display_link_f2f_actionedby($name, $row) {
        $user = fullname($row);
        return $this->rb_display_link_user($user, $row, false);
    }

    /**
     * Display function to show 'Reserved' for reserved spaces.
     *
     * @param string $user
     * @param object $row
     * @param bool $isexport
     * @return string
     */
    function rb_display_link_user($user, $row, $isexport = false) {
        if ($row->id) {
            return parent::rb_display_link_user($user, $row, $isexport);
        }
        return get_string('reserved', 'rb_source_facetoface_signin');
    }

    /**
     * Display function to link the user icon.
     *
     * @param string $user
     * @param object $row
     * @param bool $isexport
     * @return string
     */
    function rb_display_link_user_icon($user, $row, $isexport = false) {
        if ($row->id) {
            return parent::rb_display_link_user_icon($user, $row, $isexport);
        }
        return get_string('reserved', 'rb_source_facetoface_signin');
    }

    /**
     * Display function to show the user.
     *
     * @param string $user
     * @param object $row
     * @param bool $isexport
     * @return string
     */
    function rb_display_user($user, $row, $isexport = false) {
        if (!empty($user)) {
            return parent::rb_display_user($user, $row, $isexport);
        }
        return get_string('reserved', 'rb_source_facetoface_signin');
    }

    //
    //
    // Source specific filter display methods
    //
    //

    /**
     * Filter option for session status list.
     *
     * @return array
     */
    function rb_filter_session_status_list() {
        global $CFG,$MDL_F2F_STATUS;

        include_once($CFG->dirroot.'/mod/facetoface/lib.php');

        $output = array();
        if (is_array($MDL_F2F_STATUS)) {
            foreach ($MDL_F2F_STATUS as $code => $statusitem) {
                $output[$code] = get_string('status_'.$statusitem,'facetoface');
            }
        }
        // Show most completed option first in pulldown.
        return array_reverse($output, true);

    }

    /**
     * Filter option for course delivery list.
     *
     * @return array
     */
    function rb_filter_coursedelivery_list() {
        $coursedelivery = array();
        $coursedelivery['Internal'] = 'Internal';
        $coursedelivery['External'] = 'External';
        return $coursedelivery;
    }

    /**
     * Report post config operations.
     *
     * @param reportbuilder $report
     */
    public function post_config(reportbuilder $report) {
        $userid = $report->reportfor;
        if (isset($report->embedobj->embeddedparams['userid'])) {
            $userid = $report->embedobj->embeddedparams['userid'];
        }
        $fieldalias = 'course';
        $fieldbaseid = $report->get_field('course', 'id', 'course.id');
        $fieldvisible = $report->get_field('course', 'coursevisible', 'course.visible');
        $fieldaudvis = $report->get_field('course', 'courseaudiencevisible', 'course.audiencevisible');
        $report->set_post_config_restrictions(totara_visibility_where($userid,
            $fieldbaseid, $fieldvisible, $fieldaudvis, $fieldalias, 'course', $report->is_cached()));
    }

    /**
     * Custom PDF header content.
     *
     * @param reportbuilder $report
     * @return string
     */
    public function custom_pdf_header(reportbuilder $report) {
        global $DB;

        if(!isset($report->embedobj->embeddedparams['sessionid'])) {
            return '';
        }

        $session = facetoface_get_session($report->embedobj->embeddedparams['sessionid']);
        if(!$session) {
            return '';
        }

        $dates = facetoface_format_session_times($session->sessiondates[0]->timestart,
            $session->sessiondates[0]->timefinish, $session->sessiondates[0]->sessiontimezone
        );

        $activityname = $DB->get_field('facetoface', 'name', array('id' => $session->facetoface));

        $rooms = facetoface_get_rooms($session->facetoface);
        if(empty($session->roomid)) {
            $roomstring = get_string('nonapplicable', 'facetoface');
        } else {
            $room = $rooms[$session->roomid];

            $roomarray[] = !empty($room->name) ? format_string($room->name) : false;
            $roomarray[] = !empty($room->building) ? format_string($room->building) : false;
            $roomarray[] = !empty($room->address) ? format_string($room->address) : false;

            if (empty($roomarray)) {
                $roomstring = '';
            } else {
                $roomstring = implode('<br />', $roomarray);
            }

            unset($room);
        }

        $table = new html_table();
        $table->size = array('200px');
        $table->head = array();

        $titlestyles = 'font-weight:bold;';

        $table->data = array();

        // Get session custom fields.
        $sessioncustomfields = customfield_get_fields($session, 'facetoface_session', 'facetofacesession');
        if (!empty($sessioncustomfields)) {
            foreach($sessioncustomfields as $name => $data) {
                $title = new html_table_cell($name);
                $title->style = $titlestyles;
                $table->data[] = new html_table_row(array(
                    $title,
                    $data));
            }
        }

        $titlestrings = get_strings(array(
            'sessionname','sessionstartdate','sessionenddate',
            'capacity','numberofattendees','place'),
            'mod_facetoface');

        $titles = array();
        foreach($titlestrings as $identifier => $string) {
            $titles[$identifier] = new html_table_cell($string);
            $titles[$identifier]->style = $titlestyles;
        }

        $table->data[] = new html_table_row(array(
            $titles['sessionstartdate'],
            get_string('sessionstartdatewithtime', 'facetoface', $dates)));

        $table->data[] = new html_table_row(array(
            $titles['sessionenddate'],
            get_string('sessionenddatewithtime', 'facetoface', $dates)));

        $table->data[] = new html_table_row(array(
            $titles['capacity'],
            $session->capacity));

        $table->data[] = new html_table_row(array(
            $titles['numberofattendees'],
            facetoface_get_num_attendees($session->id)));

        $table->data[] = new html_table_row(array(
            $titles['place'],
            $roomstring));

        return html_writer::table($table);
    }

} // end of rb_source_facetoface_signin class
