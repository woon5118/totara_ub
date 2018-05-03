<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Alastair Munro <alastair.munro@totaralearning.com>
 * @package totara_job
 */

namespace totara_job\rb\source;

defined('MOODLE_INTERNAL') || die();

trait report_trait {

    /**
     * Adds the job_assignment, pos and org tables to the $joinlist array. All job assignments belonging to the user are returned.
     *
     * @param array &$joinlist Array of current join options
     *                         Passed by reference and updated to
     *                         include new table joins
     * @param string $join Name of the join that provides the 'user' table
     * @param string $field Name of user id field to join on
     * @param string $jointype Type of join. For performance reasons it's better to use INNER, but only use INNER if the
     *                         report base doesn't have rows with invalid or NULL values for userid (=$join.$field),
     *                         because these rows would be dropped.
     * @return boolean True
     */
    protected function add_totara_job_tables(&$joinlist, $join, $field, $jointype = 'LEFT') {
        global $DB;

        // All job fields listed by sortorder.
        $jobfieldlistsubsql = "
            (SELECT u.id AS jfid, COUNT(uja.id) AS jobcount,
            " . $DB->sql_group_concat('COALESCE(uja.fullname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS titlenamelist,
            " . $DB->sql_group_concat('COALESCE(uja.startdate, \'0\')', $this->uniquedelimiter, 'uja.sortorder') . " AS jobstartdatelist,
            " . $DB->sql_group_concat('COALESCE(uja.enddate, \'0\')', $this->uniquedelimiter, 'uja.sortorder') . " AS jobenddatelist
               FROM {user} u
          LEFT JOIN {job_assignment} uja
                 ON uja.userid = u.id
           GROUP BY u.id)";

        $joinlist[] = new \rb_join(
            'alljobfields',
            $jointype,
            $jobfieldlistsubsql,
            "alljobfields.jfid = {$join}.{$field}",
            REPORT_BUILDER_RELATION_ONE_TO_MANY,
            $join
        );

        // All manager fields listed by job sortorder.
        $usednamefields = totara_get_all_user_name_fields_join('manager', null, true);
        $manlistsubsql = "
            (SELECT u.id AS manlistid,
            " . $DB->sql_group_concat($DB->sql_concat_join("' '", $usednamefields), $this->uniquedelimiter, 'uja.sortorder') . " AS manfullnamelist,
            " . $DB->sql_group_concat('COALESCE(manager.firstname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS manfirstnamelist,
            " . $DB->sql_group_concat('COALESCE(manager.lastname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS manlastnamelist,
            " . $DB->sql_group_concat('COALESCE(manager.idnumber, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS manidnumberlist,
            " . $DB->sql_group_concat('COALESCE(manager.id, \'0\')', $this->uniquedelimiter, 'uja.sortorder') . " AS manidlist,
            " . $DB->sql_group_concat('COALESCE(CASE WHEN manager.maildisplay <> 1 THEN \'!private!\' ELSE manager.email END, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS manemailobslist,
            " . $DB->sql_group_concat('COALESCE(manager.email, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS manemailunobslist

                FROM {user} u
           LEFT JOIN {job_assignment} uja
                  ON uja.userid = u.id
           LEFT JOIN {job_assignment} mja
                  ON mja.id = uja.managerjaid
           LEFT JOIN {user} manager
                  ON mja.userid = manager.id
            GROUP BY u.id)";

        $joinlist[] = new \rb_join(
            'manallfields',
            $jointype,
            $manlistsubsql,
            "manallfields.manlistid = {$join}.{$field}",
            REPORT_BUILDER_RELATION_ONE_TO_MANY,
            $join
        );

        // All position fields listed by job sortorder.
        $poslistsubsql = "
            (SELECT u.id AS poslistid,
            " . $DB->sql_group_concat('COALESCE(position.id, \'0\')', $this->uniquedelimiter, 'uja.sortorder') . " AS posidlist,
            " . $DB->sql_group_concat('COALESCE(position.idnumber, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS posidnumberlist,
            " . $DB->sql_group_concat('COALESCE(position.fullname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS posnamelist,
            " . $DB->sql_group_concat('COALESCE(ptype.fullname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS ptypenamelist,
            " . $DB->sql_group_concat('COALESCE(pframe.id, \'0\')', $this->uniquedelimiter, 'uja.sortorder') . " AS pframeidlist,
            " . $DB->sql_group_concat('COALESCE(pframe.idnumber, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS pframeidnumberlist,
            " . $DB->sql_group_concat('COALESCE(pframe.fullname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS pframenamelist
                FROM {user} u
           LEFT JOIN {job_assignment} uja
                  ON uja.userid = u.id
           LEFT JOIN {pos} position
                  ON uja.positionid = position.id
           LEFT JOIN {pos_type} ptype
                  ON position.typeid = ptype.id
           LEFT JOIN {pos_framework} pframe
                  ON position.frameworkid = pframe.id
            GROUP BY u.id)";

        $joinlist[] = new \rb_join(
            'posallfields',
            $jointype,
            $poslistsubsql,
            "posallfields.poslistid = {$join}.{$field}",
            REPORT_BUILDER_RELATION_ONE_TO_MANY,
            $join
        );

        // List of all assigned organisation names.
        $orglistsubsql = "
            (SELECT u.id AS orglistid,
            " . $DB->sql_group_concat('COALESCE(organisation.id, \'0\')', $this->uniquedelimiter, 'uja.sortorder') . " AS orgidlist,
            " . $DB->sql_group_concat('COALESCE(organisation.idnumber, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS orgidnumberlist,
            " . $DB->sql_group_concat('COALESCE(organisation.fullname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS orgnamelist,
            " . $DB->sql_group_concat('COALESCE(otype.fullname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS otypenamelist,
            " . $DB->sql_group_concat('COALESCE(oframe.id, \'0\')', $this->uniquedelimiter, 'uja.sortorder') . " AS oframeidlist,
            " . $DB->sql_group_concat('COALESCE(oframe.idnumber, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS oframeidnumberlist,
            " . $DB->sql_group_concat('COALESCE(oframe.fullname, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS oframenamelist
                FROM {user} u
           LEFT JOIN {job_assignment} uja
                  ON uja.userid = u.id
           LEFT JOIN {org} organisation
                  ON uja.organisationid = organisation.id
           LEFT JOIN {org_type} otype
                  ON organisation.typeid = otype.id
           LEFT JOIN {org_framework} oframe
                  ON organisation.frameworkid = oframe.id
            GROUP BY u.id)";

        $joinlist[] = new \rb_join(
            'orgallfields',
            $jointype,
            $orglistsubsql,
            "orgallfields.orglistid = {$join}.{$field}",
            REPORT_BUILDER_RELATION_ONE_TO_MANY,
            $join
        );

        // List of all assigned appraiser names.
        $usednamefields = totara_get_all_user_name_fields_join('appraiser', null, true);
        $applistsubsql = "
            (SELECT u.id AS applistid,
            " . $DB->sql_group_concat($DB->sql_concat_join("' '", $usednamefields), $this->uniquedelimiter, 'uja.sortorder') . " AS appfullnamelist
                FROM {user} u
           LEFT JOIN {job_assignment} uja
                  ON uja.userid = u.id
           LEFT JOIN {user} appraiser
                  ON uja.appraiserid = appraiser.id
            GROUP BY u.id)";

        $joinlist[] = new \rb_join(
            'appallfields',
            $jointype,
            $applistsubsql,
            "appallfields.applistid = {$join}.{$field}",
            REPORT_BUILDER_RELATION_ONE_TO_MANY,
            $join
        );

        // Set up the position and organisation custom field joins.
        $posfields = $DB->get_records('pos_type_info_field', array('hidden' => '0'));
        $this->add_totara_job_custom_field_tables('pos', $posfields, $join, $field, $joinlist);

        $orgfields = $DB->get_records('org_type_info_field', array('hidden' => '0'));
        $this->add_totara_job_custom_field_tables('org', $orgfields, $join, $field, $joinlist);

        return true;
    }

