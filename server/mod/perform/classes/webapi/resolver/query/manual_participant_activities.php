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

namespace mod_perform\webapi\resolver\query;

use core\entities\user;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;

/**
 * Get the activities and their manual relationships that the current user can set the participants for.
 *
 * @package mod_perform\webapi\resolver\query
 */
class manual_participant_activities implements query_resolver, has_middleware {

    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $current_user_id = user::logged_in()->id;

        // TODO: Need to set the proper context in TL-26147
        $ec->set_relevant_context(\context_user::instance($current_user_id));

        // TODO: Return actual data in TL-26147
        return self::get_dummy_data();
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            new require_login(),
        ];
    }

    /**
     * TODO: REMOVE IN TL-26147
     */
    public static function get_dummy_data(): array {
        $activities = self::get_dummy_activities();

        $dummy_manual_relationships = [
            ['name' => 'Peer', 'id' => 4],
            ['name' => 'Client', 'id' => 5],
            ['name' => 'Mentor', 'id' => 6],
            ['name' => 'Reviewer', 'id' => 7],
        ];

        $data_to_return = [];
        foreach ($activities as $activity) {
            $item = ['activity' => $activity, 'manual_relationships' => []];

            foreach ($dummy_manual_relationships as $i => $dummy) {
                $dummy_relationship = (object) array_merge($dummy, [
                    'created_at' => time(),
                    'name_plural' => $dummy['name'] . 's',
                ]);

                if (random_int(0, 1)) {
                    $item['manual_relationships'][] = $dummy_relationship;
                }
            }

            $data_to_return[] = (object) $item;
        }

        return $data_to_return;
    }

    /**
     * TODO: REMOVE IN TL-26147
     * @return \mod_perform\models\activity\activity[]
     */
    private static function get_dummy_activities(): array {
        $existing_test_activity_count = \mod_perform\entities\activity\activity::repository()
            ->where_like_starts_with('name', 'Manual Relationship Test')
            ->count();
        if ($existing_test_activity_count === 4) {
            return \mod_perform\entities\activity\activity::repository()
                ->where_like_starts_with('name', 'Manual Relationship Test')
                ->get()
                ->map_to(\mod_perform\models\activity\activity::class)
                ->all();
        }

        global $CFG;
        require_once($CFG->dirroot . '/lib/testing/generator/component_generator_base.php');
        require_once($CFG->dirroot . '/lib/testing/generator/data_generator.php');
        require_once($CFG->dirroot . '/mod/perform/tests/generator/mod_perform_generator.class.php');
        /** @var \mod_perform_generator $generator */
        $generator = new \mod_perform_generator(new \testing_data_generator());
        /** @var \mod_perform\models\activity\activity[] $activities */
        return [
            $generator->create_activity_in_container(['activity_name' => 'Manual Relationship Test Activity One']),
            $generator->create_activity_in_container(['activity_name' => 'Manual Relationship Test Activity Two']),
            $generator->create_activity_in_container(['activity_name' => 'Manual Relationship Test Activity Three']),
            $generator->create_activity_in_container(['activity_name' => 'Manual Relationship Test Activity Four']),
        ];
    }

}
