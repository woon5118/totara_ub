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

namespace mod_perform\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;

use mod_perform\models\activity\track as track_model;
use totara_core\advanced_feature;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles the "mod_perform_track" GraphQL query.
 */
class track implements query_resolver {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        advanced_feature::require('performance_activities');

        $track_id = (int)$args['track_id'] ?? 0;
        if (!$track_id) {
            throw new \invalid_parameter_exception('invalid track id');
        }

        $track = track_model::load_by_id($track_id);
        self::setup_env($track, $ec);

        return $track;
    }

    /**
     * Checks whether the user is authenticated and sets the correct context
     * for the graphql execution.
     *
     * @param track_model $track target track.
     * @param execution_context $ec graphql execution context to update.
     */
    private static function setup_env(track_model $track, execution_context $ec): void {
        $activity = $track->activity;

        [$course, $cm] = get_course_and_cm_from_instance($activity->get_id(), 'perform');
        \require_login($course, false, $cm, false, true);

        $ec->set_relevant_context($activity->get_context());
    }
}
