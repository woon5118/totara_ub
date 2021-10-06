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

class local_totarahola_external extends external_api{

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
}