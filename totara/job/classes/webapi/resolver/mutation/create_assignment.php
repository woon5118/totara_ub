<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package totara_job
 */

namespace totara_job\webapi\resolver\mutation;

use \core\webapi\execution_context;
use \totara_job\job_assignment;

/**
 * Mutation to create a job assignment.
 */
class create_assignment implements \core\webapi\mutation_resolver {

    use \totara_job\webapi\resolver\helper;

    /**
     * Creates an assignment and returns the new assignment id.
     *
     * @param array $args
     * @param execution_context $ec
     * @return int
     */
    public static function resolve(array $args, execution_context $ec) {
        global $CFG;

        require_once($CFG->dirroot . '/totara/job/lib.php');

        // TL-21305 will find a better, encapsulated solution for require_login calls.
        require_login(null, false, null, false, true);

        $user = self::get_user_from_args($args, 'userid', false);
        // They have to be able to view and edit the job assignments in order to create.
        if (!\totara_job_can_edit_job_assignments($user->id) || !\totara_job_can_view_job_assignments($user)) {
            throw new \coding_exception('No permission to create job assignments.');
        }

        $jobassignment = new \stdClass();
        $jobassignment->userid = $user->id;
        $jobassignment->idnumber = $args['idnumber'];
        $jobassignment->fullname = $args['fullname'] ?? null;
        $jobassignment->shortname = $args['shortname'] ?? null;
        $jobassignment->description = $args['description'] ?? null;
        if (!totara_feature_disabled('positions')) {
            $jobassignment->positionid = $args['positionid'] ?? null;
        }
        $jobassignment->organisationid = $args['organisationid'] ?? null;
        $jobassignment->startdate = $args['startdate'] ?? null;
        $jobassignment->enddate = $args['enddate'] ?? null;
        $jobassignment->managerjaid = $args['managerjaid'] ?? null;
        $jobassignment->tempmanagerjaid = $args['tempmanagerjaid'] ?? null;
        $jobassignment->tempmanagerexpirydate = $args['tempmanagerexpirydate'] ?? null;
        $jobassignment->appraiserid = $args['appraiserid'] ?? null;
        $jobassignment->totarasync = $args['totarasync'] ?? null;

        $job = job_assignment::create($jobassignment);
        return $job->id;
    }
}