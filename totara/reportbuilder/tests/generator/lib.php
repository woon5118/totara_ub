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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package totara_reportbuilder
 * @category test
 *
 * Reportbuilder generator.
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir  . '/testing/generator/data_generator.php');

/**
 * Report builder generator.
 *
 * Usage:
 *    $reportgenerator = $this->getDataGenerator()->get_plugin_generator('totara_reportbuilder');
 */
class totara_reportbuilder_generator extends component_generator_base {
    protected $globalrestrictioncount = 0;
    protected $savedsearchescount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        parent::reset();

        $this->globalrestrictioncount = 0;
        $this->savedsearchescount = 0;
    }

    /**
     * Create a test restriction.
     *
     * @param array|stdClass $record
     * @return rb_global_restriction
     */
    public function create_global_restriction($record = null) {
        global $CFG;
        require_once("$CFG->dirroot/totara/reportbuilder/classes/rb_global_restriction.php");

        $this->globalrestrictioncount++;
        $i = $this->globalrestrictioncount;

        $record = (object)(array)$record;

        if (!isset($record->name)) {
            $record->name = 'Global report restriction '.$i;
        }

        $rest = new rb_global_restriction();
        $rest->insert($record);

        return $rest;
    }

    /**
     * Add user related data to restriction.
     *
     * Records of this cohort, org, pos or user are visible
     * in report with the restriction.
     *
     * @param stdClass|array $item - must contain prefix, restrictionid, itemid and optionally includechildren
     * @return stdClass the created record
     */
    public function assign_global_restriction_record($item) {
        global $DB;

        $item = (array)$item;

        if (empty($item['restrictionid'])) {
            throw new coding_exception('generator requires $item->restrictionid');
        }
        if (empty($item['prefix'])) {
            throw new coding_exception('generator requires valid $item->prefix');
        }
        if (empty($item['itemid'])) {
            throw new coding_exception('generator requires $item->itemid');
        }

        $tables = array(
            'cohort' => 'reportbuilder_grp_cohort_record',
            'org' => 'reportbuilder_grp_org_record',
            'pos' => 'reportbuilder_grp_pos_record',
            'user' => 'reportbuilder_grp_user_record',
        );

        $prefix = $item['prefix'];
        if ($prefix === 'position') {
            $prefix = 'pos';
        }
        if ($prefix === 'organisation') {
            $prefix = 'org';
        }
        if (!isset($tables[$prefix])) {
            throw new coding_exception('generator requires valid $item->prefix');
        }

        $record = new stdClass();
        $record->reportbuilderrecordid = $item['restrictionid'];
        $record->{$prefix . 'id'} = $item['itemid'];
        $record->timecreated = time();
        if (isset($item['includechildren'])) {
            $record->includechildren = $item['includechildren'];
        }

        $id = $DB->insert_record($tables[$prefix], $record);
        return $DB->get_record($tables[$prefix], array('id' => $id));
    }

    /**
     * Add user who is allowed to select restriction.
     *
     * @param stdClass|array $item - must contain prefix, restrictionid, itemid and optionally includechildren
     * @return stdClass the created record
     */
    public function assign_global_restriction_user($item) {
        global $DB;

        $item = (array)$item;

        if (empty($item['restrictionid'])) {
            throw new coding_exception('generator requires $item->restrictionid');
        }
        if (empty($item['prefix'])) {
            throw new coding_exception('generator requires valid $item->prefix');
        }
        if (empty($item['itemid'])) {
            throw new coding_exception('generator requires $item->itemid');
        }

        $tables = array(
            'cohort' => 'reportbuilder_grp_cohort_user',
            'org' => 'reportbuilder_grp_org_user',
            'pos' => 'reportbuilder_grp_pos_user',
            'user' => 'reportbuilder_grp_user_user',
        );

        $prefix = $item['prefix'];
        if ($prefix === 'position') {
            $prefix = 'pos';
        }
        if ($prefix === 'organisation') {
            $prefix = 'org';
        }
        if (!isset($tables[$prefix])) {
            throw new coding_exception('generator requires valid $item->prefix');
        }

        $record = new stdClass();
        $record->reportbuilderuserid = $item['restrictionid'];
        $record->{$prefix . 'id'} = $item['itemid'];
        $record->timecreated = time();
        if (isset($item['includechildren'])) {
            $record->includechildren = $item['includechildren'];
        }

        $id = $DB->insert_record($tables[$prefix], $record);
        return $DB->get_record($tables[$prefix], array('id' => $id));
    }

    /**
     * Generate saved search
     * @param stdClass $report
     * @param stdClass $user
     * @param array $item
     */
    public function create_saved_search(stdClass $report, stdClass $user, array $item = []) {
        global $DB;

        $this->savedsearchescount++;
        $i = $this->savedsearchescount;

        $name = isset($item['name']) ?  $item['name'] : 'Saved ' . $i;
        $search = isset($item['search']) ? $item['search'] : ['user-fullname' => ['operator' => 0, 'value' => 'user']];
        $ispublic = isset($item['ispublic']) ?  $item['ispublic']  : 0;
        $timemodified = isset($item['timemodified']) ?  $item['timemodified'] : time();

        $saved = new stdClass();
        $saved->reportid = $report->id;
        $saved->userid = $user->id;
        $saved->name = $name;
        $saved->search = serialize($search);
        $saved->ispublic = $ispublic;
        $saved->timemodified = $timemodified;

        $saved->id = $DB->insert_record('report_builder_saved', $saved);
        $saved = $DB->get_record('report_builder_saved', array('id' => $saved->id));
        return $saved;
    }

    /**
     * Generate scheduled report
     * @param stdClass $report Generated report
     * @param stdClass $user Generated user who scheduled report
     * @param array $item
     */
    public function create_scheduled_report(stdClass $report, stdClass $user,  array $item = []) {
        global $DB;

        $savedsearchid = isset($item['savedsearch']) ? $item['savedsearch']->id : 0 ;
        $usermodifiedid = isset($item['usermodified']) ? $item['usermodified']->id : $user->id;
        $format = isset($item['format']) ? $item['format'] : 'csv';
        $frequency = isset($item['frequency']) ? $item['frequency'] : 1; // Default daily.
        $schedule = isset($item['schedule']) ? $item['schedule'] : 0; // Default midnight.
        $exporttofilesystem = isset($item['exporttofilesystem']) ? $item['exporttofilesystem'] : REPORT_BUILDER_EXPORT_EMAIL;
        $nextreport = isset($item['nextreport']) ? $item['nextreport'] : 0; // Default ASAP.
        $lastmodified = isset($item['lastmodified']) ? $item['lastmodified'] : time();

        $scheduledreport = new stdClass();
        $scheduledreport->reportid = $report->id;
        $scheduledreport->savedsearchid = $savedsearchid;
        $scheduledreport->format = $format;
        $scheduledreport->frequency = $frequency;
        $scheduledreport->schedule = $schedule;
        $scheduledreport->exporttofilesystem = $exporttofilesystem;
        $scheduledreport->nextreport = $nextreport;
        $scheduledreport->userid = $user->id;
        $scheduledreport->usermodified = $usermodifiedid;
        $scheduledreport->lastmodified = $lastmodified;
        $scheduledreport->id = $DB->insert_record('report_builder_schedule', $scheduledreport);
        $scheduledreport = $DB->get_record('report_builder_schedule', array('id' => $scheduledreport->id));
        return $scheduledreport;
    }

    /**
     * Add audience to scheduled report
     * @param stdClass $schedulereport
     * @param stdClass $cohort
     * @return stdClass report_builder_schedule_email_audience record
     */
    public function add_scheduled_audience(stdClass $schedulereport, stdClass $cohort) {
        global $DB;

        $recipient = new stdClass();
        $recipient->scheduleid = $schedulereport->id;
        $recipient->cohortid = $cohort->id;
        $recipient->id = $DB->insert_record('report_builder_schedule_email_audience', $recipient);
        $recipient = $DB->get_record('report_builder_schedule_email_audience', array('id' => $recipient->id));
        return $recipient;
    }

    /**
     * Add email to scheduled report
     * @param stdClass $schedulereport
     * @param string $emal
     * @return stdClass report_builder_schedule_email_external record
     */
    public function add_scheduled_email(stdClass $schedulereport, string $email = '') {
        global $DB;

        $recipient = new stdClass();
        $recipient->scheduleid = $schedulereport->id;
        $recipient->email = empty($email) ? uniqid() . '@example.com' : $email;
        $recipient->id = $DB->insert_record('report_builder_schedule_email_external', $recipient);
        $recipient = $DB->get_record('report_builder_schedule_email_external', array('id' => $recipient->id));
        return $recipient;
    }

    /**
     * Add audience to scheduled report
     * @param stdClass $schedulereport
     * @param stdClass $user
     * @return stdClass report_builder_schedule_email_systemuser record
     */
    public function add_scheduled_user(stdClass $schedulereport, stdClass $user) {
        global $DB;

        $recipient = new stdClass();
        $recipient->scheduleid = $schedulereport->id;
        $recipient->userid = $user->id;
        $recipient->id = $DB->insert_record('report_builder_schedule_email_systemuser', $recipient);
        $recipient = $DB->get_record('report_builder_schedule_email_systemuser', array('id' => $recipient->id));
        return $recipient;
    }

    /**
     * First created the report
     * then injected the default columns
     * for the report
     *
     * @param array $record
     * @return int $record id
     */
    public function create_default_standard_report($record) {
        global $DB;
        $addon = array(
            'hidden'            => 0,
            'accessmode'        => 0,
            'contentmode'       => 0,
            'recordsperpage'    => 40,
            'toolbarsearch'     => 1,
            'globalrestriction' =>  0,
            'timemodified'      => time(),
            'defaultsortorder'  => 4,
            'embed'             => 0
        );

        if (!is_array($record)) {
            $record = (array)$record;
        }

        // Update record addon here, if the record does not have any value, then the default value will fallback to add-on
        // value
        foreach ($addon as $key => $value) {
            if (!isset($record[$key])) {
                $record[$key] = $value;
            }
        }

        $id = $DB->insert_record("report_builder", (object)$record, true);

        $src = reportbuilder::get_source_object($record['source']);

        $so = 1;
        $columnoptions = $src->columnoptions;

        /** @var rb_column_option $columnoption */
        foreach ($columnoptions as $columnoption) {
            // By default way, the columns that are deprecated should not be added into the report builder
            if (isset($columnoption->deprecated) && $columnoption->deprecated) {
                continue;
            }

            $item = array(
                'reportid'      => $id,
                'type'          => $columnoption->type,
                'value'         => $columnoption->value,
                'heading'       => $columnoption->name,
                'hidden'        => $columnoption->hidden,
                'transform'     => $columnoption->transform,
                'aggregate'     => $columnoption->aggregate,
                'sortorder'     => $so,
                'customheading' => 0
            );

            $DB->insert_record("report_builder_columns", (object)$item);
            $so+= 1;
        }

        return $id;
    }
}

