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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Marco Song <marco.song@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\relationship\resolvers\reviewer;
use totara_core\relationship\relationship;
use totara_core\relationship\relationship_resolver;

abstract class perform_relationship_resolver_test extends advanced_testcase {
    /**
     * Get resolver for different manual relationships
     *
     * @param string $idnumber
     * @return relationship_resolver
     * @throws coding_exception
     */
    protected function get_resolver(string $idnumber): relationship_resolver {
        $core_relationship_reviewer = relationship::load_by_idnumber($idnumber);
        return new reviewer($core_relationship_reviewer);
    }

    /**
     * create manual relationship resolver data
     *
     * @param string $idnumber
     * @return array
     * @throws coding_exception
     */
    protected function create_relationship_resolver_data(string $idnumber) {
        $data_generator = $this->getDataGenerator();
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');
        // user
        $user1 = $data_generator->create_user();
        $user2 = $data_generator->create_user();

        $this->setAdminUser();

        // activity
        $activity = $perform_generator->create_activity_in_container(['activity_name' => 'User1 One Activity']);

        //subject instance
        $subject_instance = $perform_generator->create_subject_instance(
            [
                'activity_id'     => $activity->id,
                'subject_user_id' => $user1->id,
            ]
        );

        //subject_instance_manual_participants
        $subject_instance_manual_participants = $perform_generator->create_subject_instance_manual_participant(
            [
                'subject_instance_id'        => $subject_instance->id,
                'user_ids'                   => [$user1->id],
                'core_relationship_idnumber' => $idnumber,
                'created_by'                 => $user2->id,
            ]
        );

        return [$user1, $subject_instance, $subject_instance_manual_participants];
    }
}