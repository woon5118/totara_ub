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
use core\entities\user;
use core_container\module\module;
use mod_perform\entities\activity\participant_section;
use mod_perform\expand_task;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\models\activity\track;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\activity\activity;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\models\activity\section;
use mod_perform\models\activity\element;
use mod_perform\models\activity\section_element;
use mod_perform\models\activity\section_relationship as section_relationship_model;
use mod_perform\models\activity\track_assignment_type;
use mod_perform\state\participant_instance\not_started as instance_not_started;
use mod_perform\state\participant_section\not_started;
use mod_perform\task\service\subject_instance_creation;
use mod_perform\user_groups\grouping;
use mod_perform\util;
use totara_core\entities\relationship_resolver;
use totara_core\relationship\relationship;
use totara_core\relationship\resolvers\subject;

/**
 * Perform generator
 */
class mod_perform_generator extends component_generator_base {

    /**
     * @var array
     */
    private $cache;

    public function __construct(testing_data_generator $datagenerator) {
        require_once __DIR__.'/activity_name_generator.php';
        require_once __DIR__.'/activity_generator_configuration.php';
        parent::__construct($datagenerator);
    }

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
        $container_data->category = util::get_default_category_id();

        return $DB->transaction(function () use ($data, $container_data) {
            $container = perform_container::create($container_data);

            // Create a performance activity inside the new performance container.
            $name = $data['activity_name'] ?? "test performance activity";
            $description = $data['description'] ?? "test description";
            $status = $data['activity_status'] ?? activity::STATUS_ACTIVE;

            /** @var perform_container $container */
            $activity = activity::create($container, $name, $description, $status);

            if (isset($data['create_section']) && $data['create_section']) {
                section::create($activity);
            }

            return $activity;
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

    public function create_section_element(section $section, element $element, $sort_order = 1): section_element {
        return section_element::create($section, $element, $sort_order);
    }

    public function create_participant_section(
        activity $activity,
        participant_instance_entity $participant_instance,
        $add_elements = true,
        section $section = null
    ): participant_section_entity {
        if ($section === null) {
            $section = $this->create_section($activity, ['title' => 'Part one']);
        }

        $participant_section =  new participant_section_entity();
        $participant_section->section_id = $section->id;
        $participant_section->participant_instance_id = $participant_instance->id;
        $participant_section->status = not_started::get_code();
        $participant_section->save();

        if ($add_elements) {
            $element = $this->create_element(['title' => 'Question one']);
            $this->create_section_element($section, $element);

            $element2 = $this->create_element(['title' => 'Question two']);
            $this->create_section_element($section, $element2, 2);
        }

        return $participant_section;
    }

    public function create_element(array $data = []): element {
        return element::create(
            $data['context'] ?? context_coursecat::instance(perform_container::get_default_category_id()),
            $data['plugin_name'] ?? 'short_text',
            $data['title'] ?? 'test element title',
            $data['identifier'] ?? 0,
            $data['data'] ?? null
        );
    }

    public function create_section_relationship(section $section, array $data): section_relationship_model {
        $relationship = $this->get_relationship($data['class_name']);
        return section_relationship_model::create($section->get_id(), $relationship->id);
    }

    public function get_relationship(string $class_name): relationship {
        if (!isset($this->cache['relationships'][$class_name])) {
            /** @var relationship_resolver|null $resolver */
            $resolver = relationship_resolver::repository()
                ->with('relationship')
                ->where('class_name', $class_name)
                ->order_by('id')
                ->first();

            if (isset($resolver)) {
                $relationship = relationship::load_by_entity($resolver->relationship);
            } else {
                $relationship = relationship::create([$class_name]);
            }

            $this->cache['relationships'][$class_name] = $relationship;
        }

        return $this->cache['relationships'][$class_name];
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

    /**
     * Create full activities including assignments, subject and participant instances
     *
     * @param mod_perform_activity_generator_configuration $configuration
     * @return collection
     */
    public function create_full_activities(mod_perform_activity_generator_configuration $configuration = null) {
        global $USER;

        // Create a default configuration if it wasn't provided
        if ($configuration === null) {
            $configuration = mod_perform_activity_generator_configuration::new();
        }

        $previous_user = clone $USER;

        // For the activity generation we need to make sure the admin user is set
        \advanced_testcase::setAdminUser();

        $activity_name_generator = new mod_perform_activity_name_generator();

        $activities = [];
        for ($i = 0; $i < $configuration->get_number_of_activities(); $i++) {
            $data = [
                'activity_name' => $activity_name_generator->generate()
            ];

            $activity = $this->create_activity_in_container($data);
            $section = $this->create_section($activity, ['title' => $activity->name . ' section']);
            $this->create_section_relationship($section, ['class_name' => subject::class]);
            $this->create_activity_tracks($activity);
            $activities[] = $activity;
        }

        foreach ($activities as $activity) {
            $cohorts = [];
            for ($i = 0; $i < $configuration->get_cohort_assignments_per_activity(); $i++) {
                $cohort = $this->datagenerator->create_cohort();
                $cohorts[] = $cohort->id;
                for ($k = 0; $k < $configuration->get_number_of_users_per_user_group_type(); $k++) {
                    $user = $this->datagenerator->create_user();
                    cohort_add_member($cohort->id, $user->id);
                }
            }

            $track = track::load_by_activity($activity)->first();
            $this->create_track_assignments_with_existing_groups($track, $cohorts);
        }

        // Expand assignments to user assignments
        if ($configuration->should_generate_user_assignments()) {
            (new expand_task())->expand_all();
        }
        if ($configuration->should_generate_subject_instances()) {
            // Create subject instances for all user assignments
            (new subject_instance_creation())->generate_instances();
        }

        \advanced_testcase::setUser($previous_user);

        return collection::new($activities);
    }

    /**
     * Creates a user activity (subject_instance) with one participant and optionally the subject participating too.
     *
     * The top level perform activity is created if a name (activity_name) or id (activity_id) is not supplied,
     * otherwise the perform row will be looked id or name if supplied.
     *
     * The subject can either be identified by 'subject_user_id' (user.id) or 'subject_username' (user.username).
     *
     * The (other) participant can either be identified by 'other_participant_id' (user.id) or
     * 'other_participant_username' (user.username) or left out.
     *
     * @param array $data
     * @return subject_instance_entity
     * @throws coding_exception
     */
    public function create_subject_instance(array $data): subject_instance_entity {
        $activity_id = $data['activity_id'] ?? null;

        if ($activity_id) {
            $activity = activity::load_by_id($activity_id);
        } else {
            $activity = $this->find_or_make_perform_activity($data['activity_name'] ?? null);
        }

        $subject_id = $data['subject_user_id'] ?? null;

        if ($subject_id) {
            $subject = user::repository()->find($subject_id);
        } else {
            /** @var user $subject */
            $subject = user::repository()
                ->where('username', $data['subject_username'])
                ->order_by('id')
                ->first();
        }

        $other_participant_id = $data['other_participant_id'] ?? null;
        $other_participant_username = $data['other_participant_username'] ?? null;
        $other_participant = null;

        if ($other_participant_id) {
            $other_participant = user::repository()->find($other_participant_id);
        } else if ($other_participant_username) {
            /** @var user $other_participant */
            $other_participant = user::repository()
                ->where('username', $other_participant_username)
                ->order_by('id')
                ->first();
        }

        $subject_is_participating = $data['subject_is_participating'] ?? false;

        // String conversion for behat
        if (is_string($subject_is_participating) && $subject_is_participating !== 'true') {
            $subject_is_participating = false;
        }

        $track = track::create($activity, "track for {$activity->name}");

        $user_assignment = new track_user_assignment();
        $user_assignment->track_id =  $track->id;
        $user_assignment->subject_user_id = $subject->id;
        $user_assignment->deleted = false;
        $user_assignment->save();

        $subject_instance = new subject_instance_entity();
        $subject_instance->track_user_assignment_id = $user_assignment->id;
        $subject_instance->subject_user_id = $user_assignment->subject_user_id; // Purposeful denormalization
        $subject_instance->save();

        $subject_is_participating = $data['subject_is_participating'] ?? false;
        // String conversion for behat, defaulting to false.
        if (is_string($subject_is_participating) && $subject_is_participating !== 'true') {
            $subject_is_participating = false;
        }

        $subjects_participant_instance = null;
        if ($subject_is_participating) {
            $subjects_participant_instance = new participant_instance_entity();
            $subjects_participant_instance->activity_relationship_id = 0; // stubbed
            $subjects_participant_instance->participant_id = $subject->id; // Answering on activity about them self
            $subjects_participant_instance->subject_instance_id = $subject_instance->id;
            $subjects_participant_instance->status = instance_not_started::get_code();
            $subjects_participant_instance->save();
        }

        $other_participant_instance = null;
        if ($other_participant) {
            $other_participant_instance = new participant_instance_entity();
            $other_participant_instance->activity_relationship_id = 0; // stubbed
            $other_participant_instance->participant_id = $other_participant->id;
            $other_participant_instance->subject_instance_id = $subject_instance->id;
            $other_participant_instance->status = instance_not_started::get_code();
            $other_participant_instance->save();
        }

        $include_questions = $data['include_questions'] ?? true;

        // String conversion for behat, defaulting to true.
        if ($include_questions === 'false') {
            $include_questions = false;
        }

        if ($include_questions) {
            foreach ([$subjects_participant_instance, $other_participant_instance] as $participant_instance) {
                if ($participant_instance === null) {
                    continue;
                }

                $this->create_participant_section($activity, $participant_instance);
            }
        }

        return $subject_instance;
    }

    private function find_or_make_perform_activity($name): activity {
        if (!$name) {
            return $this->create_activity_in_container();
        }

        /** @var activity_entity $activity_entity */
        $activity_entity = activity_entity::repository()->where('name', $name)->order_by('id')->first();

        if ($activity_entity === null) {
            return $this->create_activity_in_container(['activity_name' => $name]);
        }

        return activity::load_by_entity($activity_entity);
    }


}