/**
 * This class intended to generate different mock entities
 *
 * @package totara_reportbuilder
 * @category test
 */
class totara_reportbuilder_cache_generator extends testing_data_generator {
    protected static $cohortrulecount = 0;
    protected static $programcount = 0;
    protected static $certificationcount = 0;
    protected static $plancount = 0;

    private $generator;

    public function set_actual_generator(testing_data_generator $generator) {
        $this->generator = $generator;
    }

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        self::$cohortrulecount = 0;
        self::$programcount = 0;
        self::$certificationcount = 0;
        self::$plancount = 0;
        parent::reset();
    }

    /**
     * Add particular mock params to cohort rules
     *
     * @deprecated since Totara 13, please use totara_cohort_generator.
     *
     * @param int $ruleid
     * @param array $params Params to add
     * @param array $listofvalues List of values
     */
    public function create_cohort_rule_params(int $ruleid, array $params, array $listofvalues) {
        /** @var totara_cohort_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_cohort');
        $generator->add_param_to_cohort_rule($ruleid, $params, $listofvalues);
    }

    /**
     * Create program for testing.
     *
     * @deprecated since Totara 13, please use totara_program_generator.
     *
     * @param array $data Override default properties
     * @return program Program object
     */
    public function create_program(array $data = array()) {
        // Keep a record of how many test programs are being created.
        self::$programcount++;

        if (!isset($data['fullname'])) {
            $data['fullname'] = 'Program ' . self::$programcount;
        }

        /** @var totara_program_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_program');
        return $generator->legacy_create_program($data);
    }

    /**
     * Create program certification for testing.
     *
     * @deprecated since Totara 13, please use totara_program_generator.
     *
     * @param array $data Override default properties - use 'cert_' or 'prog_' prefix for each parameter name
     * @param array $coursesetdata Course set data which gets given to create_coursesets_in_program.
     *                             Check that function for details.
     *
     * @return program Program object
     */
    public function create_certification(array $data = array(), array $coursesetdata = null) {
        // Keep a record of how many test certifications are being created.
        self::$certificationcount++;

        if (!isset($data['prog_fullname'])) {
            $data['prog_fullname'] = 'Certification ' . self::$certificationcount;
        }

        /** @var totara_program_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_program');
        $certifprogram = $generator->legacy_create_certification($data);

        if ($coursesetdata !== null) {
            $generator->legacy_add_coursesets_to_program($certifprogram, $coursesetdata);
        }

        return $certifprogram;
    }

    /**
     * Creates course sets and adds content given on the data passed through details.
     *
     * Details should be an array of course set data, each item can have the following keys:
     *
     *   - type int The type, one of CONTENTTYPE_MULTICOURSE, CONTENTTYPE_COMPETENCY, CONTENTTYPE_RECURRING
     *   - nextsetoperator int The next set operator, one of NEXTSETOPERATOR_THEN, NEXTSETOPERATOR_AND, NEXTSETOPERATOR_OR
     *   - completiontype The type, one of COMPLETIONTYPE_ALL, COMPLETIONTYPE_SOME, COMPLETIONTYPE_OPTIONAL
     *   - certifpath The certification path for this set, one of CERTIFPATH_STD, CERTIFPATH_RECERT
     *   - mincourses int The minimum number of courses the user is required to complete (only relevant with COMPLETIONTYPE_SOME)
     *   - coursesumfield int Id of custom field created by totara_customfield_generator::create_multiselect (only relevant with COMPLETIONTYPE_SOME)
     *   - coursesumfieldtotal int The required minimum score required to complete (only relevant with COMPLETIONTYPE_SOME)
     *   - timeallowed int The minimum time, in seconds, which users are expected to be able to finish in.
     *   - courses array An array of courses created by create_course.
     *
     * @deprecated since Totara 13, please use totara_program_generator.
     *
     * @param program $program
     * @param array $details
     * @throws coding_exception
     */
    public function create_coursesets_in_program(program $program, array $details) {
        /** @var totara_program_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_program');
        $generator->legacy_add_coursesets_to_program($program, $details);
    }

    /**
     * Create mock user with assigned manager
     *
     * @deprecated since Totara 13, please use totara_job_generator or testing_data_generator.
     *
     * @param  array|stdClass $record
     * @param  array $options
     * @return stdClass
     */
    public function create_user($record = null, array $options = null) {
        /** @var totara_job_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_job');
        list($user, $ja) = $generator->create_user_and_job((array)$record, null, $options);
        return $user;
    }

    /**
     * Get empty program assignment
     *
     * @deprecated since Totara 13, please use totara_program_generator.
     *
     * @param int $programid
     * @return stdClass
     */
    protected function get_empty_prog_assignment(int $programid) {
        /** @var totara_program_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_program');
        $method = new ReflectionMethod($generator, 'get_empty_prog_assignment');
        $method->setAccessible(true);
        return $method->invoke($generator, $programid);
    }

    /**
     * Assign users to a program
     *
     * @deprecated since Totara 13, please use totara_program_generator.
     *
     * @param int $programid Program id
     * @param int $assignmenttype Assignment type
     * @param int $itemid item to be assigned to the program. e.g Audience, position, organization, individual
     * @param array $record
     */
    public function assign_to_program(int $programid, int $assignmenttype, int $itemid, $record = null) {
        /** @var totara_program_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_program');
        $generator->assign_to_program($programid, $assignmenttype, $itemid, $record, true);
    }

    /**
     * Add mock program to user
     *
     * @deprecated since Totara 13, please use totara_program_generator.
     *
     * @param int $programid Program id
     * @param array $userids User ids array of int
     */
    public function assign_program(int $programid, array $userids) {
        /** @var totara_program_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_program');
        $generator->assign_program($programid, $userids);
    }

    /**
     * Add course to program
     *
     * @deprecated since Totara 13, please use totara_program_generator::add_courses_and_courseset_to_program.
     *
     * @param int $programid Program id
     * @param array $courseids of int Course id
     * @param int $certifpath CERTIFPATH_XXX constant
     */
    public function add_courseset_program(int $programid, array $courseids, int $certifpath = CERTIFPATH_STD) {
        $program = new program($programid);
        $courseset = [];
        foreach ($courseids as $id) {
            $course = new stdClass();
            $course->id = $id;
            $courseset[] = $course;
        }
        /** @var totara_program_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_program');
        $generator->add_courses_and_courseset_to_program($program, [$courseset], $certifpath);
    }

    /**
     * Create mock program
     *
     * @deprecated since Totara 13, please use totara_plan_generator.
     *
     * @param int $userid User id
     * @param array|stdClass $record Override default properties
     * @return development_plan
     */
    public function create_plan(int $userid, $record = array()) {
        // Keep a record of how many test plans are being created.
        self::$plancount++;

        $record = (array)$record;
        if (!isset($record['name'])) {
            $record['name'] = 'Learning plan '. self::$plancount;
        }
        /** @var totara_plan_generator $generator */
        $generator = $this->generator->get_plugin_generator('totara_plan');
        return $generator->legacy_create_plan($userid, $record);
    }
}
