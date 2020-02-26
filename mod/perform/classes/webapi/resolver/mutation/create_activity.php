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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\mutation;

use container_perform\create_exception;
use container_perform\perform as perform_container;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use mod_perform\models\activity\activity;

class create_activity implements mutation_resolver {

    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        global $DB;

        if (!activity::can_create()) {
            throw new create_exception(get_string('error:create_permission_missing', 'mod_perform'));
        }

        if (empty($args['name'])) {
            throw new create_exception(get_string('error:activity_name_missing', 'mod_perform'));
        }

        $courseinfo = new \stdClass();
        $courseinfo->fullname = $args['name'];
        $courseinfo->description = $args['description'] ?? null;
        $courseinfo->category = perform_container::get_default_categoryid();

        /** @var activity $activity */
        $activity =  $DB->transaction(function () use ($courseinfo, $args) {
            $container = perform_container::create($courseinfo);

            // Create a performance activity inside the new performance container.
            $activity_data = new \stdClass();
            $activity_data->name = $args['name'];
            $activity_data->description = $args['description'] ?? null;
            $activity_data->status = $args['status'] ?? activity::STATUS_ACTIVE;

            /** @var perform_container $container */
            return activity::create($activity_data, $container);
        });

        return ['activity' => $activity];
    }
}