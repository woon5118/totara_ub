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
}
