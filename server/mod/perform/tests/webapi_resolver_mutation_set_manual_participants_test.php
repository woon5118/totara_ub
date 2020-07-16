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

use totara_core\advanced_feature;
use totara_core\feature_not_available_exception;
use totara_core\relationship\relationship;
use totara_webapi\phpunit\webapi_phpunit_helper;

require_once(__DIR__ . '/subject_instance_set_participant_users_test.php');

/**
 * @group perform
 */
class mod_perform_webapi_resolver_mutation_set_manual_participants_manual_testcase extends advanced_testcase {

    private const MUTATION = 'mod_perform_set_manual_participants';

    use webapi_phpunit_helper;

    /**
     * Note: This is really just a sanity check to see that the mutation resolver works.
     * Most of the actual logic and functionality is tested in {@see mod_perform_subject_instance_set_participant_users_testcase}.
     */
    public function test_ajax_mutation_successful(): void {
        self::setAdminUser();
        $data = new manual_participant_selector_test_data();
        $data->create_data();

        // Relationships
        $mentor_relationship = relationship::load_by_idnumber('perform_mentor');
        $reviewer_relationship = relationship::load_by_idnumber('perform_reviewer');

        self::setUser($data->manager_user);
        $result = $this->mutate($data->act1_user1_subject_instance->id, [
            $mentor_relationship->id => [$data->manager_user->id],
            $reviewer_relationship->id => [$data->appraiser_user->id, $data->user2->id],
        ]);
        $this->assertTrue($result['success'], 'Mutation failed');

        // We get an exception if we try to set the participants again since it can they can only be set once.
        $this->expectException(coding_exception::class);
        $this->mutate($data->act1_user1_subject_instance->id, [
            $mentor_relationship->id => [$data->manager_user->id],
        ]);
    }

    public function test_ajax_mutation_fails_when_not_logged_in(): void {
        $this->expectException(require_login_exception::class);

        self::setUser();
        $result = $this->mutate(-1, []);
        $this->assert_webapi_operation_failed($result, 'not logged in');
    }

    public function test_ajax_mutation_fails_when_feature_disabled(): void {
        $this->expectException(feature_not_available_exception::class);

        self::setAdminUser();
        advanced_feature::disable('performance_activities');
        $result = $this->mutate(-1, []);
        $this->assert_webapi_operation_failed($result, 'Feature performance_activities is not available.');
    }

    /**
     * @param int $subject_instance_id
     * @param int[][] $relationships_and_participants Array of $relationship_id => [$participant_user_id]
     * @return array Mutation result.
     */
    private function mutate(int $subject_instance_id, array $relationships_and_participants): array {
        return $this->resolve_graphql_mutation(self::MUTATION, [
            'subject_instance_id' => $subject_instance_id,
            'participants' => array_map(static function ($relationship_id) use ($relationships_and_participants) {
                return [
                    'manual_relationship_id' => $relationship_id,
                    'user_ids' => $relationships_and_participants[$relationship_id],
                ];
            }, array_keys($relationships_and_participants)),
        ]);
    }

}
