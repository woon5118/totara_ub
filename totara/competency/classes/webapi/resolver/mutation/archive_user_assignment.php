<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\webapi\resolver\mutation;

use core\entities\user;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use totara_competency\models\assignment;

class archive_user_assignment implements mutation_resolver {

    /**
     * Archives a user assignment.
     *
     * @param array $args Contain keys as specified in the mutation schema.
     * @param execution_context $ec Context.
     *
     * @return assignment
     */
    public static function resolve(array $args, execution_context $ec): assignment {
        $user_id = self::check_and_get_loggedin_user_id();
        $assignment = assignment::load_by_id($args['assignment_id']);

        if (!$assignment->can_archive($user_id)) {
            throw new \moodle_exception('error:archive_user_assignment', 'totara_competency');
        }
        $assignment->archive();

        return $assignment;
    }

    /**
     * Check and get logged in user id.
     *
     * @return int
     */
    private static function check_and_get_loggedin_user_id(): int {
        require_login(null, false, null, false, true);
        $user = user::logged_in();

        if (is_null($user)) {
            throw new \moodle_exception('error:usernotfound', 'totara_core');
        }

        return $user->id;
    }
}
