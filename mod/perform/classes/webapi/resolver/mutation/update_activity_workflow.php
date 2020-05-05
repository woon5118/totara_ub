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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use mod_perform\models\activity\activity;
use totara_core\advanced_feature;
use required_capability_exception;

class update_activity_workflow implements mutation_resolver {

    /**
     * Updates the workflow options for an activity.
     *
     * @param array $args
     * @param execution_context $ec
     * @return activity
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('performance_activities');
        require_login(null, false, null, false, true);

        $input = $args['input'];
        $activity = activity::load_by_id($input['activity_id']);

        if (!$activity->can_manage()) {
            throw new required_capability_exception(
                $activity->get_context(),
                'mod/perform:manage_activity',
                'nopermission',
                ''
            );
        }

        $activity->set_close_on_completion($input['close_on_completion']);

        $ec->set_relevant_context($activity->get_context());

        return $activity;
    }

}
