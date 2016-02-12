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
 * @author Michael Gwynne <michael.gwynne@kineo.com>
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package mod_facetoface
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/facetoface/lib.php');
require_once($CFG->dirroot . '/mod/facetoface/rb_sources/rb_facetoface_base_source.php');

class rb_source_facetoface_summary extends rb_facetoface_base_source {
    public $base, $joinlist, $columnoptions, $filteroptions;
    public $contentoptions, $paramoptions, $defaultcolumns;
    public $defaultfilters, $sourcetitle;

    public function __construct($groupid, rb_global_restriction_set $globalrestrictionset = null) {
        if ($groupid instanceof rb_global_restriction_set) {
            throw new coding_exception('Wrong parameter orders detected during report source instantiation.');
        }
        // Remember the active global restriction set.
        $this->globalrestrictionset = $globalrestrictionset;

        // Global report restrictions are applied in define_joinlist() method.

        $this->usedcomponents[] = 'mod_facetoface';
        $this->base = '{facetoface_sessions_dates}';
        $this->joinlist = $this->define_joinlist();
        $this->columnoptions = $this->define_columnoptions();
        $this->filteroptions = $this->define_filteroptions();
        $this->contentoptions = $this->define_contentoptions();
        $this->defaultcolumns = $this->define_defaultcolumns();
        $this->requiredcolumns = $this->define_requiredcolumns();
        $this->sourcetitle = get_string('sourcetitle', 'rb_source_facetoface_summary');
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

    function define_joinlist() {
        $global_restriction_join_su = $this->get_global_report_restriction_join('su', 'userid');

        $joinlist = array(
            new rb_join(
                'sessions',
                'INNER',
                '{facetoface_sessions}',
                "sessions.id = base.sessionid",
                REPORT_BUILDER_RELATION_MANY_TO_ONE
            ),
            new rb_join(
                'facetoface',
                'LEFT',
                '{facetoface}',
                '(facetoface.id = sessions.facetoface)',
                REPORT_BUILDER_RELATION_ONE_TO_MANY,
                'sessions'
            ),
            new rb_join(
                'attendees',
                'LEFT',
                "(SELECT su.sessionid, su.userid, ss.id AS ssid, ss.statuscode
                    FROM {facetoface_signups} su
                    {$global_restriction_join_su}
                    JOIN {facetoface_signups_status} ss
                        ON su.id = ss.signupid
                    WHERE ss.superceded = 0)",
                'attendees.sessionid = sessions.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'sessions'
            ),
            // No global restrictions here because status is absolute (e.g if it is overbooked then it is overbooked, even if user
            // cannot see all participants.
            new rb_join(
                'cntbookings',
                'LEFT',
                "(SELECT s.id sessionid, COUNT(ss.id) cntsignups
                    FROM {facetoface_sessions} s
                    LEFT JOIN {facetoface_signups} su ON (su.sessionid = s.id)
                    LEFT JOIN {facetoface_signups_status} ss
                        ON (su.id = ss.signupid AND ss.superceded = 0 AND ss.statuscode >= " . MDL_F2F_STATUS_BOOKED . ")
                    WHERE 1=1
                    GROUP BY s.id)",

                'cntbookings.sessionid = sessions.id',
                REPORT_BUILDER_RELATION_ONE_TO_ONE,
                'sessions'
            ),
        );

        $this->add_course_table_to_joinlist($joinlist, 'facetoface', 'course');
        $this->add_course_category_table_to_joinlist($joinlist, 'course', 'category');
        $this->add_position_tables_to_joinlist($joinlist, 'attendees', 'userid');
        $this->add_user_table_to_joinlist($joinlist, 'attendees', 'userid');
        $this->add_facetoface_session_roles_to_joinlist($joinlist, 'sessions.id');