    /**
     * Adds some common user manager info to the $columnoptions array,
     * assumes that the joins from add_totara_job_tables have been
     * added to the source.
     *
     * @param array &$columnoptions Array of current column options
     *                              Passed by reference and updated by
     *                              this method
     * @return True
     */
    protected function add_totara_job_columns(&$columnoptions) {
        global $CFG, $DB;

        // Job assignment field columns.
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'alltitlenames',
            get_string('usersjobtitlenameall', 'totara_reportbuilder'),
            "alljobfields.titlenamelist",
            array(
                'joins' => 'alljobfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );

        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allstartdates',
            get_string('usersjobstartdateall', 'totara_reportbuilder'),
            "alljobfields.jobstartdatelist",
            array(
                'joins' => 'alljobfields',
                'displayfunc' => 'orderedlist_to_newline_date',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );

        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allenddates',
            get_string('usersjobenddateall', 'totara_reportbuilder'),
            "alljobfields.jobenddatelist",
            array(
                'joins' => 'alljobfields',
                'displayfunc' => 'orderedlist_to_newline_date',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'numjobassignments',
            get_string('usersnumjobassignments', 'totara_reportbuilder'),
            "alljobfields.jobcount",
            array(
                'joins'       => 'alljobfields',
                'displayfunc' => 'plaintext', // We know we will have an integer here, so minimum cleaning needed.
                'iscompound'  => true,
                'dbdatatype'  => 'integer',
            )
        );

        // Position field columns.
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allpositionnames',
            get_string('usersposnameall', 'totara_reportbuilder'),
            "posallfields.posnamelist",
            array(
                'joins' => 'posallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allpositionids',
            get_string('usersposidall', 'totara_reportbuilder'),
            "posallfields.posidlist",
            array(
                'joins' => 'posallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allpositionidnumbers',
            get_string('usersposidnumberall', 'totara_reportbuilder'),
            "posallfields.posidnumberlist",
            array(
                'joins' => 'posallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allpositiontypes',
            get_string('userspostypeall', 'totara_reportbuilder'),
            "posallfields.ptypenamelist",
            array(
                'joins' => 'posallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allposframenames',
            get_string('usersposframenameall', 'totara_reportbuilder'),
            "posallfields.pframenamelist",
            array(
                'joins' => 'posallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allposframeids',
            get_string('usersposframeidall', 'totara_reportbuilder'),
            "posallfields.pframeidlist",
            array(
                'joins' => 'posallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allposframeidnumbers',
            get_string('usersposframeidnumberall', 'totara_reportbuilder'),
            "posallfields.pframeidnumberlist",
            array(
                'joins' => 'posallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );

        // Organisation field columns.
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allorganisationnames',
            get_string('usersorgnameall', 'totara_reportbuilder'),
            "orgallfields.orgnamelist",
            array(
                'joins' => 'orgallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allorganisationids',
            get_string('usersorgidall', 'totara_reportbuilder'),
            "orgallfields.orgidlist",
            array(
                'joins' => 'orgallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allorganisationidnumbers',
            get_string('usersorgidnumberall', 'totara_reportbuilder'),
            "orgallfields.orgidnumberlist",
            array(
                'joins' => 'orgallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allorganisationtypes',
            get_string('usersorgtypeall', 'totara_reportbuilder'),
            "orgallfields.otypenamelist",
            array(
                'joins' => 'orgallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allorgframenames',
            get_string('usersorgframenameall', 'totara_reportbuilder'),
            "orgallfields.oframenamelist",
            array(
                'joins' => 'orgallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allorgframeids',
            get_string('usersorgframeidall', 'totara_reportbuilder'),
            "orgallfields.oframeidlist",
            array(
                'joins' => 'orgallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allorgframeidnumbers',
            get_string('usersorgframeidnumberall', 'totara_reportbuilder'),
            "orgallfields.oframeidnumberlist",
            array(
                'joins' => 'orgallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );

        // Manager field columns.
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allmanagernames',
            get_string('usersmanagernameall', 'totara_reportbuilder'),
            "manallfields.manfullnamelist",
            array(
                'joins' => 'manallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allmanagerfirstnames',
            get_string('usersmanagerfirstnameall', 'totara_reportbuilder'),
            "manallfields.manfirstnamelist",
            array(
                'joins' => 'manallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allmanagerlastnames',
            get_string('usersmanagerlastnameall', 'totara_reportbuilder'),
            "manallfields.manlastnamelist",
            array(
                'joins' => 'manallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allmanagerids',
            get_string('usersmanageridall', 'totara_reportbuilder'),
            "manallfields.manidlist",
            array(
                'joins' => 'manallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allmanageridnumbers',
            get_string('usersmanageridnumberall', 'totara_reportbuilder'),
            "manallfields.manidnumberlist",
            array(
                'joins' => 'manallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );

        // Managers unobscured emails.
        $canview = !empty($CFG->showuseridentity) && in_array('email', explode(',', $CFG->showuseridentity));
        $canview |= has_capability('moodle/site:config', \context_system::instance());
        if ($canview) {
            $columnoptions[] = new \rb_column_option(
                'job_assignment',
                'allmanagerunobsemails',
                get_string('usersmanagerunobsemailall', 'totara_reportbuilder'),
                "manallfields.manemailunobslist",
                array(
                    'joins' => 'manallfields',
                    'displayfunc' => 'orderedlist_to_newline_email',
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                    'nosort' => true,
                    'style' => array('white-space' => 'pre'),
                    // Users must have viewuseridentity.
                    'capability' => 'moodle/site:viewuseridentity',
                    'iscompound' => true,
                )
            );
        }
        // Managers obscured emails.
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allmanagerobsemails',
            get_string('usersmanagerobsemailall', 'totara_reportbuilder'),
            "manallfields.manemailobslist",
            array(
                'joins' => 'manallfields',
                'displayfunc' => 'orderedlist_to_newline_email',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );

        // Appraiser field columns.
        $columnoptions[] = new \rb_column_option(
            'job_assignment',
            'allappraisernames',
            get_string('usersappraisernameall', 'totara_reportbuilder'),
            "appallfields.appfullnamelist",
            array(
                'joins' => 'appallfields',
                'displayfunc' => 'orderedlist_to_newline',
                'dbdatatype' => 'char',
                'outputformat' => 'text',
                'nosort' => true,
                'iscompound' => true,
                'style' => array('white-space' => 'pre')
            )
        );

        // Set up the position and organisation custom field columns.
        $posfields = $DB->get_records('pos_type_info_field', array('hidden' => '0'));
        $this->add_totara_job_custom_field_columns('pos', $posfields, $columnoptions);

        $orgfields = $DB->get_records('org_type_info_field', array('hidden' => '0'));
        $this->add_totara_job_custom_field_columns('org', $orgfields, $columnoptions);

        return true;
    }

    /**
     * Adds some common user position filters to the $filteroptions array
     *
     * @param array &$filteroptions Array of current filter options
     *                              Passed by reference and updated by
     *                              this method
     * @param string $userjoin Table name to join to which has the user's id
     * @param string $userfield Field name containing the user's id
     * @return True
     */
    protected function add_totara_job_filters(&$filteroptions, $userjoin = 'auser', $userfield = 'id') {
        global $DB, $CFG;

        // Job assignment field filters.
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',                                           // type
            'alltitlenames',                                            // value
            get_string('jobassign_jobtitle', 'totara_reportbuilder'),   // label
            'text'                                                      // filtertype
        );

        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allstartdatesfilter',
            get_string('jobassign_jobstart', 'totara_reportbuilder'),
            'grpconcat_date',
            array(
                'prefix' => 'job',
                'datefield' => 'startdate',
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );

        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allenddatesfilter',
            get_string('jobassign_jobend', 'totara_reportbuilder'),
            'grpconcat_date',
            array(
                'prefix' => 'job',
                'datefield' => 'enddate',
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );

        // Position field filters.
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allpositions',
            get_string('usersposall', 'totara_reportbuilder'),
            'grpconcat_jobassignment',
            array(
                'hierarchytype' => 'pos',
                'jobfield' => 'positionid',                                 // Jobfield, map to the column in the job_assignments table.
                'jobjoin' => 'pos',                                         // The table that the job join information can be found in.
            ),
            "{$userjoin}.{$userfield}",                                                  // $field
            $userjoin                                                          // $joins string | array
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allpositionnamesfilter',
            get_string('usersposnameall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'p.fullname',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {pos} p ON p.id = ja.positionid
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allpositionidnumbersfilter',
            get_string('usersposidnumberall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'p.idnumber',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {pos} p ON p.id = ja.positionid
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allpositiontypesfilter',
            get_string('userspostypeall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'pt.fullname',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {pos} p ON p.id = ja.positionid
                                        JOIN {pos_type} pt ON p.typeid = pt.id
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allposframeidsfilter',
            get_string('usersposframeidall', 'totara_reportbuilder'),
            'correlated_subquery_number',
            array(
                'searchfield' => 'pf.id',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {pos} p ON p.id = ja.positionid
                                        JOIN {pos_framework} pf ON p.frameworkid = pf.id
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allposframenamesfilter',
            get_string('usersposframenameall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'pf.fullname',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {pos} p ON p.id = ja.positionid
                                        JOIN {pos_framework} pf ON p.frameworkid = pf.id
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allposframeidnumbersfilter',
            get_string('usersposframeidnumberall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'pf.idnumber',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {pos} p ON p.id = ja.positionid
                                        JOIN {pos_framework} pf ON p.frameworkid = pf.id
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );

        // Organisation field filters.
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allorganisations',
            get_string('usersorgall', 'totara_reportbuilder'),
            'grpconcat_jobassignment',
            array(
                'hierarchytype' => 'org',
                'jobfield' => 'organisationid',                             // Jobfield, map to the column in the job_assignments table.
                'jobjoin' => 'org',                                         // The table that the job join information can be found in.
            ),
            "{$userjoin}.{$userfield}",                                                  // $field
            $userjoin                                                          // $joins string | array
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allorganisationnamesfilter',
            get_string('usersorgnameall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'o.fullname',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {org} o ON o.id = ja.organisationid
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allorganisationidnumbersfilter',
            get_string('usersorgidnumberall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'o.idnumber',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {org} o ON o.id = ja.organisationid
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allorganisationtypesfilter',
            get_string('usersorgtypeall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'ot.fullname',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {org} o ON o.id = ja.organisationid
                                        JOIN {org_type} ot ON o.typeid = ot.id
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allorgframeidsfilter',
            get_string('usersorgframeidall', 'totara_reportbuilder'),
            'correlated_subquery_number',
            array(
                'searchfield' => 'of.id',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {org} o ON o.id = ja.organisationid
                                        JOIN {org_framework} of ON o.frameworkid = of.id
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allorgframenamesfilter',
            get_string('usersorgframenameall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'of.fullname',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {org} o ON o.id = ja.organisationid
                                        JOIN {org_framework} of ON o.frameworkid = of.id
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allorgframeidnumbersfilter',
            get_string('usersorgframeidnumberall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'of.idnumber',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {org} o ON o.id = ja.organisationid
                                        JOIN {org_framework} of ON o.frameworkid = of.id
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );

        // Manager field filters.
        // Manager field filters (Limits the number of selected managers in the report-builder JA filter)
        // We use this limit to prevent generating enormous query when applying the filter.
        // Each additional selected manager adds AND EXISTS (SELECT ... JOIN ...) and this when getting to
        // large number of managers get some mysql derivatives confused as well as MS SQL server.
        $selectionlimit = isset($CFG->totara_reportbuilder_filter_selected_managers_limit)
            ? intval($CFG->totara_reportbuilder_filter_selected_managers_limit) : 25;

        $filteroptionoptions = [
            'jobfield' => 'managerjaid',   // Jobfield, map to the column in the job_assignments table.
            'jobjoin' => 'user',           // The table that the job join information can be found in.
            'extfield' => 'userid',        // Extfield, this overrides the jobfield as the select after joining.
            'extjoin' => 'job_assignment', // Extjoin, whether an additional join is required.
        ];

        // Setting manager filter selection limit
        if ($selectionlimit > 0) {
            $filteroptionoptions['selectionlimit'] = $selectionlimit;
        }

        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allmanagers',
            get_string('usersmanagerall', 'totara_reportbuilder'),
            'grpconcat_jobassignment',
            $filteroptionoptions,
            "{$userjoin}.{$userfield}",                                                  // $field
            $userjoin                                                          // $joins string | array
        );
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allmanageridnumbersfilter',
            get_string('usersmanageridnumberall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'u.idnumber',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {job_assignment} mja ON mja.id = ja.managerjaid
                                        JOIN {user} u ON u.id = mja.userid
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );
        $canview = !empty($CFG->showuseridentity) && in_array('email', explode(',', $CFG->showuseridentity));
        $canview |= has_capability('moodle/site:config', \context_system::instance());
        if ($canview) {
            $filteroptions[] = new \rb_filter_option(
                'job_assignment',
                'allmanagerunobsemailsfilter',
                get_string('usersmanagerunobsemailall', 'totara_reportbuilder'),
                'correlated_subquery_text',
                array(
                    'searchfield' => 'u.email',
                    'subquery' => "EXISTS(SELECT 'x'
                                            FROM {job_assignment} ja
                                            JOIN {job_assignment} mja ON mja.id = ja.managerjaid
                                            JOIN {user} u ON u.id = mja.userid
                                           WHERE ja.userid = (%1\$s) AND (%2\$s) )",
                ),
                "{$userjoin}.{$userfield}",
                $userjoin
            );
        }
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allmanagerobsemailsfilter',
            get_string('usersmanagerobsemailall', 'totara_reportbuilder'),
            'correlated_subquery_text',
            array(
                'searchfield' => 'u.email',
                'subquery' => "EXISTS(SELECT 'x'
                                        FROM {job_assignment} ja
                                        JOIN {job_assignment} mja ON mja.id = ja.managerjaid
                                        JOIN {user} u ON u.id = mja.userid
                                       WHERE ja.userid = (%1\$s) AND (%2\$s) )",
            ),
            "{$userjoin}.{$userfield}",
            $userjoin
        );

        // Appraiser field filters.
        $filteroptions[] = new \rb_filter_option(
            'job_assignment',
            'allappraisers',
            get_string('jobassign_appraiser', 'totara_reportbuilder'),
            'grpconcat_jobassignment',
            array(
                'jobfield' => 'appraiserid',                                // Jobfield, map to the column in the job_assignments table.
                'jobjoin' => 'user',                                        // The table that the job join information can be found in.
            ),
            "{$userjoin}.{$userfield}",                                                  // $field
            $userjoin                                                          // $joins string | array
        );

        // Set up the position and organisation custom field filters.
        $posfields = $DB->get_records('pos_type_info_field', array('hidden' => '0'));
        $this->add_totara_job_custom_field_filters('pos', $posfields, $filteroptions, $userjoin, $userfield);

        $orgfields = $DB->get_records('org_type_info_field', array('hidden' => '0'));
        $this->add_totara_job_custom_field_filters('org', $orgfields, $filteroptions, $userjoin, $userfield);

        return true;
    }

    /**
     * Adds the joins for pos/org custom fields to the $joinlist.
     *
     * @param string $prefix    Whether this is a pos/org
     * @param string $fields    The fields that need to be joined
     * @param string $join      The table to take the userid from
     * @param string $joinfield The field to take the userid from
     * @param array  $joinlist
     *
     * @return bool
     */
    private function add_totara_job_custom_field_tables($prefix, $fields, $join, $joinfield, &$joinlist) {
        global $DB;

        // We need a join for each custom field to get them concatenating.
        foreach ($fields as $field) {
            $uniquename = "{$prefix}_custom_{$field->id}";
            $idfield = $prefix == 'pos' ? 'positionid' : 'organisationid';

            switch ($field->datatype) {
                case 'date' :
                case 'datetime' :
                case 'checkbox' :
                case 'text' :
                case 'menu' :
                case 'url' :
                case 'location' :
                case 'file' :
                    break;
                case 'textarea' :
                    // Not yet supported
                    continue(2);
            }

            $customsubsql = "
                (SELECT uja.userid AS customlistid,
                " . $DB->sql_group_concat('COALESCE(otdata.data, \'-\')', $this->uniquedelimiter, 'uja.sortorder') . " AS {$uniquename}
                    FROM {job_assignment} uja
               LEFT JOIN {{$prefix}} item
                      ON uja.{$idfield} = item.id
               LEFT JOIN {{$prefix}_type_info_field} otfield
                      ON item.typeid = otfield.typeid
                     AND otfield.id = {$field->id}
               LEFT JOIN {{$prefix}_type_info_data} otdata
                      ON otdata.fieldid = otfield.id
                     AND otdata.{$idfield} = item.id
                GROUP BY uja.userid)";

            $joinlist[] = new \rb_join(
                $uniquename,
                'LEFT',
                $customsubsql,
                "{$uniquename}.customlistid = {$join}.{$joinfield}",
                REPORT_BUILDER_RELATION_ONE_TO_MANY,
                $join
            );
        }

        return true;
    }

    private function add_totara_job_custom_field_columns($prefix, $fields, &$columnoptions) {

        foreach ($fields as $field) {
            $uniquename = "{$prefix}_custom_{$field->id}";

            switch ($field->datatype) {
                case 'datetime' :
                    $displayfunc = $field->param3 ? 'delimitedlist_datetime_in_timezone' : 'delimitedlist_date_in_timezone';
                    break;
                case 'checkbox' :
                    $displayfunc = 'delimitedlist_yes_no';
                    break;
                case 'text' :
                    $displayfunc = 'delimitedlist_to_newline';
                    break;
                case 'menu' :
                    $displayfunc = 'delimitedlist_to_newline';
                    break;
                case 'multiselect' :
                    $displayfunc = 'delimitedlist_multi_to_newline';
                    break;
                case 'url' :
                    $displayfunc = 'delimitedlist_url_to_newline';
                    break;
                case 'location' :
                    $displayfunc = 'delimitedlist_location_to_newline';
                    break;
                case 'file' :
                    $displayfunc = "delimitedlist_{$prefix}files_to_newline";
                    break;
                case 'textarea' :
                    // Text areas severly break the formatting of concatenated columns, so they are unsupported.
                    continue(2);
            }

            // Job assignment field columns.
            $columnoptions[] = new \rb_column_option(
                'job_assignment',
                $uniquename,
                s($field->fullname),
                "{$uniquename}.{$uniquename}",
                array(
                    'joins' => $uniquename,
                    'displayfunc' => $displayfunc,
                    'dbdatatype' => 'char',
                    'outputformat' => 'text',
                    'nosort' => true,
                    'iscompound' => true,
                    'style' => array('white-space' => 'pre')
                )
            );
        }
    }

    /**
     * @param $prefix
     * @param $fields
     * @param $filteroptions
     * @param string $userjoin Table name to join to which has the user's id
     * @param string $userfield Field name containing the user's id
     * @return bool
     */
    private function add_totara_job_custom_field_filters($prefix, $fields, &$filteroptions, $userjoin = 'auser', $userfield = 'id') {
        global $CFG;

        foreach ($fields as $field) {
            $uniquename = "{$prefix}_custom_{$field->id}";

            switch ($field->datatype) {
                case 'datetime' :
                    $filteroptions[] = new \rb_filter_option(
                        'job_assignment',
                        $uniquename.'filter',
                        s($field->fullname),
                        'grpconcat_date',
                        array(
                            'datefield' => $field->shortname,
                            'prefix' => $prefix,
                        ),
                        "{$userjoin}.{$userfield}",
                        $userjoin
                    );
                    break;
                case 'checkbox' :
                    $filteroptions[] = new \rb_filter_option(
                        'job_assignment',
                        $uniquename,
                        s($field->fullname),
                        'grpconcat_checkbox',
                        array(
                            'simplemode' => true,
                            'selectchoices' => array(
                                0 => get_string('filtercheckboxallno', 'totara_reportbuilder'),
                                1 => get_string('filtercheckboxallyes', 'totara_reportbuilder'),
                                2 => get_string('filtercheckboxanyno', 'totara_reportbuilder'),
                                3 => get_string('filtercheckboxanyyes', 'totara_reportbuilder'),
                            ),
                        )
                    );
                    break;
                case 'text' :
                    $filteroptions[] = new \rb_filter_option(
                        'job_assignment',
                        $uniquename,
                        s($field->fullname),
                        'text'
                    );
                    break;
                case 'menu' :
                    $filteroptions[] = new \rb_filter_option(
                        'job_assignment',
                        $uniquename,
                        s($field->fullname),
                        'grpconcat_menu',
                        array(
                            'selectchoices' => $this->list_to_array($field->param1, "\n"),
                            'simplemode' => true,
                        )
                    );
                    break;
                case 'multiselect' :
                    require_once($CFG->dirroot . '/totara/customfield/field/multiselect/define.class.php');

                    $cfield = new \customfield_define_multiselect();
                    $cfield->define_load_preprocess($field);

                    $selectchoices = array();
                    foreach ($field->multiselectitem as $selectchoice) {
                        $selectchoices[$selectchoice['option']] = format_string($selectchoice['option']);
                    }
                    // TODO - it would be nice to display the icon here as well.
                    $filter_options['selectchoices'] = $selectchoices;
                    $filteroptions[] = new \rb_filter_option(
                        'job_assignment',
                        $uniquename,
                        s($field->fullname),
                        'grpconcat_multi',
                        $filter_options
                    );

                    break;
                case 'url' :
                case 'location' :
                    // TODO - not yet supported filter types.
                    break;
                case 'textarea' :
                case 'file' :
                    // Unsupported filter types.
                    continue(2);
            }
        }

        return true;
    }
}
