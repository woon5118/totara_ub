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

namespace totara_job\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use totara_job\job_assignment;
use totara_job\webapi\resolver\helper;

/**
 * Query to return all of the job assignments belonging to a user.
 */
class assignments implements query_resolver, has_middleware {

    use helper;

    /**
     * Returns all of the job assignments belong to a user.
     *
     * @param array $args
     * @param execution_context $ec
     * @return job_assignment[]
     */
    public static function resolve(array $args, execution_context $ec) {
        global $CFG;

        require_once($CFG->dirroot . '/totara/job/lib.php');

        $user = self::get_user_from_args($args, 'userid', false);
        if (!totara_job_can_view_job_assignments($user)) {
            throw new \moodle_exception('nopermissions', '', '', 'view job assignments');
        }

        return job_assignment::get_all($user->id);
    }

    public static function get_middleware(): array {
        return [
            require_login::class
        ];
    }

}