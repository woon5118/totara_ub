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

use mod_perform\models\activity\track;
use mod_perform\models\activity\track_assignment_type;

use mod_perform\user_groups\grouping;
use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles the "mod_perform_add_track_assignments" GraphQL mutation.
 */
class add_track_assignments implements mutation_resolver {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('performance_activities');

        $assignments = $args['assignments'] ?? null;
        if (!$assignments) {
            throw new \invalid_parameter_exception("no assignments given'");
        };

        $track_id = $assignments['track_id'] ?? null;
        if (!$track_id) {
            throw new \invalid_parameter_exception('invalid track id');
        }

        $track = track::load_by_id($track_id);
        self::setup_env($track, $ec);

        $assignment_type = $assignments['type'] ?? track_assignment_type::ADMIN;
        $groups = $assignments['groups'] ?? [];

        foreach ($groups as $group) {
            $group_id = $group['id'] ?? 0;
            if (!$group_id) {
                throw new \invalid_parameter_exception('invalid group id');
            }

            $group_type = $group['type'];
            $grouping = grouping::by_type($group_type, $group_id);

            $track->add_assignment($assignment_type, $grouping);
        }

        return $track;
    }

    /**
     * Checks whether the user is authenticated and sets the correct context
     * for the graphql execution.
     *
     * @param track $track target track.
     * @param execution_context $ec graphql execution context to update.
     */
    private static function setup_env(track $track, execution_context $ec): void {
        $activity = $track->activity;

        [$course, $cm] = get_course_and_cm_from_instance($activity->get_id(), 'perform');
        \require_login($course, false, $cm, false, true);

        $ec->set_relevant_context($activity->get_context());
    }
}
