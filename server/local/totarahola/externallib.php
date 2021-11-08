<?php   
/**
 * local_totarahola  web services implemntation.
 *
 * @package local_totarahola
 * @copyright 2021, Steven CIBAMBO, Videabiz <steven@videabiz.com>
 * @license MIT
 */
defined('MOODLE_INTERNAL') || die;


require_once($CFG->libdir."/externallib.php");
require_once($CFG->dirroot."/local/totarahola/lib.php");

class local_totarahola_external extends external_api{
        /**
        * Returns a prepared structure to use a context parameters.
        * @return external_single_structure
        */
        protected static function get_context_parameters() {
                $id = new external_value(
                PARAM_INT,
                'Context ID. Either use this value, or level and instanceid.',
                VALUE_DEFAULT,
                0
                );
                $level = new external_value(
                PARAM_ALPHA,
                'Context level. To be used with instanceid.',
                VALUE_DEFAULT,
                ''
                );
                $instanceid = new external_value(
                PARAM_INT,
                'Context instance ID. To be used with level',
                VALUE_DEFAULT,
                0
                );
                return new external_single_structure(array(
                'contextid' => $id,
                'contextlevel' => $level,
                'instanceid' => $instanceid,
                ));
        }
        public static function totara_user_validate_password_parameters() {
                return new external_function_parameters(
                        array(
                                'username' =>new external_value(PARAM_USERNAME, 'Username policy is defined in Moodle security config.'),
                                'password' =>new external_value(PARAM_RAW, 'Plain text password consisting of any characters')
                        )
                );
        
        }
        public static function totara_user_validate_password($username, $password) {

                global $CFG, $DB;

                //require_once($CFG->libdir . '/authlib.php');
                require_once($CFG->dirroot . "/lib/moodlelib.php");

                $user = $DB->get_record('user', array('username'=>$username));
                $result['final'] = validate_internal_user_password($user, $password);
                
                return $result;
                /*
                $class = new auth_plugin_manual();
                $result['final'] = $class->user_login($username, $password);
                // $result['final'] = validate_internal_user_password($username, $password);
                */
        }
        public static function totara_user_validate_password_returns() {
                
                return new external_single_structure(
                        array("final" => new external_value(PARAM_BOOL, "TRUE/FALSE"))
                );
        }
        /**
        * Returns description of course() parameters.
        *
        * @return \external_function_parameters
        */
        public static function get_course_parameters() {
                
                $id = new external_value(
                PARAM_INT,
                'Id course to want to retrieve'
                );
                return new external_function_parameters(
                        array('filters' => new external_single_structure(
                                array('id' => $id)
                        )));
        }
        /**
         * Return the existing competency
         *
         * @param array $filters
         *
         * @return array
         * @throws \required_capability_exception
         * @throws invalid_parameter_exception
         */
        public static function get_course($filter) {
                global $DB, $PAGE;

                $params = self::validate_parameters(self::get_competency_parameters(), array('filters' => $filter));
                $params = $params['filters'];
                # if an exception is thrown in the below code, all DB queries in this code will be rollback.
                $transaction = $DB->start_delegated_transaction(); 

                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/hierarchy:viewcompetencyframeworks', $context);
                $output = $PAGE->get_renderer('core');

                $results = local_totarahola_lib::get_course($params['id']);
                $transaction->allow_commit();
                return $results;
        }
        /**
         * Returns description of list_competency result value.
         *
         * @return \external_description
         */
        public static function get_course_returns() {
                return new external_single_structure(
                        array(
                                'id' => new external_value(PARAM_INT, 'course id'),
                                'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                                'category' => new external_value(PARAM_INT, 'category id'),
                                'categorysortorder' => new external_value(PARAM_INT,
                                        'sort order into the category', VALUE_OPTIONAL),
                                'fullname' => new external_value(PARAM_TEXT, 'full name'),
                                'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL),
                                'summary' => new external_value(PARAM_RAW, 'summary'),
                                'summaryformat' => new external_format_value('summary'),
                                'format' => new external_value(PARAM_PLUGIN,
                                        'course format: weeks, topics, social, site,..'),
                                'showgrades' => new external_value(PARAM_INT,
                                        '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                                'newsitems' => new external_value(PARAM_INT,
                                        'number of recent items appearing on the course page', VALUE_OPTIONAL),
                                'startdate' => new external_value(PARAM_INT,
                                        'timestamp when the course start'),
                                'enddate' => new external_value(PARAM_INT, 'timestamp when the course end'),
                                'numsections' => new external_value(PARAM_INT,
                                        '(deprecated, use courseformatoptions) number of weeks/topics',
                                        VALUE_OPTIONAL),
                                'maxbytes' => new external_value(PARAM_INT,
                                        'largest size of file that can be uploaded into the course',
                                        VALUE_OPTIONAL),
                                'showreports' => new external_value(PARAM_INT,
                                        'are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                                'visible' => new external_value(PARAM_INT,
                                        '1: available to student, 0:not available', VALUE_OPTIONAL),
                                'hiddensections' => new external_value(PARAM_INT,
                                        '(deprecated, use courseformatoptions) How the hidden sections in the course are displayed to students',
                                        VALUE_OPTIONAL),
                                'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible',
                                        VALUE_OPTIONAL),
                                'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no',
                                        VALUE_OPTIONAL),
                                'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id',
                                        VALUE_OPTIONAL),
                                'timecreated' => new external_value(PARAM_INT,
                                        'timestamp when the course have been created', VALUE_OPTIONAL),
                                'timemodified' => new external_value(PARAM_INT,
                                        'timestamp when the course have been modified', VALUE_OPTIONAL),
                                'enablecompletion' => new external_value(PARAM_INT,
                                        'Enabled, control via completion and activity settings. Disbaled,
                                                not shown in activity settings.', VALUE_OPTIONAL),
                                'completionstartonenrol' => new external_value(PARAM_INT,
                                        '1: begin tracking a student\'s progress in course completion
                                                after course enrolment. 0: does not',
                                        VALUE_OPTIONAL),
                                'completionnotify' => new external_value(PARAM_INT,
                                        '1: yes 0: no', VALUE_OPTIONAL),
                                'lang' => new external_value(PARAM_SAFEDIR,
                                        'forced course language', VALUE_OPTIONAL),
                                'forcetheme' => new external_value(PARAM_PLUGIN,
                                        'name of the force theme', VALUE_OPTIONAL),
                                'courseformatoptions' => new external_multiple_structure(
                                        new external_single_structure(
                                        array('name' => new external_value(PARAM_ALPHANUMEXT, 'course format option name'),
                                                'value' => new external_value(PARAM_RAW, 'course format option value')
                                        )),
                                        'additional options for particular course format', VALUE_OPTIONAL
                                        ),
                                // Start T-14436 specific params.
                                'audiencevisible' => new external_value(PARAM_INT,
                                        '0: enrolled users only,
                                        1: enrolled users and members of the selected audiences,
                                        2: all users,
                                        3: no users',
                                        VALUE_OPTIONAL),
                                'coursetype' => new external_value(PARAM_INT,
                                        '0: elearning,
                                        1: blended,
                                        2: facetoface',
                                        VALUE_OPTIONAL),
                                'completionprogressonview' => new external_value(PARAM_INT,
                                        '1: mark as in progress on first view,
                                        0: does not',
                                        VALUE_OPTIONAL),
                        )
                );
        }
        /**
         * Returns description of method parameters
         * @return external_function_parameters
         */
        public static function get_popular_courses_parameters()
        {
                return new external_function_parameters(
                        array(
                                //
                        )
                );
        }
        /**
         * Get popular courses
         *
         * @return array
         * @since Moodle 2.2
         */
        public static function get_popular_courses() {
                global $CFG, $DB;
                require_once($CFG->dirroot . "/course/lib.php");

                //retrieve courses
                // Totara: Added the ability to fetch only courses and excluding non-courses. Note that this part here will return a list of courses including site course records - according to the understanding of PHPUNIT tests related.
                $courses = $DB->get_records_sql(
                'SELECT * FROM "ttr_course" c WHERE (c.containertype = :type OR c.containertype = :site_type)',
                        [
                        'type' => \container_course\course::get_type(),
                        'site_type' => \container_site\site::get_type()
                        ]
                );
                //create return value
                $coursesinfo = array();
                foreach ($courses as $course) {

                // now security checks
                $context = context_course::instance($course->id, IGNORE_MISSING);
                $courseformatoptions = course_get_format($course)->get_format_options();
                try {
                        self::validate_context($context);
                } catch (Exception $e) {
                        $exceptionparam = new stdClass();
                        $exceptionparam->message = $e->getMessage();
                        $exceptionparam->courseid = $course->id;
                        throw new moodle_exception('errorcoursecontextnotvalid', 'webservice', '', $exceptionparam);
                }
                if ($course->id != SITEID) {
                        require_capability('moodle/course:view', $context);
                }

                $courseinfo = array();
                $courseinfo['id'] = $course->id;
                $courseinfo['fullname'] = external_format_string($course->fullname, $context->id);
                $courseinfo['shortname'] = external_format_string($course->shortname, $context->id);
                $courseinfo['displayname'] = external_format_string(get_course_display_name_for_list($course), $context->id);
                $courseinfo['categoryid'] = $course->category;
                list($courseinfo['summary'], $courseinfo['summaryformat']) =
                        external_format_text($course->summary, $course->summaryformat, $context->id, 'course', 'summary', 0);
                $courseinfo['format'] = $course->format;
                $courseinfo['startdate'] = $course->startdate;
                $courseinfo['enddate'] = $course->enddate;
                if (array_key_exists('numsections', $courseformatoptions)) {
                        // For backward-compartibility
                        $courseinfo['numsections'] = $courseformatoptions['numsections'];
                }

                //some field should be returned only if the user has update permission
                $courseadmin = has_capability('moodle/course:update', $context);
                if ($courseadmin) {
                        $courseinfo['categorysortorder'] = $course->sortorder;
                        $courseinfo['idnumber'] = $course->idnumber;
                        $courseinfo['showgrades'] = $course->showgrades;
                        $courseinfo['showreports'] = $course->showreports;
                        $courseinfo['newsitems'] = $course->newsitems;
                        $courseinfo['visible'] = $course->visible;
                        $courseinfo['maxbytes'] = $course->maxbytes;
                        if (array_key_exists('hiddensections', $courseformatoptions)) {
                        // For backward-compartibility
                        $courseinfo['hiddensections'] = $courseformatoptions['hiddensections'];
                        }
                        // Return numsections for backward-compatibility with clients who expect it.
                        $courseinfo['numsections'] = course_get_format($course)->get_last_section_number();
                        $courseinfo['groupmode'] = $course->groupmode;
                        $courseinfo['groupmodeforce'] = $course->groupmodeforce;
                        $courseinfo['defaultgroupingid'] = $course->defaultgroupingid;
                        $courseinfo['lang'] = clean_param($course->lang, PARAM_LANG);
                        $courseinfo['timecreated'] = $course->timecreated;
                        $courseinfo['timemodified'] = $course->timemodified;
                        $courseinfo['forcetheme'] = clean_param($course->theme, PARAM_THEME);
                        $courseinfo['enablecompletion'] = $course->enablecompletion;
                        $courseinfo['completionstartonenrol'] = $course->completionstartonenrol;
                        $courseinfo['completionnotify'] = $course->completionnotify;
                        $courseinfo['courseformatoptions'] = array();
                        foreach ($courseformatoptions as $key => $value) {
                        $courseinfo['courseformatoptions'][] = array(
                                'name' => $key,
                                'value' => $value
                                );
                        }
                        // TOTARA changes.
                        $courseinfo['audiencevisible'] = $course->audiencevisible;
                        $courseinfo['coursetype'] = $course->coursetype;
                        $courseinfo['completionprogressonview'] = $course->completionprogressonview;
                        // End TOTARA.
                }
                if ($courseadmin or $course->visible
                        or has_capability('moodle/course:viewhiddencourses', $context)) {
                        $coursesinfo[] = $courseinfo;
                        }
                }
                return $coursesinfo;
        }
        /**
         * Returns description of method result value
         *
         * @return external_description
         * @since Moodle 2.2
         */
        public static function get_popular_courses_returns() {
                return new external_multiple_structure(
                        new external_single_structure(
                                array(
                                'id' => new external_value(PARAM_INT, 'course id'),
                                'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                                'categoryid' => new external_value(PARAM_INT, 'category id'),
                                'categorysortorder' => new external_value(PARAM_INT,
                                        'sort order into the category', VALUE_OPTIONAL),
                                'fullname' => new external_value(PARAM_TEXT, 'full name'),
                                'displayname' => new external_value(PARAM_TEXT, 'course display name'),
                                'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL),
                                'summary' => new external_value(PARAM_RAW, 'summary'),
                                'summaryformat' => new external_format_value('summary'),
                                'format' => new external_value(PARAM_PLUGIN,
                                        'course format: weeks, topics, social, site,..'),
                                'showgrades' => new external_value(PARAM_INT,
                                        '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                                'newsitems' => new external_value(PARAM_INT,
                                        'number of recent items appearing on the course page', VALUE_OPTIONAL),
                                'startdate' => new external_value(PARAM_INT,
                                        'timestamp when the course start'),
                                'enddate' => new external_value(PARAM_INT,
                                        'timestamp when the course end'),
                                'numsections' => new external_value(PARAM_INT,
                                        '(deprecated, use courseformatoptions) number of weeks/topics',
                                        VALUE_OPTIONAL),
                                'maxbytes' => new external_value(PARAM_INT,
                                        'largest size of file that can be uploaded into the course',
                                        VALUE_OPTIONAL),
                                'showreports' => new external_value(PARAM_INT,
                                        'are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                                'visible' => new external_value(PARAM_INT,
                                        '1: available to student, 0:not available', VALUE_OPTIONAL),
                                'hiddensections' => new external_value(PARAM_INT,
                                        '(deprecated, use courseformatoptions) How the hidden sections in the course are displayed to students',
                                        VALUE_OPTIONAL),
                                'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible',
                                        VALUE_OPTIONAL),
                                'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no',
                                        VALUE_OPTIONAL),
                                'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id',
                                        VALUE_OPTIONAL),
                                'timecreated' => new external_value(PARAM_INT,
                                        'timestamp when the course have been created', VALUE_OPTIONAL),
                                'timemodified' => new external_value(PARAM_INT,
                                        'timestamp when the course have been modified', VALUE_OPTIONAL),
                                'enablecompletion' => new external_value(PARAM_INT,
                                        'Enabled, control via completion and activity settings. Disbaled,
                                                not shown in activity settings.',
                                        VALUE_OPTIONAL),
                                'completionstartonenrol' => new external_value(PARAM_INT,
                                        '1: begin tracking a student\'s progress in course completion
                                                after course enrolment. 0: does not',
                                        VALUE_OPTIONAL),
                                'completionnotify' => new external_value(PARAM_INT,
                                        '1: yes 0: no', VALUE_OPTIONAL),
                                'lang' => new external_value(PARAM_SAFEDIR,
                                        'forced course language', VALUE_OPTIONAL),
                                'forcetheme' => new external_value(PARAM_PLUGIN,
                                        'name of the force theme', VALUE_OPTIONAL),
                                'courseformatoptions' => new external_multiple_structure(
                                        new external_single_structure(
                                        array('name' => new external_value(PARAM_ALPHANUMEXT, 'course format option name'),
                                                'value' => new external_value(PARAM_RAW, 'course format option value')
                                        )),
                                        'additional options for particular course format', VALUE_OPTIONAL
                                ),
                                // Start T-14436 specific params.
                                'audiencevisible' => new external_value(PARAM_INT,
                                        '0: enrolled users only,
                                        1: enrolled users and members of the selected audiences,
                                        2: all users,
                                        3: no users',
                                        VALUE_OPTIONAL),
                                'coursetype' => new external_value(PARAM_INT,
                                        '0: elearning,
                                        1: blended,
                                        2: facetoface',
                                        VALUE_OPTIONAL),
                                'completionprogressonview' => new external_value(PARAM_INT,
                                        '1: mark as in progress on first view,
                                        0: does not',
                                        VALUE_OPTIONAL),
                                ), 'course'
                        )
                );
        }
        /**
         * Returns description of get_competency_with_more_courses() parameters.
         *
         * @return \external_function_parameters
         */     
        public static function get_competency_with_more_courses_parameters() {
                $sort = new external_value(
                PARAM_TEXT,
                'Column to sort by.',
                VALUE_DEFAULT,
                'fullname'
                );
                $order = new external_value(
                PARAM_TEXT,
                'Sort direction. Should be either ASC or DESC',
                VALUE_DEFAULT,
                'ASC'
                );
                $skip = new external_value(
                PARAM_INT,
                'Skip this number of records before returning results',
                VALUE_DEFAULT,
                0
                );
                $limit = new external_value(
                PARAM_INT,
                'Return this number of records at most.',
                VALUE_DEFAULT,
                0
                );

                $params = array(
                'sort' => $sort,
                'order' => $order,
                'skip' => $skip,
                'limit' => $limit
                );
                return new external_function_parameters(
                        array(
                        'filters' => new external_single_structure($params)
                        ));
        }
        /**
         * List the existing competency.
         *
         * @param int $sort
         * @param string $order
         * @param string $skip
         * @param int $limit
         *
         * @return array
         * @throws \required_capability_exception
         * @throws invalid_parameter_exception
         */
        public static function get_competency_with_more_courses($filters) {
                global $DB, $PAGE;

                $params = self::validate_parameters(self::get_competency_with_more_courses_parameters(), array('filters' => $filters));
                $params = $params['filters'];

                if ($params['order'] !== '' && $params['order'] !== 'ASC' && $params['order'] !== 'DESC') {
                        throw new invalid_parameter_exception('Invalid order param. Must be ASC, DESC or empty.');
                }        
                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/hierarchy:viewcompetencyframeworks', $context);
                $output = $PAGE->get_renderer('core');

                //retrieve competencies
                // Totara hola: Added the ability to fetch only courses and excluding non-courses. Note that this part here will return a list of courses including site course records - according to the understanding of PHPUNIT tests related.
                $results = local_totarahola_lib::get_competency_with_more_courses($params['sort'], $params['order'], $params['skip'], $params['limit']);

                $records = array();
                foreach ($results as $result) {
                //     $exporter = new competency_exporter($competency, array('context' => $context));
                //     $record = $exporter->export($output);
                array_push($records, $result);
                }
                return $records;
        }
        /**
        * Returns description of list_competency_frameworks() result value.
        *
        * @return \external_description
        */
        public static function get_competency_with_more_courses_returns() {
                return new external_multiple_structure(
                        new external_single_structure(
                                array(
                                'id' => new external_value(PARAM_INT, 'Competency id'),
                                        'shortname' => new external_value(PARAM_TEXT, 'Competency shortname'),
                                        'description' => new external_value(PARAM_RAW, ''),
                                        'idnumber' => new external_value(PARAM_TEXT, 'Competency ID number'),
                                        'frameworkid' => new external_value(PARAM_INT, 'Competency framework id'),
                                        'path' => new external_value(PARAM_TEXT, 'Competency path'),
                                        'parentid' => new external_value(PARAM_INT, 'Parent competency id'),
                                        'visible' => new external_value(PARAM_INT, 'Comptency Visibility'),
                                        'proficiencyexpected' => new external_value(PARAM_INT, 'Proficiency expected'),
                                        'evidencecount' => new external_value(PARAM_INT, ''),
                                        'timecreated' => new external_value(PARAM_INT, ''),
                                        'timemodified' => new external_value(PARAM_INT, ''),
                                        'usermodified' => new external_value(PARAM_INT, ''),
                                        'fullname' => new external_value(PARAM_TEXT, ''),
                                        'depthlevel' => new external_value(PARAM_INT, ''),
                                        'typeid' => new external_value(PARAM_INT, '') 
                                )
                        )
                );
        }
        /**
         * Returns description of list_framework_competencies() parameters.
         *
         * @return \external_function_parameters
         */
        public static function get_framework_competencies_parameters() {
                $frameworkid = new external_value(
                        PARAM_INT,
                        'Framewok id belong to a competency',
                        VALUE_DEFAULT,
                        ''
                );
                $sort = new external_value(
                PARAM_TEXT,
                'Column to sort by.',
                VALUE_DEFAULT,
                'fullname'
                );
                $order = new external_value(
                PARAM_TEXT,
                'Sort direction. Should be either ASC or DESC',
                VALUE_DEFAULT,
                ''
                );
                $params = array(
                'frameworkid' => $frameworkid,
                'sort' => $sort,
                'order' => $order,
                );
                return new external_function_parameters(
                        array(
                                'filters' => new external_single_structure($params)
                        ));
        }
        /**
         * List the existing competency.
         *
         * @param array $filters
         *
         * @return array
         * @throws \required_capability_exception
         * @throws invalid_parameter_exception
         */
        public static function get_framework_competencies($filters) {
                global $DB, $PAGE;

                $params = self::validate_parameters(self::get_framework_competencies_parameters(), array('filters' => $filters));
                $params = $params['filters'];

                if ($params['order'] !== '' && $params['order'] !== 'ASC' && $params['order'] !== 'DESC') {
                        throw new invalid_parameter_exception('Invalid order param. Must be ASC, DESC or empty.');
                }
                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/hierarchy:viewcompetencyframeworks', $context);
                $output = $PAGE->get_renderer('core');

                //retrieve competencies
                // Totara hola: Added the ability to fetch only courses and excluding non-courses. Note that this part here will return a list of courses including site course records - according to the understanding of PHPUNIT tests related.
                $competencies = local_totarahola_lib::get_framework_competencies(
                        $params['frameworkid'],
                        $params['sort'],
                        $params['order']);

                $records = array();
                foreach ($competencies as $competency) {
                array_push($records, $competency);
                }
                return $records;
        }
        /**
         * Returns description of list_competencies() result value.
         *
         * @return \external_description
         */
        public static function get_framework_competencies_returns() {
                return new external_multiple_structure(
                        new external_single_structure(
                                array(
                                'id' => new external_value(PARAM_INT, 'competency id'),
                                'shortname' => new external_value(PARAM_TEXT, 'competency short name'),
                                'description' => new external_value(PARAM_RAW, 'competency description'),
                                'frameworkid' => new external_value(PARAM_INT, 'framework id belong to a competency'),
                                'idnumber' => new external_value(PARAM_TEXT, 'competency id number'),
                                'parentid' => new external_value(PARAM_INT, 'competency parent id'),
                                'visible' => new external_value(PARAM_INT, 'competency visible'),
                                'evidencecount' => new external_value(PARAM_INT, 'Evidence count'),
                                'timemodified' => new external_value(PARAM_INT, 'Date of modification'),
                                'timecreated' => new external_value(PARAM_INT, 'Date of creation'),
                                'usermodified' => new external_value(PARAM_INT, 'The id of the creator s competency'),
                                'fullname' => new external_value(PARAM_TEXT, 'The fullname of the competency'),
                                'depthlevel' => new external_value(PARAM_INT, 'Depth level of the competency in whole framework'),
                                'typeid' => new external_value(PARAM_INT, 'Type id of the competency by default is 0'),
                        )
                ));
        }
        /**
         * Returns description of competency_frameworks() parameters.
         *
         * @return \external_function_parameters
         */
        public static function get_competency_frameworks_parameters() {
                $sort = new external_value(
                PARAM_ALPHANUMEXT,
                'Column to sort by.',
                VALUE_DEFAULT,
                'fullname'
                );
                $order = new external_value(
                PARAM_ALPHA,
                'Sort direction. Should be either ASC or DESC',
                VALUE_DEFAULT,
                ''
                );
                $onlyvisible = new external_value(
                PARAM_INT,
                'Only visible frameworks will be returned if visible true',
                VALUE_DEFAULT,
                1
                );
                $params = array(
                'sort' => $sort,
                'order' => $order,
                'onlyvisible' => $onlyvisible,
                );
                return new external_function_parameters(
                        array(
                                'filters' => new external_single_structure($params)
                        ));
        }
        /**
         * List the existing competency frameworks
         *
         * @param array $filters
         *
         * @return array
         * @throws \required_capability_exception
         * @throws invalid_parameter_exception
         */
        public static function get_competency_frameworks($filters) {
                global $DB, $PAGE;

                $params = self::validate_parameters(self::get_competency_frameworks_parameters(), array('filters' => $filters));
                $params = $params['filters'];
                # if an exception is thrown in the below code, all DB queries in this code will be rollback.
                $transaction = $DB->start_delegated_transaction(); 

                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/hierarchy:viewcompetencyframeworks', $context);
                $output = $PAGE->get_renderer('core');

                if ($params['order'] !== '' && $params['order'] !== 'ASC' && $params['order'] !== 'DESC') {
                throw new invalid_parameter_exception('Invalid order param. Must be ASC, DESC or empty.');
                }

                $results = local_totarahola_lib::get_frameworks($params['sort'],
                                        $params['order'],
                                        $params['onlyvisible']);
                $records = array();
                foreach ($results as $result) {
                        array_push($records, $result);
                }
                $transaction->allow_commit();
                return $records;
        }
        /**
         * Returns description of list_competency_frameworks() result value.
         *
         * @return \external_description
         */
        public static function get_competency_frameworks_returns() {
                return new external_multiple_structure(
                        new external_single_structure(
                                array(
                                        'id' => new external_value(PARAM_INT, 'id of the framework'),
                                        'shortname' => new external_value(PARAM_TEXT, 'Short name of the framework by default is empty'),
                                        'idnumber' => new external_value(PARAM_TEXT, 'id number of the the framework'),
                                        'description' => new external_value(PARAM_RAW, 'Framework description'),
                                        'sortorder' => new external_value(PARAM_INT, 'Criteria of sorting'),
                                        'visible' => new external_value(PARAM_INT, 'The visibility of framework'),
                                        'timecreated' => new external_value(PARAM_INT, 'Created time'),
                                        'timemodified' => new external_value(PARAM_INT, 'Last time of modification'),
                                        'usermodified' => new external_value(PARAM_INT, 'Creator user id'),
                                        'fullname' => new external_value(PARAM_TEXT, 'Framework fullname'),
                                )
                        )
                );
        }
        /**
         * Returns description of competency() parameters.
         *
         * @return \external_function_parameters
         */
        public static function get_competency_parameters() {
                
                $id = new external_value(
                PARAM_INT,
                'Id competency to want to retrieve'
                );
                return new external_function_parameters(
                        array('filters' => new external_single_structure(
                                array('id' => $id)
                        )));
        }
        /**
         * Return the existing competency
         *
         * @param array $filters
         *
         * @return array
         * @throws \required_capability_exception
         * @throws invalid_parameter_exception
         */
        public static function get_competency($filter) {
                global $DB, $PAGE;

                $params = self::validate_parameters(self::get_competency_parameters(), array('filters' => $filter));
                $params = $params['filters'];
                # if an exception is thrown in the below code, all DB queries in this code will be rollback.
                $transaction = $DB->start_delegated_transaction(); 

                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/hierarchy:viewcompetencyframeworks', $context);
                $output = $PAGE->get_renderer('core');

                $results = local_totarahola_lib::get_competency($params['id']);
                $transaction->allow_commit();
                return $results;
        }
        /**
         * Returns description of list_competency result value.
         *
         * @return \external_description
         */
        public static function get_competency_returns() {
                return new external_single_structure(
                        array(
                                'id' => new external_value(PARAM_INT, 'competency id'),
                                'shortname' => new external_value(PARAM_TEXT, 'competency short name'),
                                'description' => new external_value(PARAM_RAW, 'competency description'),
                                'frameworkid' => new external_value(PARAM_INT, 'framework id belong to a competency'),
                                'idnumber' => new external_value(PARAM_TEXT, 'competency id number'),
                                'parentid' => new external_value(PARAM_INT, 'competency parent id'),
                                'visible' => new external_value(PARAM_INT, 'competency visible'),
                                'evidencecount' => new external_value(PARAM_INT, 'Evidence count'),
                                'timemodified' => new external_value(PARAM_INT, 'Date of modification'),
                                'timecreated' => new external_value(PARAM_INT, 'Date of creation'),
                                'usermodified' => new external_value(PARAM_INT, 'The id of the creator s competency'),
                                'fullname' => new external_value(PARAM_TEXT, 'The fullname of the competency'),
                                'depthlevel' => new external_value(PARAM_INT, 'Depth level of the competency in whole framework'),
                                'typeid' => new external_value(PARAM_INT, 'Type id of the competency by default is 0'),
                        )   
                );
        }
        /**
         * Returns description of competency_criteria() parameters.
         *
         * @return \external_function_parameters
         */
        public static function get_competency_criteria_parameters() {
                
                $id = new external_value(
                PARAM_INT,
                'Competency ID'
                );
                return new external_function_parameters(
                        array('filter' => new external_single_structure(
                                array('id' => $id)
                        )));
        }
        /**
         * Return the existing competency
         *
         * @param array $filter
         *
         * @return array
         * @throws \required_capability_exception
         * @throws invalid_parameter_exception
         */
        public static function get_competency_criteria($filter) {
                global $DB, $PAGE;

                $params = self::validate_parameters(self::get_competency_criteria_parameters(), array('filter' => $filter));
                $params = $params['filter'];
                // # if an exception is thrown in the below code, all DB queries in this code will be rollback.
                $transaction = $DB->start_delegated_transaction(); 

                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/hierarchy:viewcompetencyframeworks', $context);
                $output = $PAGE->get_renderer('core');

                $results = local_totarahola_lib::get_competency_criteria($params['id']);
                $records = array();
                foreach($results as $result)
                {
                array_push($records, $result);
                }
                $transaction->allow_commit();
                return $records;
        }
        /**
         * Returns description of list_competency result value.
         *
         * @return \external_description
         */
        public static function get_competency_criteria_returns() {
                return new external_multiple_structure(
                        new external_single_structure(
                        array(
                                'id' => new external_value(PARAM_INT, 'course id'),
                                'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                                'category' => new external_value(PARAM_INT, 'course category'),
                                'idnumber' => new external_value(PARAM_TEXT, 'course id number'),
                                'visible' => new external_value(PARAM_INT, 'course visible'),
                                'timemodified' => new external_value(PARAM_INT, 'Date of modification'),
                                'timecreated' => new external_value(PARAM_INT, 'Date of creation'),
                                'usermodified' => new external_value(PARAM_INT, 'The id of the creator s course'),
                                'fullname' => new external_value(PARAM_TEXT, 'The fullname of the course')
                        )   
                ));
        }
        /**
        * Returns description of method parameters
        * @return external_function_parameters
        */
        public static function get_user_learning_plan_parameters()
        {
                $sort = new external_value(
                        PARAM_TEXT,
                        'Ordering criteria by default ASC on id column',
                        VALUE_DEFAULT,
                        'ASC'
                );
                $column = new external_value(
                        PARAM_TEXT,
                        'Sort column',
                        VALUE_DEFAULT,
                        ''
                );
                return new external_function_parameters(
                        array(
                                'filter' => new external_single_structure(
                                        array('sort' => $sort, 'column' => $column)
                                )
                        )
                );
        }
        /**
         * Get popular list's user learning plan
         *
         * @return array
         */
        public static function get_user_learning_plan($filter) {
                global $PAGE, $DB;
              
                $params = self::validate_parameters(self::get_user_learning_plan_parameters(), array('filter' => $filter));
                $params = $params['filter'];
                if($params['column'] == '')
                {
                    $params['column'] = 'id';
                }

                # if an exception is thrown in the below code, all DB queries in this code will be rollback.
                $transaction = $DB->start_delegated_transaction(); 

                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/plan:accessanyplan', $context);
                $output = $PAGE->get_renderer('core');

                $results = local_totarahola_lib::get_user_learning_plan($params['sort'], $params['column']);
                $records = array();
                foreach ($results as $result) {
                        array_push($records, $result);
                }
                $transaction->allow_commit();
                return $records;
        }
        /**
         * Returns description of method result value
         *
         * @return external_description
         * @since Moodle 2.2
         */
        public static function get_user_learning_plan_returns() {
                return new external_multiple_structure(
                        new external_single_structure(
                                array(
                                'id' => new external_value(PARAM_INT, 'user id'),
                                'username' => new external_value(PARAM_TEXT, 'username'),
                                'firstname' => new external_value(PARAM_TEXT, 'firstname'),
                                'lastname' => new external_value(PARAM_TEXT, 'lastname')
                                )
                        )
                );
        }
        /**
         * Returns description of learning_plan() parameters.
         *
         * @return \external_function_parameters
         */
        public static function set_learning_plan_parameters() {
                
                $userid = new external_value(
                        PARAM_INT,
                        'Id competency to want to retrieve'
                );
                $name = new external_value(
                        PARAM_TEXT,
                        'learning plan name'
                );
                $description = new external_value(
                        PARAM_RAW,
                        'learning plan description',
                        VALUE_DEFAULT,
                        ''
                );
                $startdate = new external_value(
                        PARAM_INT,
                        'learning plan start date'
                );
                $enddate = new external_value(
                        PARAM_INT,
                        'learning plan end date'
                );
                $courses = new external_value(
                        PARAM_TEXT,
                        'learning plan courses ids, string separated by coma'
                );
                
                return new external_function_parameters(
                        array('playload' => new external_single_structure(
                                array(
                                        'userid' => $userid,
                                        'name' => $name,
                                        'description' => $description,
                                        'startdate' => $startdate,
                                        'enddate' => $enddate
                                        )
                                ),
                                'courses' => new external_single_structure(
                                        array('ids' => $courses)
                                )
                ));
        }
        /**
         * add new user learning plan
         *
         * @param array $playload
         * @param string $courses
         *
         * @return array
         * @throws \required_capability_exception
         * @throws invalid_parameter_exception
         */
        public static function set_learning_plan($playload, $courses) {
                global $DB, $PAGE;

                $params = self::validate_parameters(self::set_learning_plan_parameters(), array('playload' => $playload, 'courses' => $courses));
                $playload = $params['playload'];
                $courses = $params['courses'];
                # if an exception is thrown in the below code, all DB queries in this code will be rollback.
                $transaction = $DB->start_delegated_transaction(); 

                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/plan:accessanyplan', $context);
                $output = $PAGE->get_renderer('core');

                if ($playload['userid'] == '' || $playload['name'] == '' || $playload['startdate'] <= 0 || $playload['enddate' <= 0]) {
                        throw new invalid_parameter_exception('Invalid parameters.');
                }
                $template = local_totarahola_lib::get_default_lp_template();
                
                // record new learning plan
                $id = local_totarahola_lib::set_learning_plan(
                        $template,
                        $playload['name'], 
                        $playload['userid'], 
                        $playload['desciption'],
                        $playload['startdate'],
                        $playload['enddate']
                );
                $object_return = array();

                $courses = explode(',', $courses);
                try{
                        for($i = 0; $i < count($courses); $i++)
                        {
                                $course = new object();
                                $course->planid = $id;
                                $course->courseid = $courses[0];
                                $DB->insert_record('dp_plan_course_assign', $course, false, false);
                        }
                        $object_return['status'] = 201;
                        $object_return['message'] = "learning plan added successful";
                }catch(Exception $e)
                {
                        $object_return['status'] = 400;
                        $object_return['message'] = 'Error adding course '.$e->getMessage();
                }
                $transaction->allow_commit();
                return $object_return;
                // return array();
        }
        /**
         * Returns description of method result value
         *
         * @return external_description
         */
        public static function set_learning_plan_returns() {
                return new external_single_structure(
                        array(
                                'message' => new external_value(PARAM_TEXT, 'recorded message'),
                                'status' => new external_value(PARAM_INT, 'response status'))
                        );
        }
        /**
         * Returns description of get_learning_plan() parameters.
         *
         * @return \external_function_parameters
         */
        public static function get_learning_plan_parameters() {
                
                $userid = new external_value(
                        PARAM_INT,
                        'user id',
                );
                return new external_function_parameters(
                        array('filter' => new external_single_structure(
                                array('userid' => $userid))
                             
                ));
        }
        public static function get_learning_plan($filter)
        {
                global $DB, $PAGE;

                $params = self::validate_parameters(self::get_learning_plan_parameters(), array('filter' => $filter));
                $params = $params['filter'];
                # if an exception is thrown in the below code, all DB queries in this code will be rollback.
                $transaction = $DB->start_delegated_transaction(); 

                $context = context_system::instance();
                self::validate_context($context);
                require_capability('totara/plan:accessanyplan', $context);
                $output = $PAGE->get_renderer('core');

                $results = local_totarahola_lib::get_learning_plan($params['userid']);
                $records = array();
                foreach($results as $result)
                {
                        array_push($records, $result);
                }
                $transaction->allow_commit();
                return $records;
        }
        /**
         * Returns description of method result value
         *
         * @return external_description
         */
        public static function get_learning_plan_returns() {
                return new external_multiple_structure(
                        new external_single_structure(
                                array(
                                'id' => new external_value(PARAM_INT, 'learning plan id'),
                                'name' => new external_value(PARAM_TEXT, 'learning plan name'),
                                'description' => new external_value(PARAM_RAW, 'learning plan description'),
                                'startdate' => new external_value(PARAM_INT, 'learning plan Start date'),
                                'enddate' => new external_value(PARAM_INT, 'learning plan End date')
                                )
                        )
                );
        }
}