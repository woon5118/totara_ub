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
 * @author Ben Lobo <ben.lobo@kineo.com>
 * @package totara
 * @subpackage program
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/** @deprecated since Totara 13 */
define('EXCEPTIONTYPE_TIME_ALLOWANCE', 1);
/** @deprecated since Totara 13 */
define('EXCEPTIONTYPE_ALREADY_ASSIGNED', 2);
/** @deprecated since Totara 13 */
define('EXCEPTIONTYPE_COMPLETION_TIME_UNKNOWN', 4);
/** @deprecated since Totara 13 */
define('EXCEPTIONTYPE_UNKNOWN', 5);
/** @deprecated since Totara 13 */
define('EXCEPTIONTYPE_DUPLICATE_COURSE', 6);

/** @deprecated since Totara 13 */
define('SELECTIONTYPE_NONE', 0);
/** @deprecated since Totara 13 */
define('SELECTIONTYPE_ALL', -1);
/** @deprecated since Totara 13 */
define('SELECTIONTYPE_TIME_ALLOWANCE', 1);
/** @deprecated since Totara 13 */
define('SELECTIONTYPE_ALREADY_ASSIGNED', 2);
/** @deprecated since Totara 13 */
define('SELECTIONTYPE_COMPLETION_TIME_UNKNOWN', 4);
/** @deprecated since Totara 13 */
define('SELECTIONTYPE_DUPLICATE_COURSE', 5);

/** @deprecated since Totara 13 */
define('SELECTIONACTION_NONE', 0);
/** @deprecated since Totara 13 */
define('SELECTIONACTION_AUTO_TIME_ALLOWANCE', 1);
/** @deprecated since Totara 13 */
define('SELECTIONACTION_OVERRIDE_EXCEPTION', 2);
/** @deprecated since Totara 13 */
define('SELECTIONACTION_DISMISS_EXCEPTION', 3);

/** @deprecated since Totara 13 */
define('RESULTS_PER_PAGE', 50);

/** @deprecated since Totara 13 */
class prog_exceptions_manager {

    protected $programid;
    protected $selectedexceptions;

    public $exceptiontype_classnames;
    private $exceptiontype_descriptors;
    private $exception_actions;

    function __construct($programid) {
        debugging(__CLASS__ . ' has been deprecated. Use an equivalent \totara_program\exception\manager class', DEBUG_DEVELOPER);

        $this->programid = $programid;
        $this->selectedexceptions = array();

        $this->exceptiontype_classnames = array(
            EXCEPTIONTYPE_TIME_ALLOWANCE    => 'time_allowance_exception',
            EXCEPTIONTYPE_ALREADY_ASSIGNED  => 'already_assigned_exception',
            EXCEPTIONTYPE_COMPLETION_TIME_UNKNOWN => 'completion_time_unknown_exception',
            EXCEPTIONTYPE_UNKNOWN => 'unknown_exception',
            EXCEPTIONTYPE_DUPLICATE_COURSE  => 'duplicate_course_exception'
        );

        $this->exceptiontype_descriptors = array(
            EXCEPTIONTYPE_TIME_ALLOWANCE    => get_string('timeallowance', 'totara_program'),
            EXCEPTIONTYPE_ALREADY_ASSIGNED  => get_string('exceptiontypealreadyassigned', 'totara_program'),
            EXCEPTIONTYPE_COMPLETION_TIME_UNKNOWN  => get_string('completiontimeunknown', 'totara_program'),
            EXCEPTIONTYPE_UNKNOWN => get_string('unknownexception', 'totara_program'),
            EXCEPTIONTYPE_DUPLICATE_COURSE  => get_string('exceptiontypeduplicatecourse', 'totara_program')
        );

        $this->exception_actions = array(
            SELECTIONACTION_NONE,
            SELECTIONACTION_AUTO_TIME_ALLOWANCE,
            SELECTIONACTION_OVERRIDE_EXCEPTION,
            SELECTIONACTION_DISMISS_EXCEPTION,
        );
    }

    public function get_selected_exceptions() {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        return $this->selectedexceptions;
    }

