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
 * @author Simon Coggins <simon.coggins@totaralms.com>
 * @author Aaron Barnes <aaron.barnes@totaralms.com>
 * @package totara
 * @subpackage totara_hierarchy
 *
 * @deprecated since Totara 13.0
 */

use core\orm\query\order;
use totara_competency\entity\competency_achievement;

require_once("{$CFG->dirroot}/completion/data_object.php");


/**
 * Competency evidence
 */
class competency_evidence extends data_object {

    /**
     * Database table
     * @var string
     */
    public $table = 'comp_record';

    /**
     * Database required fields
     * @var array
     */
    public $required_fields = array(
        'id', 'userid', 'competencyid', 'positionid', 'organisationid',
        'assessorid', 'assessorname', 'assessmenttype', 'proficiency',
        'timeproficient', 'timecreated', 'timemodified', 'reaggregate'
    );

    public $userid;
    public $competencyid;
    public $positionid;
    public $organisationid;
    public $assessorid;
    public $assessorname;
    public $assessmenttype;
    public $proficiency;
    public $timeproficient;
    public $timecreated;
    public $timemodified;
    public $reaggregate;

    final public function __construct($params = null, $fetch = true) {
        debugging('\competency_evidence has been deprecated, please use \totara_competency\entity\competency_achievement.', DEBUG_DEVELOPER);
    }

    /**
     * Finds and returns a data_object instance based on params.
     *
     * @param array $params associative arrays varname => value
     * @return object data_object instance or false if none found.
     *
     * @deprecated since Totara 13.0
     */
    public static function fetch($params) {
        debugging('\competency_evidence::fetch has been deprecated, please use \totara_competency\entity\competency_achievement .',
            DEBUG_DEVELOPER
        );

        return false;
    }

    /**
     * Trigger a reaggregation of this evidence item
     *
     * @return  void
     *
     * @deprecated since Totara 13.0
     */
    public function trigger_reaggregation() {
        debugging('\competency_evidence\trigger_reaggregation has been deprecated. Re-aggregation is now triggered automatically',
            DEBUG_DEVELOPER
        );

        return false;
    }

    /**
     * Update the user's proficiency for this evidence item
     *
     * @param   $proficiency    int
     * @return  void
     *
     * @deprecated since Totara 13.0
     */
    public function update_proficiency($proficiency) {
        debugging('\competency_evidence\update_proficiency has been deprecated. Please use \dp_competency_component\set_value',
            DEBUG_DEVELOPER
        );

        return false;
    }

    /**
     * Save an evidence record (create or update as neccessary)
     *
     * Also, create any parent records if they do not exist.
     *
     * @return  void
     */
    private function _save() {
        $now = time();

        // Set up some stuff
        if (!$this->timecreated) {
            $this->timecreated = $now;
        }

        $this->timemodified = $now;

        // Update database
        if (!$this->id) {
            if (!$this->insert()) {
                print_error('insertevidenceitem', 'totara_hierarchy');
            }
        }
        else {
            if (!$this->update()) {
                print_error('updateevidenceitem', 'totara_hierarchy');
            }
        }

        // Insert a history record.
        $this->insert_comp_record_history();
    }

    /**
     * Insert a comp_record_history record.
     * Uses the values from $this to construct the history record.
     * This is only used to store changes to proficiency.
     * This will only save if the proficiency has changed, so it is safe to call multiple times.
     */
    private function insert_comp_record_history() {
        global $DB, $USER;

        $currenthistory = $DB->get_records('comp_record_history',
                array('userid' => $this->userid, 'competencyid' => $this->competencyid), 'timemodified DESC');

        if (empty($currenthistory) || $this->proficiency != reset($currenthistory)->proficiency) {
            $history = new stdClass();
            $history->userid = $this->userid;
            $history->competencyid = $this->competencyid;
            $history->proficiency = $this->proficiency;
            $history->timeproficient = $this->timeproficient;
            $history->timemodified = time();
            $history->usermodified = $USER->id;

            return $DB->insert_record('comp_record_history', $history);
        }
    }

    /**
     * Trigger reaggregation of any parent competencies
     *
     * @return  void
     */
    private function _trigger_parent_reaggregation() {
        global $DB;

        // Check if this competency has a parent
        $competency = $DB->get_record('comp', array('id' => $this->competencyid));

        if (!$competency->parentid) {
            return;
        }

        $pevidence = new competency_evidence(
            array(
                'competencyid'  => $competency->parentid,
                'userid'        => $this->userid
            )
        );

        // Save parent's competency evidence. This will create the record
        // if it doesn't exist, and recursively call parent reaggregation
        $pevidence->trigger_reaggregation();
    }
}
