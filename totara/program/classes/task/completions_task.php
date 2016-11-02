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
 * @author Valerii Kuznetsov <valerii.kuznetsov@totaralms.com>
 * @package totara_program
 */

namespace totara_program\task;

class completions_task extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('completionstask', 'totara_program');
    }

    /**
     * Determine whether or not any users have completed any programs
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/totara/program/lib.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

        // Don't run programs cron if programs and certifications are disabled.
        if (totara_feature_disabled('programs') &&
            totara_feature_disabled('certifications')) {
            return false;
        }

        // OK we want to find all programs that have at least one user assigned who has not completed the program already.
        // Once a user has completed the program we no longer need to check that user. Complete is complete.
        $programs = \program::get_all_programs_with_incomplete_users();
        foreach ($programs as $program) {
            // Get all the users enrolled on this program who have not already completed it.
            $program_users = $program->get_program_learners(STATUS_PROGRAM_INCOMPLETE);
            if (empty($program_users)) {
                continue;
            }

            foreach ($program_users as $userid) {
                prog_update_completion($userid, $program, false);
            }
        }

        return true;
    }
}

