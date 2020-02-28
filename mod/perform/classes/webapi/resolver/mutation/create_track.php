<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTDvs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 * @author  Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\mutation_resolver;

use mod_perform\models\activity\activity;
use mod_perform\models\activity\track;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles the "mod_perform_create_track" GraphQL mutation.
 */
class create_track implements mutation_resolver {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $details = $args['details'] ?? null;
        if (!$details) {
            throw new \invalid_parameter_exception('new track details not given');
        }

        $activity_id = (int)$details['activity_id'] ?? 0;
        if (!$activity_id) {
            throw new \invalid_parameter_exception('unknown activity id');
        }

        $activity = activity::load_by_id($activity_id);
        self::setup_env($activity, $ec);

        $description = $details['description'] ?? '';

        return track::create($activity, $description);
    }

    /**
     * Checks whether the user is authenticated and sets the correct context
     * for the graphql execution.
     *
     * @param activity $activity parent activity.
     * @param execution_context $ec graphql execution context to update.
     */
    private static function setup_env(activity $activity, execution_context $ec): void {
        [$course, $cm] = get_course_and_cm_from_instance($activity->get_id(), 'perform');
        \require_login($course, false, $cm, false, true);

        $ec->set_relevant_context($activity->get_context());
    }
}
