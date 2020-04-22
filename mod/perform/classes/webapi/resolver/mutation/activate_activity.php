<?php
/*
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
 * @author Fabian Derschatta <fabiab.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use mod_perform\models\activity\activity;
use moodle_exception;
use totara_core\advanced_feature;

class activate_activity implements mutation_resolver {

    /**
     * This activates the activity
     *
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('performance_activities');
        require_login(null, false, null, false, true);

        $args = $args['input'];

        try {
            $activity = activity::load_by_id($args['activity_id']);
        } finally {
            if (!isset($activity) || !$activity->can_manage()) {
                throw new moodle_exception('invalid_activity', 'mod_perform');
            }
        }

        $ec->set_relevant_context($activity->get_context());

        // If activity is already active just return
        if ($activity->is_active()) {
            return ['activity' => $activity];
        }

        if (!$activity->can_activate()) {
            throw new moodle_exception('error_activate', 'mod_perform');
        }

        $activity->activate();

        return ['activity' => $activity];
    }
}