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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_learning_plan
 */

namespace pathway_learning_plan\webapi\resolver\query;

use context_user;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use pathway_learning_plan\models\competency_plan;

class competency_plans implements query_resolver {

    /**
     * Queries learning plans and competency plan values for a given user and assignment
     *
     * @param array $args
     * @param execution_context $ec
     * @return competency_plan
     */
    public static function resolve(array $args, execution_context $ec) {
        global $USER;

        require_login(null, false, null, false, true);

        $user_id = $args['user_id'];
        $capability = $USER->id == $user_id ? 'totara/competency:view_own_profile' : 'totara/competency:view_other_profile';
        require_capability($capability, context_user::instance($user_id));

        return competency_plan::for_assignment($args['assignment_id'], $user_id);
    }

}