    /**
     * Adds a new exception.
     *
     * @param int $exceptiontype
     * @param int $userid
     * @param int $assignmentid
     * @param int $timeraised
     * @return <type>
     */
    public function raise_exception($exceptiontype, $userid, $assignmentid, $timeraised=null) {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        if (prog_exception::exception_exists($this->programid, $exceptiontype, $userid)) {
            // Return true if this exception has already been raised
            return true;
        }
        return prog_exception::insert_exception($this->programid, $exceptiontype, $userid, $assignmentid, $timeraised);
    }

    /**
     * Override and complete the assignment for a user with a previously dismissed exception.
     * This should never be used, however dismissed exceptions are handled badly so we have to
     *
     * @param userid
     * @return bool
     */
    public function override_dismissed_exception($userid) {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        global $DB;

        $assignparams = array('programid' => $this->programid, 'userid' => $userid);
        $assignments = $DB->get_records('prog_user_assignment', $assignparams);
        if (!empty($assignments)) {
            // Update all the dismissed exceptions at once.
            $assignsql = 'UPDATE {prog_user_assignment}
                             SET exceptionstatus = ' . PROGRAM_EXCEPTION_RESOLVED . '
                           WHERE exceptionstatus = ' . PROGRAM_EXCEPTION_DISMISSED . '
                             AND userid = :userid
                             AND programid = :programid';
            $DB->execute($assignsql, $assignparams);
        } else {
            throw new ProgramExceptionException(get_string('error:assignmentnotfound', 'totara_program'));
        }

        // There shouldn't be any exception records here but just to be safe.
        $exceptions = $DB->get_records('prog_exception', $assignparams);
        foreach ($exceptions as $exception) {
            // Only delete the ones we have edited above.
            if ($assignments[$exception->assignmentid]->exceptionstatus === PROGRAM_EXCEPTION_DISMISSED) {
                prog_exception::delete_exception($exception->id);
            }
        }

        // Record the change in the program completion log.
        prog_log_completion(
            $this->programid,
            $userid,
            'Program Assignment Exception - Overridden a previously dismissed exception'
        );

        // Event trigger to send notification when exception is resolved.
        $event = \totara_program\event\program_assigned::create(
            array(
                'objectid' => $this->programid,
                'context' => context_program::instance($this->programid),
                'userid' => $userid,
            )
        );
        $event->trigger();

        return true;
    }

    /**
     * Deletes an exception from the database
     *
     * @param <type> $exceptionid
     */
    public function delete_exception($exceptionid) {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        return prog_exception::delete_exception($exceptionid);
    }

    /**
     * Deletes all exceptions and exception data relating to a specific assignment
     * from the database
     *
     * @global object $CFG
     * @param int $assignmentid
     * @param int $userid (optional)
     * @return bool Success status
     */
    public static function delete_exceptions_by_assignment($assignmentid, $userid=0) {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $exceptionselect = "assignmentid = ?";
        $params = array($assignmentid);
        if ($userid) {
            $exceptionselect .= " AND userid = ?";
            $params[] = $userid;
        }

        $DB->delete_records_select('prog_exception', $exceptionselect, $params);
        // Deleted exceptions, now update exception status for user assignments.
        $update_sql = "UPDATE {prog_user_assignment} SET exceptionstatus = 0 WHERE assignmentid = ?";
        $params = array($assignmentid);
        if ($userid) {
            $update_sql .= " AND userid = ?";
            $params[] = $userid;
        }

        $DB->execute($update_sql, $params);
        $transaction->allow_commit();
        return true;
    }

    /**
     * Deletes all exceptions for this program
     *
     * @return true
     */
    public function delete() {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        global $DB;

        return $DB->delete_records('prog_exception', array('programid' => $this->programid));
    }

    public function handle_exceptions($action, $formdata) {
        debugging(__METHOD__ . ' has been deprecated and should no longer be used.', DEBUG_DEVELOPER);

        foreach ($this->selectedexceptions as $selectedexception) {
            return $this->handle_exception($selectedexception->id, $action);
        }
    }