        return $joinlist;
    }

    function define_columnoptions() {
        global $CFG;
        $intimezone = '';
        if (!empty($CFG->facetoface_displaysessiontimezones)) {
            $intimezone = '_in_timezone';
        }

        $now = time();
        $columnoptions = array(
            new rb_column_option(
                'session',
                'capacity',
                get_string('sesscapacity', 'rb_source_facetoface_sessions'),
                'sessions.capacity',
                array('joins' => 'sessions', 'dbdatatype' => 'integer')
            ),
            new rb_column_option(
                'session',
                'numattendees',
                get_string('numattendees', 'rb_source_facetoface_sessions'),
                '(CASE WHEN attendees.statuscode >= ' . MDL_F2F_STATUS_BOOKED . ' THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'numattendeeslink',
                get_string('numattendeeslink', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode >= ' . MDL_F2F_STATUS_BOOKED . ' THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'numattendeeslink',
                    'defaultheading' => get_string('numattendees', 'rb_source_facetoface_sessions'),
                    'extrafields' => array(
                        'session' => 'sessions.id'
                    )
                )
            ),
            new rb_column_option(
                'session',
                'totalnumattendees',
                get_string('totalnumattendees', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode >= ' . MDL_F2F_STATUS_REQUESTED . ' THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'waitlistattendees',
                get_string('waitlistattendees', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode = ' . MDL_F2F_STATUS_WAITLISTED . ' THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'numspaces',
                get_string('numspaces', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode >= ' . MDL_F2F_STATUS_APPROVED . ' THEN 1 ELSE NULL END)',
                array('joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'displayfunc' => 'session_spaces',
                    'extrafields' => array('overall_capacity' => 'sessions.capacity'),
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'cancelledattendees',
                get_string('cancelledattendees', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode IN (' . MDL_F2F_STATUS_USER_CANCELLED . ', ' . MDL_F2F_STATUS_SESSION_CANCELLED . ') THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'fullyattended',
                get_string('fullyattended', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode = ' . MDL_F2F_STATUS_FULLY_ATTENDED . ' THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'partiallyattended',
                get_string('partiallyattended', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode = ' . MDL_F2F_STATUS_PARTIALLY_ATTENDED . ' THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'noshowattendees',
                get_string('noshowattendees', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode = ' . MDL_F2F_STATUS_NO_SHOW . ' THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'declinedattendees',
                get_string('declinedattendees', 'rb_source_facetoface_summary'),
                '(CASE WHEN attendees.statuscode = ' . MDL_F2F_STATUS_DECLINED . ' THEN 1 ELSE NULL END)',
                array(
                    'joins' => array('attendees', 'sessions'),
                    'grouping' => 'count',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'session',
                'details',
                get_string('sessdetails', 'rb_source_facetoface_sessions'),
                'sessions.details',
                array('joins' => 'sessions')
            ),
            new rb_column_option(
                'session',
                'overbookingallowed',
                get_string('overbookingallowed', 'rb_source_facetoface_summary'),
                'sessions.allowoverbook',
                array(
                    'joins' => 'sessions',
                    'displayfunc' => 'yes_or_no'
                )
            ),
            // TODO: TL-8187 Cancellation status ("Face-to-face cancellations" specification).
            new rb_column_option(
                'session',
                'overallstatus',
                get_string('overallstatus', 'rb_source_facetoface_summary'),
                "( CASE WHEN datetimeknown = 0 OR timestart IS NULL OR timestart = 0 OR timestart > {$now} THEN 'upcoming'
                        WHEN {$now} > timestart AND {$now} < timefinish THEN 'started'
                        WHEN {$now} > timefinish THEN 'ended'
                        ELSE NULL END
                 )",
                array(
                    'joins' => 'sessions',
                    'displayfunc' => 'overallstatus',
                    'extrafields' => array(
                        'timestart' => 'base.timestart',
                        'timefinish' => 'base.timefinish',
                        'timezone' => 'base.sessiontimezone',
                        'datetimeknown' => 'sessions.datetimeknown'
                    )
                )
            ),
            new rb_column_option(
                'session',
                'bookingstatus',
                get_string('bookingstatus', 'rb_source_facetoface_summary'),
                "(CASE WHEN cntsignups < sessions.mincapacity THEN 'underbooked'
                       WHEN cntsignups < sessions.capacity THEN 'available'
                       WHEN cntsignups = sessions.capacity THEN 'fullybooked'
                       WHEN cntsignups > sessions.capacity THEN 'overbooked'
                       ELSE NULL END)",
                array(
                    'joins' => array('cntbookings', 'sessions'),
                    'displayfunc' => 'bookingstatus',
                    'dbdatatype' => 'char',
                    'extrafields' => array(
                        'mincapacity' => 'sessions.mincapacity',
                        'capacity' => 'sessions.capacity'
                    )
                )
            ),
            new rb_column_option(
                'facetoface',
                'name',
                get_string('ftfname', 'rb_source_facetoface_sessions'),
                'facetoface.name',
                array(
                    'joins' => 'facetoface'
                )
            ),
            new rb_column_option(
                'facetoface',
                'namelink',
                get_string('ftfnamelink', 'rb_source_facetoface_sessions'),
                "facetoface.name",
                array(
                    'joins' => array('facetoface', 'sessions'),
                    'displayfunc' => 'link_f2f',
                    'defaultheading' => get_string('ftfname', 'rb_source_facetoface_sessions'),
                    'extrafields' => array('activity_id' => 'sessions.facetoface'),
                )
            ),
            new rb_column_option(
                'facetoface',
                'intro',
                get_string('f2fdesc', 'rb_source_facetoface_summary'),
                'facetoface.intro',
                array(
                    'joins' => 'facetoface'
                )
            ),
            new rb_column_option(
                'facetoface',
                'facetofaceid',
                get_string('f2fid', 'rb_source_facetoface_summary'),
                'facetoface.id',
                array(
                    'joins' => 'facetoface',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'facetoface',
                'sessionid',
                get_string('sessionid', 'rb_source_facetoface_summary'),
                'sessions.id',
                array(
                    'joins' => 'sessions',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'facetoface',
                'normalcost',
                get_string('normalcost', 'rb_source_facetoface_summary'),
                'sessions.normalcost',
                array(
                    'joins' => 'sessions',
                    'dbdatatype' => 'decimal'
                )
            ),
            new rb_column_option(
                'facetoface',
                'discountcost',
                get_string('discountcost', 'rb_source_facetoface_summary'),
                'sessions.discountcost',
                array(
                    'joins' => 'sessions',
                    'dbdatatype' => 'decimal'
                )
            ),
            new rb_column_option(
                'facetoface',
                'duration',
                get_string('duration', 'rb_source_facetoface_summary'),
                'sessions.duration',
                array(
                    'joins' => 'sessions',
                    'dbdatatype' => 'integer',
                    'displayfunc' => 'duration'
                )
            ),
            new rb_column_option(
                'facetoface',
                'minbookings',
                get_string('minbookings', 'rb_source_facetoface_summary'),
                'sessions.mincapacity',
                array(
                    'joins' => 'sessions',
                    'dbdatatype' => 'integer'
                )
            ),
            new rb_column_option(
                'facetoface',
                'approvaltype',
                get_string('f2f_approvaltype', 'rb_source_facetoface_summary'),
                "facetoface.approvaltype",
                array(
                    'joins' => 'facetoface',
                    'displayfunc' => 'f2f_approval',
                    'defaultheading' => get_string('approvaltype', 'rb_source_facetoface_sessions'),
                    'extrafields' => array(
                        'approvalrole' => 'facetoface.approvalrole'
                    )
                )
            ),
            new rb_column_option(
                'date',
                'sessiondate',
                get_string('sessdatetime', 'rb_source_facetoface_summary'),
                '(CASE WHEN sessions.datetimeknown > 0 THEN base.timestart ELSE NULL END)',
                array(
                    'joins' => 'sessions',
                    'extrafields' => array(
                        'timezone' => 'base.sessiontimezone',
                        'datetimeknown' => 'sessions.datetimeknown'
                    ),
                    'displayfunc' => 'nice_date' . $intimezone,
                    'dbdatatype' => 'timestamp'
                )
            ),
            new rb_column_option(
                'date',
                'sessiondate_link',
                get_string('sessdatetimelink', 'rb_source_facetoface_summary'),
                '(CASE WHEN sessions.datetimeknown > 0 THEN base.timestart ELSE NULL END)',
                array(
                    'joins' => 'sessions',
                    'extrafields' => array(
                        'session_id' => 'sessions.id',
                        'timezone' => 'base.sessiontimezone',
                        'datetimeknown' => 'sessions.datetimeknown'
                    ),
                    'defaultheading' => get_string('sessdatetime', 'rb_source_facetoface_summary'),
                    'displayfunc' => 'link_f2f_session' . $intimezone,
                    'dbdatatype' => 'timestamp'
                )
            ),
        );

        $this->add_facetoface_session_roles_to_columns($columnoptions);

        // Include some standard columns.
        $this->add_course_category_fields_to_columns($columnoptions);
        $this->add_course_fields_to_columns($columnoptions);

        return $columnoptions;
    }

    protected function define_filteroptions() {
        $filteroptions = array(
            new rb_filter_option(
                'facetoface',
                'name',
                get_string('ftfname', 'rb_source_facetoface_sessions'),
                'text'
            ),
            new rb_filter_option(
                'date',
                'sessiondate',
                get_string('sessdate', 'rb_source_facetoface_sessions'),
                'date'
            ),
            new rb_filter_option(
                'session',
                'bookingstatus',
                get_string('bookingstatus', 'rb_source_facetoface_summary'),
                'select',
                array(
                    'selectchoices' => self::get_bookingstatus_options(),
                )
            ),
            new rb_filter_option(
                'session',
                'overallstatus',
                get_string('overallstatus', 'rb_source_facetoface_summary'),
                'select',
                array(
                    'selectfunc' => 'overallstatus',
                )
            ),
        );

        $this->add_facetoface_session_role_fields_to_filters($filteroptions);

        // Add session custom fields to filters.
        $this->add_course_category_fields_to_filters($filteroptions);
        $this->add_course_fields_to_filters($filteroptions);

        return $filteroptions;
    }

    protected function define_contentoptions() {
        $contentoptions = array(
            new rb_content_option(
                'current_org',                      // class name
                get_string('currentorg', 'rb_source_facetoface_sessions'),  // title
                'organisation.path',                // field
                'organisation'                      // joins
            ),
            new rb_content_option(
                'user',
                get_string('user', 'rb_source_facetoface_sessions'),
                array(
                    'userid' => 'attendees.userid',
                    'managerid' => 'position_assignment.managerid',
                    'managerpath' => 'position_assignment.managerpath',
                    'postype' => 'position_assignment.type',
                ),
                array('attendees', 'position_assignment')
            ),
        );
        return $contentoptions;
    }

    protected function define_defaultcolumns() {
        $defaultcolumns = array(
            array(
                'type' => 'course',
                'value' => 'fullname',
            ),
            array(
                'type' => 'facetoface',
                'value' => 'namelink',
            ),
        );

        return $defaultcolumns;
    }

    /**
     * Convert a f2f date into a link to that session with timezone.
     *
     * @param string $date Date of session
     * @param object $row Report row
     * @param bool $isexport
     * @return string Display html
     */
    function rb_display_link_f2f_session_in_timezone($date, $row, $isexport = false) {
        global $OUTPUT;
        $sessionid = $row->session_id;
        if ($date && is_numeric($date)) {
            $date = $this->rb_display_nice_datetime_in_timezone($date, $row);
            if ($isexport) {
                return $date;
            }
            return $OUTPUT->action_link(new moodle_url('/mod/facetoface/attendees.php', array('s' => $sessionid)), $date);
        } else {
            $unknownstr = get_string('unknowndate', 'rb_source_facetoface_summary');
             if ($isexport) {
                return $unknownstr;
            }
            return $OUTPUT->action_link(new moodle_url('/mod/facetoface/attendees.php', array('s' => $sessionid)), $unknownstr);
        }
    }

    /**
     * Convert a f2f approvaltype into a human readable string
     *
     * @param int $approvaltype
     * @param object $row
     * @return string
     */
    function rb_display_f2f_approval($approvaltype, $row) {
        return facetoface_get_approvaltype_string($approvaltype, $row->approvalrole);
    }

    /**
     * Spaces left on session.
     *
     * @param string $count Number of signups
     * @param object $row Report row
     * @return string Display html
     */
    function rb_display_session_spaces($count, $row) {
        $spaces = $row->overall_capacity - $count;
        return ($spaces > 0 ? $spaces : 0);
    }

    /**
     * Show if manager's approval required
     * @param bool $required True when approval required
     * @param stdClass $row
     */
    public function rb_display_approver($required, $row) {
        if ($required) {
            return get_string('manager', 'core_role');
        } else {
            return get_string('noone', 'rb_source_facetoface_summary');
        }
    }

    /**
     * Display booking status according number of bookings and capacities
     *
     * @param string $status
     * @param stdClass $row
     * @param bool $isexport
     * @return type
     */
    public function rb_display_bookingstatus($status, $row, $isexport = false){
        switch($status) {
            case 'underbooked':
                $str = get_string('status:underbooked', 'rb_source_facetoface_summary');
                $class = 'underbooked';
                break;
            case 'available':
                $str = get_string('status:available', 'rb_source_facetoface_summary');
            $class = 'available';
                break;
            case 'fullybooked':
                $str = get_string('status:fullybooked', 'rb_source_facetoface_summary');
                $class = 'fullybooked';
                break;
            case 'overbooked':
                $str = get_string('status:overbooked', 'rb_source_facetoface_summary');
                $class = 'overbooked';
                break;
            default:
                $str = get_string('status:notavailable', 'rb_source_facetoface_summary');
                $class = 'notavailable';
        }

        if ($isexport) {
            return $str;
        }
        return html_writer::div(html_writer::span($str), $class);
    }

    /**
     * Get currently supported booking status filter options
     * @return array
     */
    protected static function get_bookingstatus_options() {
        $statusopts = array(
            'underbooked' => get_string('status:underbooked', 'rb_source_facetoface_summary'),
            'available' => get_string('status:available', 'rb_source_facetoface_summary'),
            'fullybooked' => get_string('status:fullybooked', 'rb_source_facetoface_summary'),
            'overbooked' => get_string('status:overbooked', 'rb_source_facetoface_summary'),
        );
        return $statusopts;
    }

    /**
     * Display count of attendees and link to session attendees report page.
     *
     * @param int $cntattendees
     * @param stdClass $row
     * @param bool $isexport
     */
    public function rb_display_numattendeeslink($cntattendees, $row, $isexport = false) {
        if ($isexport) {
            return $cntattendees;
        }
        return html_writer::link(new moodle_url('/mod/facetoface/attendees.php', array('s' => $row->session)), $cntattendees);
    }

    /**
     * Display count of attendees and link to session attendees report page.
     *
     * @param string $status
     * @param stdClass $row
     * @param bool $isexport
     */
    public function rb_display_overallstatus($status, $row, $isexport = false) {
        switch($status) {
            case 'cancelled':
                $str = get_string('status:cancelled', 'rb_source_facetoface_summary');
                $class = 'cancelled';
                break;
            case 'upcoming':
                $str = get_string('status:upcoming', 'rb_source_facetoface_summary');
                $class = 'upcoming';
                break;
            case 'started':
                $str = get_string('status:started', 'rb_source_facetoface_summary');
                $class = 'started';
                break;
            case 'ended':
                $str = get_string('status:ended', 'rb_source_facetoface_summary');
                $class = 'ended';
                break;
            default:
                $str = get_string('status:notavailable', 'rb_source_facetoface_summary');
                $class = 'notavailable';
        }

        if ($isexport) {
            return $str;
        }
        return html_writer::div(html_writer::span($str), $class);
    }

    /**
     * Filter by session overall status
     * @return array of options
     */
    public function rb_filter_overallstatus() {
        $statusopts = array(
            'upcoming' => get_string('status:upcoming', 'rb_source_facetoface_summary'),
            // TODO: TL-8187 Uncomment when implemented according Session cancellations specification.
            //'cancelled' => get_string('status:cancelled', 'rb_source_facetoface_summary'),
            'started' => get_string('status:started', 'rb_source_facetoface_summary'),
            'ended' => get_string('status:ended', 'rb_source_facetoface_summary'),
        );
        return $statusopts;
    }

    /**
     * Required columns.
     */
    protected function define_requiredcolumns() {
        // Session_id is needed so when grouping we can keep the information grouped by sessions.
        // This is done to cover the case when we have several sessions which are identical.
        $requiredcolumns = array(
            new rb_column(
                'sessions',
                'id',
                '',
                "sessions.id",
                array('joins' => 'sessions')
            )
        );
        return $requiredcolumns;
    }

    protected function add_customfields() {
        $this->add_custom_fields_for('facetoface_session', 'sessions', 'facetofacesessionid', $this->joinlist, $this->columnoptions, $this->filteroptions);
    }
}
