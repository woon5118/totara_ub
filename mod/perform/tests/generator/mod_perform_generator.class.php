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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

use container_perform\perform as perform_container;

use core\collection;
use core_container\module\module;
use mod_perform\models\activity\section_relationship as section_relationship_model;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\section;
use mod_perform\models\activity\element;
use mod_perform\models\activity\section_element;
use mod_perform\models\activity\track;
use mod_perform\models\activity\track_assignment_type;
use mod_perform\user_groups\grouping;

/**
 * Perform generator
 */
class mod_perform_generator extends component_generator_base {

    /**
     * Create a performance activity and a performance container to contain it
     *
     * @param array $data
     * @return activity
     */
    public function create_activity_in_container($data = []): activity {
        global $DB;

        $container_data = new stdClass();
        $container_data->name = $data['container_name'] ?? "test performance container";
        $container_data->category = \mod_perform\util::get_default_categoryid();

        return $DB->transaction(function () use ($data, $container_data) {
            $container = perform_container::create($container_data);

            // Create a performance activity inside the new performance container.
            $activity_data = new \stdClass();
            $name = $data['activity_name'] ?? "test performance activity";
            $description = $data['description'] ?? "test description";
            $status = $data['activity_status'] ?? activity::STATUS_ACTIVE;

            /** @var perform_container $container */
            $activity = activity::create($container, $name, $description, $status);

            return activity::load_by_id($activity->get_id());
        });
    }

    /**
     * Creates only a performance activity module
     *
     * This function is required by module generators.
     *
     * @param array $data
     * @return module
     */
    public function create_instance($data = []): module {
        $name = $data['name'] ?? "test performance activity";
        $description = $data['description'] ?? "test description";
        $status = $data['status'] ?? activity::STATUS_ACTIVE;

        $container = perform_container::from_id($data['course']);

        /** @var perform_container $container */
        activity::create($container, $name, $description, $status);

        $modules = $container->get_section(0)->get_all_modules();
        $module = reset($modules);

        return $module;
    }

    public function create_section(activity $activity, $data = []): section {
        $title =  $data['title'] ?? "test Section";
        return section::create($activity, $title);
    }

    public function create_section_element(section $section, element $element) {
        return section_element::create($section, $element);
    }

    public function create_element(array $data = []) {
        return element::create(
            $data['context'] ?? context_coursecat::instance(perform_container::get_default_categoryid()),
            $data['plugin_name'] ?? 'short_text',
            $data['title'] ?? 'test element title',
            $data['identifier'] ?? 0,
            $data['data'] ?? null
        );
    }

    public function create_section_relationship(section $section, array $data): section_relationship_model {
        return section_relationship_model::create($section->get_id(), $data['class_name']);
    }

    /**
     * Creates a set of tracks for the given activity.
     *
     * @param activity $activity parent activity.
     * @param int $track_count no of tracks to generate
     *
     * @return collection $tracks the generated tracks.
     */
    public function create_activity_tracks(activity $activity, int $track_count=1): collection {
        return collection::new(range(0, $track_count - 1))
            ->map_to(
                function (int $i) use ($activity): track {
                    return track::create($activity, "track #$i");
                }
            );
    }

    /**
     * Creates one track with one cohort assignment for the given activity.
     *
     * @param activity $activity parent activity.
     *
     * @return track $track the generated track.
     */
    public function create_single_activity_track_and_assignment(activity $activity): track {
        $track = track::create($activity, "test track");
        return $this->create_track_assignments($track, 1, 0, 0, 0);
    }

    /**
     * Creates a set of track assignments for the given track.
     *
     * @param track $track parent track.
     * @param int $cohort_count no of cohorts to generate for assignments.
     * @param int $org_count no of organizations to generate for assignments.
     * @param int $pos_count no of positions to generate for assignments.
     * @param int $user_count no of users to generate for assignments.
     *
     * @return track the updated track.
     */
    public function create_track_assignments(
        track $track,
        int $cohort_count=1,
        int $org_count=1,
        int $pos_count=1,
        int $user_count=1
    ): track {
        $pos = [];
        $hierarchies = $this->datagenerator->get_plugin_generator('totara_hierarchy');
        if ($pos_count > 0) {
            $data = ['frameworkid' => $hierarchies->create_pos_frame([])->id];

            foreach (range(0, $pos_count - 1) as $unused) {
                $pos[] = $hierarchies->create_pos($data)->id;
            }
        }

        $orgs = [];
        if ($org_count > 0) {
            $data = ['frameworkid' => $hierarchies->create_org_frame([])->id];

            foreach (range(0, $org_count - 1) as $unused) {
                $orgs[] = $hierarchies->create_org($data)->id;
            }
        }

        $cohorts = [];
        if ($cohort_count > 0) {
            foreach (range(0, $cohort_count - 1) as $unused) {
                $cohorts[] = $this->datagenerator->create_cohort()->id;
            }
        }

        $users = [];
        if ($user_count > 0) {
            foreach (range(0, $user_count - 1) as $unused) {
                $users[] = $this->datagenerator->create_user()->id;
            }
        }

        return $this->create_track_assignments_with_existing_groups($track, $cohorts, $orgs, $pos, $users);
    }

    /**
     * Creates a set of track assignments (of admin type) for the given track.
     *
     * @param track $track parent track.
     * @param int[] $cohorts cohort ids to assign.
     * @param int[] $orgs organization ids to assign.
     * @param int[] $pos position ids to assign.
     * @param int[] $users user ids to assign.
     *
     * @return track the updated track.
     */
    public function create_track_assignments_with_existing_groups(
        track $track,
        array $cohorts = [],
        array $orgs = [],
        array $pos = [],
        array $users = []
    ): track {
        $assignments = [];
        foreach ($cohorts as $id) {
            $assignments[] = grouping::cohort($id);
        }
        foreach ($orgs as $id) {
            $assignments[] = grouping::org($id);
        }
        foreach ($pos as $id) {
            $assignments[] = grouping::pos($id);
        }
        foreach ($users as $id) {
            $assignments[] = grouping::user($id);
        }

        $assign_type = track_assignment_type::ADMIN;
        return collection::new($assignments)
            ->reduce(
                function (track $interim, grouping $group) use ($assign_type): track {
                    return $interim->add_assignment($assign_type, $group);
                },
                $track
            );
    }
}