    public function count_exceptions() {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        global $DB;

        $sql = "SELECT COUNT(ex.id)
                  FROM {prog_exception} ex
            INNER JOIN {user} us ON us.id = ex.userid
                 WHERE ex.programid = ? AND us.deleted = ?";

        return $DB->count_records_sql($sql, array($this->programid, 0));
    }

    public function handle_exception($exceptionid, $action) {
        debugging(__METHOD__ . ' has been deprecated and should no longer be used', DEBUG_DEVELOPER);

        global $DB;
        if (!$exception = $DB->get_record('prog_exception', array('id' => $exceptionid))) {
            throw new ProgramExceptionException(get_string('exceptionnotfound', 'totara_program'));
        }

        if (!array_key_exists($exception->exceptiontype, $this->exceptiontype_classnames)) {
            throw new ProgramExceptionException(get_string('exceptiontypenotfound', 'totara_program'));
        }

        $exception_classname = $this->exceptiontype_classnames[$exception->exceptiontype];
        $exceptionob = new $exception_classname($exception);

        return $exceptionob->handle($action);
    }

    /**
     * Creates an array containing the ids of all the exceptions that match a
     * specific selection criteia
     *
     * @param int $selectiontype
     * @return bool
     */
    public function set_selections($selectiontype, $searchterm='') {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        if ($selectiontype == SELECTIONTYPE_ALL) {
            $this->selectedexceptions = $this->search_exceptions('all', $searchterm);
        } else if ($selectiontype == SELECTIONTYPE_NONE) {
            $this->selectedexceptions = array();
        } else {
            switch($selectiontype) {
                case SELECTIONTYPE_TIME_ALLOWANCE:
                    $exceptiontype = EXCEPTIONTYPE_TIME_ALLOWANCE;
                    break;
                case SELECTIONTYPE_ALREADY_ASSIGNED:
                    $exceptiontype = EXCEPTIONTYPE_ALREADY_ASSIGNED;
                    break;
                case SELECTIONTYPE_COMPLETION_TIME_UNKNOWN:
                    $exceptiontype = EXCEPTIONTYPE_COMPLETION_TIME_UNKNOWN;
                    break;
                case SELECTIONTYPE_DUPLICATE_COURSE:
                    $exceptiontype = EXCEPTIONTYPE_DUPLICATE_COURSE;
                    break;
                default:
                    $exceptiontype = EXCEPTIONTYPE_UNKNOWN;
                    break;
            }
            $this->selectedexceptions = $this->search_exceptions('all', $searchterm, $exceptiontype);

        }

        return true;

    }

    public function search_exceptions($page='all', $searchterm='', $exceptiontype='', $count=false) {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        global $DB;

        $usernamefields = get_all_user_name_fields(true, 'us', null, 'user_');

        $fields = "ex.*, {$usernamefields}, us.id as userid";
        if ($count) {
            $fields = 'COUNT(ex.id)';
        }

        $sql = "SELECT $fields
        FROM {prog_exception} ex
        INNER JOIN {user} us ON us.id = ex.userid
        WHERE ex.programid = ? AND us.deleted = 0";
        $params = array($this->programid);
        if (!empty($exceptiontype)) {
            $sql .= " AND ex.exceptiontype = ?";
            $params[] = $exceptiontype;
        }

        if (!empty($searchterm)) {
            if (is_numeric($searchterm)) {
                $sql .= " AND us.id = ?";
                $params[] = $searchterm;
            } else {
                $sql .= " AND " . $DB->sql_like($DB->sql_concat('us.firstname', "' '", 'us.lastname'), '?', false);
                $params[] = '%' . $DB->sql_like_escape($searchterm) . '%';
            }
        }

        if ($count) {
            return $DB->count_records_sql($sql, $params);
        }

        $limit = is_int($page) ? RESULTS_PER_PAGE : '';
        $offset = is_int($page) ? (($page) * RESULTS_PER_PAGE) : '';

        $exceptions = $DB->get_records_sql($sql, $params, $offset, $limit);

        return $exceptions;

    }

