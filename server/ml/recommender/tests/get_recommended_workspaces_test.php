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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package ml_recommender
 */
defined('MOODLE_INTERNAL') || die();

use core\webapi\execution_context;
use totara_core\advanced_feature;
use totara_webapi\graphql;

/**
 * Test the endpoints for recommending workspaces by workspace or user
 */
class ml_recommender_get_recommended_workspaces_testcase extends advanced_testcase {
    /**
     * Test workspaces are recommended by workspace id
     */
    public function test_recommended_workspaces_graphql() {
        $generator = $this->getDataGenerator();
        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        $this->setAdminUser();

        $user = $generator->create_user();
        $this->setUser($user);

        /** @var ml_recommender_generator $recommendations_generator */
        $recommendations_generator = $generator->get_plugin_generator('ml_recommender');

        // This is our target workspace. We're going to ask for recommendations related to this workspace
        $target_workspace = $workspace_generator->create_workspace(
            'Target Workspace',
            'Summary',
            null,
            $user->id
        );
        $target_workspace2 = $workspace_generator->create_workspace(
            'Target Workspace 2',
            'Summary',
            null,
            $user->id
        );

        // Going to create a few workspaces, then recommend *some* of them
        $workspaces = [];
        for ($i = 1; $i <= 10; $i++) {
            $workspace = $workspace_generator->create_workspace(
                'W' . $i,
                'Summary',
                null,
                $user->id
            );
            $workspaces[] = $workspace;

            // Recommend it if it's > 5
            if ($i > 5) {
                $recommendations_generator->create_item_recommendation(
                    $target_workspace->get_id(),
                    $workspace->get_id(),
                    $workspace::get_type(),
                    null,
                    2.5
                );
            }
        }

        // Now we're going to ask for some recommended workspaces
        advanced_feature::enable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_workspaces');
        $parameters = [
            'workspace_id' => $target_workspace->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertEmpty($result->errors);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['workspaces'];

        $this->assertEquals(5, $cursor['total']);
        $this->assertCount(5, $results);

        // Quick check
        $expected = ['W5', 'W6', 'W7', 'W8', 'W9', 'W10'];
        foreach ($results as $result) {
            $this->assertTrue(in_array($result['name'], $expected));
        }

        // Now check for no results
        $parameters = [
            'workspace_id' => $target_workspace2->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['workspaces'];

        $this->assertEquals(0, $cursor['total']);
        $this->assertCount(0, $results);

        // Test disabled feature
        advanced_feature::disable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_workspaces');
        $parameters = [
            'workspace_id' => $target_workspace->get_id(),
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['workspaces'];

        $this->assertNull($cursor);
        $this->assertEmpty($results);
    }

    /**
     * Test workspaces are recommended by user id
     */
    public function test_recommended_workspaces_by_user_graphql() {
        $generator = $this->getDataGenerator();
        /** @var container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');

        $this->setAdminUser();

        $user = $generator->create_user();
        $this->setUser($user);

        /** @var ml_recommender_generator $recommendations_generator */
        $recommendations_generator = $generator->get_plugin_generator('ml_recommender');

        // We're going to recommend for user 2
        $user2 = $generator->create_user();

        // Going to create a few workspaces, then recommend *some* of them
        $workspaces = [];
        for ($i = 1; $i <= 10; $i++) {
            $workspace = $workspace_generator->create_workspace(
                'W' . $i,
                'Summary',
                null,
                $user->id
            );
            $workspaces[] = $workspace;

            // Recommend it if it's > 5
            if ($i > 5) {
                $recommendations_generator->create_user_recommendation(
                    $user2->id,
                    $workspace->get_id(),
                    $workspace::get_type(),
                    null,
                    2.5
                );
            }
        }

        // Now we're going to ask for some recommended workspaces
        advanced_feature::enable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_user_workspaces');
        $parameters = [
            'user_id' => $user2->id,
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);

        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['workspaces'];

        $this->assertEquals(5, $cursor['total']);
        $this->assertCount(5, $results);

        // Quick check
        $expected = ['W5', 'W6', 'W7', 'W8', 'W9', 'W10'];
        foreach ($results as $result) {
            $this->assertTrue(in_array($result['name'], $expected));
        }

        // Now check for no results
        $parameters = [
            'user_id' => $user->id,
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['workspaces'];

        $this->assertEquals(0, $cursor['total']);
        $this->assertCount(0, $results);

        // Test disabled feature
        advanced_feature::disable('ml_recommender');
        $ec = execution_context::create('ajax', 'ml_recommender_get_recommended_user_workspaces');
        $parameters = [
            'user_id' => $user2->id,
            'cursor' => null,
        ];
        $result = graphql::execute_operation($ec, $parameters);
        $this->assertNotNull($result->data);

        $cursor = $result->data['cursor'];
        $results = $result->data['workspaces'];

        $this->assertNull($cursor);
        $this->assertEmpty($results);
    }
}