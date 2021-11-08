<?php

/**
 * @package  local_totarahola
 * @copyright 2021, Steven CIBAMBO, Videabiz <steven@videabiz.com>
 * @license MIT
 * @doc https://docs.moodle.org/dev/Plugin_files
 */
 
defined('MOODLE_INTERNAL') || die();

class local_totarahola_lib
{
    /**
     * Perform a search based on the provided id and return a competency.
     *
     * Requires totara/hierarchie:viewcompetencyframeworks capability at the system context.
     *
     * @param int $id The column to sort on 
     * @return array of competency_framework
     */
    public static function get_course($idcourse) {
        global $DB;

        $params = array();
        $validcolumns = array('id', 'shortname', 'description', 'idnumber', 'frameworkid',
            'parentid', 'visible', 'evidencecount', 'timecreated', 'fullname', 'depthlevel', 'typeid');

        $params['id'] = $idcourse;
        // OK - all set.
        return $DB->get_record('course', $params, $fields='*');
    }
     /**
     * Perform a search based on the provided id and return a section course.
     *
     * Requires totara/hierarchie:viewcourse capability at the system context.
     *
     * @param int $id The column to sort on 
     * @return array of course_section
     */
    public static function get_course_sections($idcourse) {
        global $DB;

        $params = array();
        $validcolumns = array('id', 'shortname', 'description', 'idnumber', 'frameworkid',
            'parentid', 'visible', 'evidencecount', 'timecreated', 'fullname', 'depthlevel', 'typeid');

        $params['course'] = $idcourse;
        // OK - all set.
        return $DB->get_records('course_sections', $params, $fields='*');
    }
    /**
     * Perform a search based on the provided filters and return list of records.
     *
     * Requires moodle/competency:competencyview capability at the system context.
     *
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param bool $onlyvisible If true return only visible frameworks
     * @return array of competency_framework
     */
    public static function get_frameworks($sort, $order, $onlyvisible) {
        global $DB;

        $params = array();
        $validcolumns = array('id', 'shortname', 'idnumber', 'fullname', 'timecreated');
        
        if (!in_array($sort, $validcolumns)) {
            throw new invalid_parameter_exception('Sort column was invalid');
        }
        // OK - all set.
        if ($onlyvisible) {
            $params['visible'] = 1;
        }
        $framework = $DB->get_records_sql(
            'SELECT f.id, f.fullname, f.visible, f.idnumber, f.description, 
                    f.shortname, f.usermodified, f.timecreated, f.timemodified, f.sortorder 
            FROM "ttr_comp_framework" f 
            JOIN "ttr_comp" c ON f.id = c.frameworkid 
            WHERE (f.visible = :visible)',
            [
                'visible' => $params['visible'],
            ]
        );
        return $framework;
    }
    /**
     * Perform a search based on the provided filters and return list of records.
     *
     * Requires totara/hierarchie:viewcompetencyframeworks capability at the system context.
     *
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip recods to leave before 
     * @param int $limit 
     * @return array of competency_framework
     */
    public static function get_competency_with_more_courses($sort, $order, $skip, $limit) {
        global $DB;

        $params = array();
        $validcolumns = array('id', 'shortname', 'description', 'idnumber', 'frameworkid',
            'parentid', 'visible', 'evidencecount', 'timecreated', 'fullname', 'depthlevel', 'typeid');

        if (!in_array($sort, $validcolumns)) {
                throw new invalid_parameter_exception('Filter column was invalid');
        }
        $params['visible'] = 1;
        // OK - all set.
        return $DB->get_records('comp', $params, $sort . ' ' . $order, '*', $skip, $limit);
    }
    /**
     * Perform a search based on the provided filters and return list of records.
     *
     * Requires totara/hierarchie:viewcompetencyframeworks capability at the system context.
     *
     * @param string $sort The column to sort on
     * @param string $order ('ASC' or 'DESC')
     * @param int $skip recods to leave before 
     * @param int $limit 
     * @return array of competency_framework
     */
    public static function get_framework_competencies($frameworkid, $sort, $order) {
        global $DB;

        $params = array();
        $validcolumns = array('id', 'shortname', 'description', 'idnumber', 'frameworkid',
            'parentid', 'visible', 'evidencecount', 'timecreated', 'fullname', 'depthlevel', 'typeid');

        if (!in_array($sort, $validcolumns)) {
                throw new invalid_parameter_exception('Filter column was invalid');
        }
        $params['frameworkid'] = $frameworkid;
        $params['visible'] = 1;
        // OK - all set.
        return $DB->get_records('comp', $params, $sort . ' ' . $order, '*');
    }
    /**
     * Perform a search based on the provided id and return a competency.
     *
     * Requires totara/hierarchie:viewcompetencyframeworks capability at the system context.
     *
     * @param int $id The column to sort on 
     * @return array of competency_framework
     */
    public static function get_competency($idcompetency) {
        global $DB;

        $params = array();
        $validcolumns = array('id', 'shortname', 'description', 'idnumber', 'frameworkid',
            'parentid', 'visible', 'evidencecount', 'timecreated', 'fullname', 'depthlevel', 'typeid');

        $params['id'] = $idcompetency;
        // OK - all set.
        return $DB->get_record('comp', $params, $fields='*');
    }
    /**
     * Perform a search based on the provided id and return a competency.
     *
     * Requires totara/hierarchie:viewcompetencyframeworks capability at the system context.
     *
     * @param int $id The column to sort on 
     * @return array of competency_framework
     */
    public static function get_competency_criteria($idcomp) {
        global $DB;

        $params = array();
        $validcolumns = array('id', 'shortname', 'description', 'idnumber', 'frameworkid',
            'parentid', 'visible', 'evidencecount', 'timecreated', 'fullname', 'depthlevel', 'typeid');

        $params['id'] = $idcompetency;
        // OK - all set.
        $courses = $DB->get_records_sql(
            'SELECT c.id as id, c.category as category, c.fullname as fullname, c.shortname as shortname, 
                    c.idnumber as idnumber, c.icon as icon, cc.competencyid as competency, 
                    cc.usermodified as usermodified, c.visible as visible, c.timecreated as timecreated, c.timemodified as timemodified
            FROM "ttr_course" c 
            JOIN "ttr_comp_criteria" cc ON c.id = cc.iteminstance 
            WHERE (cc.competencyid = :idcomp) AND (visible = 1)', ['idcomp' => $idcomp]
        );

        return $courses;
    }
    /**
     * Return a list of user learning plan.
     *
     * @param string $sort the sorting order
     * @param string $column the sorting column
     * @return array of user learning plan
     */
    public static function get_user_learning_plan($sort, $column) {
        global $DB;

        // OK - all set.
        $users = $DB->get_records_sql(
            'SELECT u.id as id, u.username as username, u.firstname as firstname, u.lastname as lastname, 
                    u.idnumber as idnumber, lp.name as lpname
            FROM "ttr_user" u
            JOIN "ttr_dp_plan" lp ON u.id = lp.userid
            WHERE (lp.status = 50)
            ORDER BY lp.id '.$sort
        );

        return $users;
    }
    /**
     * Return the id of the default learning plan
     */
    public static function get_default_lp_template()
    {
        global $DB;

        $id = $DB->get_field('dp_template', 'id', array('isdefault' => 1));
        // return id
        return $id;
    }
    /**
     * 
     */
    public static function set_learning_plan($template, $name, $userid, $description, $startdate, $enddate)
    {
        global $DB;
        $status = array('unapproved' => 10, 'approved' => 50);

        $learning_plan = new stdClass();
        $learning_plan->templateid = $template;
        $learning_plan->userid = $userid;
        $learning_plan->name = $name;
        $learning_plan->description = $description;
        $learning_plan->startdate = $startdate;
        $learning_plan->enddate = $enddate;
        $learning_plan->status = $status['approved'];
        $learning_plan->createdby = $userid;

        $lp = $DB->insert_record('dp_plan', $learning_plan, $returnid = true, $bulk = false);

        return $lp;
    }
    public static function get_learning_plan($userid)
    {
        global $DB;

        // OK - all set.
        $learning_plan = $DB->get_records_sql(
            'SELECT DISTINCT lp.id as id, lp.name as name, lp.description as description, lp.startdate as startdate, 
                    lp.enddate as enddate
            FROM "ttr_dp_plan" lp 
            JOIN "ttr_user" u ON u.id = lp.userid
            WHERE (lp.status = 50 AND lp.userid = '.$userid.')'
        );

        return $learning_plan;
    }
}