    public function print_exceptions_form($programid, $exceptions, $selectedexceptions=null, $selectiontype=SELECTIONTYPE_NONE) {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        global $PAGE;
        $numexceptions = count($exceptions);
        $numselectedexceptions = count($selectedexceptions);

        $tabledata = array();

        foreach ($exceptions as $exception) {
            $rowdata = new stdClass();

            $user = new stdClass();
            $user->id = $exception->userid;
            foreach (get_all_user_name_fields() as $field) {
                $varname = "user_{$field}";
                $user->$field = $exception->$varname;
            }
            $rowdata->exceptionid = $exception->id;
            $rowdata->user = $user;
            $rowdata->firstname = $user->firstname;
            $rowdata->lastname = $user->lastname;
            $rowdata->selected = isset($selectedexceptions[$exception->id]) ? true : false;

            if (isset($this->exceptiontype_descriptors[$exception->exceptiontype])) {
                $exceptiontype = $exception->exceptiontype;
                $descriptor = $this->exceptiontype_descriptors[$exceptiontype];
            } else {
                $exceptiontype = EXCEPTIONTYPE_UNKNOWN;
                $descriptor = $this->exceptiontype_descriptors[$exceptiontype];
            }

            $rowdata->exceptiontype = $exceptiontype;
            $rowdata->descriptor = $descriptor;

            $tabledata[] = $rowdata;
        }
        $renderer = $PAGE->get_renderer('totara_program');
        echo $renderer->print_exceptions_form($numexceptions, $numselectedexceptions, $programid, $selectiontype, $tabledata);
    }


    /**
     * Get a multidimensional array of the different exception types and the
     * actions that they handle. Can be returned as an array or as a JSON encoded
     * string
     *
     * @param str $returntype
     * @return array|string
     */
    public function get_handled_actions($returntype='array') {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        global $CFG;

        // Build a list of exceptions and their handled actions
        $handledActions = array();
        foreach ($this->exceptiontype_classnames as $exception_class) {
            $exception = new $exception_class($this->programid);
            $handledActions[$exception->exceptiontype] = array();
            foreach ($this->exception_actions as $action) {
                $handledActions[$exception->exceptiontype][$action] = $exception->handles($action);
            }
        }

        if ($returntype == 'json') {
            return json_encode($handledActions);
        } else {
            return $handledActions;
        }
    }

    /**
     * Get an array specifying which of the defined actions can be handled by
     * the currently selectedexceptions. Can be returned as an array or as a
     * JSON encoded string
     *
     * @param str $returntype
     * @return array|string
     */
    public function get_handled_actions_for_selection($returntype='array', $selectedexceptions=null) {
        debugging(__METHOD__ . ' has been deprecated. Use equivalent from \totara_program\exception\manager class', DEBUG_DEVELOPER);

        global $CFG;

        if ($selectedexceptions == null) {
            $selectedexceptions = $this->selectedexceptions;
        }

        if (empty($selectedexceptions)) {
            $handledActions = array(
                SELECTIONACTION_AUTO_TIME_ALLOWANCE     => false,
                SELECTIONACTION_OVERRIDE_EXCEPTION      => false,
                SELECTIONACTION_DISMISS_EXCEPTION       => false,
            );
        } else {
            // Build a list of exceptions and their handled actions
            $handledActions = array(
                SELECTIONACTION_AUTO_TIME_ALLOWANCE     => true,
                SELECTIONACTION_OVERRIDE_EXCEPTION      => true,
                SELECTIONACTION_DISMISS_EXCEPTION       => true,
            );

            foreach ($selectedexceptions as $selectedexception) {
                if (isset($this->exceptiontype_classnames[$selectedexception->exceptiontype])) {
                    $classname = $this->exceptiontype_classnames[$selectedexception->exceptiontype];
                } else {
                    $classname = $this->exceptiontype_classnames[EXCEPTIONTYPE_UNKNOWN];
                }

                $exceptionob = new $classname($this->programid, $selectedexception);

                foreach ($handledActions as $action => $handles) {
                    if (!$exceptionob->handles($action)) {
                        $handledActions[$action] = false;
                    }
                }
            }
        }

        if ($returntype == 'json') {
            return json_encode($handledActions);
        } else {
            return $handledActions;
        }
    }

}

/** @deprecated since Totara 13 */
class ProgramExceptionException extends Exception {

}
