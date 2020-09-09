<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_competency
 */

namespace degeneration;

use coding_exception;
use container_workspace\loader\discussion\loader as discussion_loader;
use container_workspace\loader\member\loader as member_loader;
use container_workspace\query\discussion\query as discussion_query;
use container_workspace\query\member\query as member_query;
use core\orm\collection;
use core\orm\query\builder;
use degeneration\items\audience;
use degeneration\items\competency;
use degeneration\items\competency_scale;
use degeneration\items\container_perform\activity as activity_item;
use degeneration\items\container_perform\preset_configurations;
use degeneration\items\container_workspace\workspace;
use degeneration\items\course;
use degeneration\items\course_completion;
use degeneration\items\course_completion_basic;
use degeneration\items\criteria\child_competency;
use degeneration\items\criteria\course_completion as course_completion_criterion;
use degeneration\items\criteria\linked_courses as linked_courses_criterion;
use degeneration\items\criteria\on_activate;
use degeneration\items\item;
use degeneration\items\organisation;
use degeneration\items\pathways\criteria_group;
use degeneration\items\position;
use degeneration\items\user;
use Exception;
use hierarchy_organisation\entities\organisation_framework;
use hierarchy_position\entities\position_framework;
use mod_perform\expand_task;
use mod_perform\task\service\subject_instance_creation;
use totara_competency\linked_courses;

class performance_testing extends App {

    /**
     * User ids
     *
     * @var int[]
     */
    protected $users = [];

    protected $users_with_courses = [];

    protected $courses_with_users = [];

    /**
     * @var course[]
     */
    protected $courses = [];

    /**
     * Created audiences
     *
     * @var audience[]
     */
    protected $audiences = [];

    /**
     * @var hierarchy|null
     */
    protected $competency_hierarchy = null;

    /**
     * @var array
     */
    protected $scales = [];

    /**
     * Should it create group assignment or user assignments
     *
     * @var bool
     */
    protected $assign_groups = true;

    /**
     * Check for if track user assignments were expanded.
     *
     * @var bool
     */
    private $user_assignments_expanded = false;

    /**
     * @var array
     */
    protected $workspaces = [];
    /**
     * @var array|position[]
     */
    private $positions = [];
    /**
     * @var array|organisation[]
     */
    private $organisations = [];

    /**
     * Enable / disable function calls here to control which data is generated
     * when you run this script.
     */
    public function generate() {
        // If you need caching enable it here.
        // Cached data is not used at the moment so disabled by default for now.
        Cache::disable();

        $this->create_users()
            ->create_organisations()
            ->create_positions()
            ->create_audiences()
            ->add_audience_members()
            ->create_job_assignments_for_user()
            ->perform_act_create_activities()
            // ->perform_act_expand_track_user_assignments()   // Use with care as will have considerable performance impact on generation
            // ->perform_act_generate_instances()              // Use with care as will have considerable performance impact on generation
            // ->create_scales()
            // ->create_competencies()
            // ->perform_comp_create_organisation_assignments()
            // ->perform_comp_create_position_assignments()
            // ->perform_comp_create_audience_assignments()
            // ->perform_comp_create_assignments()
            // ->create_courses()
            // ->enrol_users()                             // Enrolling users has a huge performance impact only activate if absolutely necessary
            // ->create_course_completions()
            // ->create_course_completions_basic()
            // ->add_linked_courses()
            // ->perform_comp_create_criteria()
            // ->engage_create_workspaces()
            // ->engage_create_workspace_members()
            // ->engage_create_workspace_discussions()
            // ->engage_create_workspace_discussion_comments_replies()
            ;
    }

    /**
     * Tweak numbers here
     *
     * @return array
     */
    public function get_xs_size() {
        return [
            'users' => 2000,
            'courses' => 10,
            'audiences' => 50,
            'users_per_audience' => 1000,
            'workspaces' => 1,
            'activities' => [
                'count' => 5,
                'audiences_per_activity' => 1,
            ],
            'workspace_members' => 2,
            'workspace_discussions' => 1,
            'workspace_discussion_comments' => 1,
            'organisations' => [3, 3],
            'positions' => [3, 3],
            'competencies' => [3, 3],
            'competency_assignments' => 100,
            'job_assignments_for_user' => 2,
            'enrolments' => 100,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 100, // <-- Note this is a percentage
            'workspace_discussion_replies' => 1,
        ];
    }

    /**
     * Tweak numbers here
     *
     * @return array
     */
    public function get_s_size() {
        return [
            'users' => 7000,
            'courses' => 100,
            'audiences' => 80,
            'users_per_audience' => 2500,
            'workspaces' => 10,
            'activities' => [
                'count' => 25,
                'audiences_per_activity' => 2,
            ],
            'workspace_members' => 5,
            'workspace_discussions' => 5,
            'workspace_discussion_comments' => 3,
            'organisations' => [4, 4],
            'positions' => [4, 4],
            'competencies' => [4, 4],
            'competency_assignments' => 100,
            'job_assignments_for_user' => 1,
            'enrolments' => 100,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 75, // <-- Note this is a percentage
            'workspace_discussion_replies' => 2,
        ];
    }

    /**
     * Tweak numbers here
     *
     * @return array
     */
    public function get_m_size() {
        return [
            'users' => 15000,
            'courses' => 100,
            'audiences' => 50,
            'users_per_audience' => 5000,
            'workspaces' => 100,
            'activities' => [
                'count' => 100,
                'audiences_per_activity' => 3,
            ],
            'workspace_members' => 100,
            'workspace_discussions' => 100,
            'workspace_discussion_comments' => 100,
            'workspace_discussion_replies' => 100,
            'organisations' => [4, 4],
            'positions' => [4, 4],
            'competencies' => [4, 4],
            'competency_assignments' => 80,
            'job_assignments_for_user' => 1,
            'enrolments' => 80,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 75, // <-- Note this is a percentage
        ];
    }

    /**
     * Tweak numbers here
     *
     * @return array
     */
    public function get_l_size() {
        return [
            'users' => 35000,
            'courses' => 100,
            'audiences' => 80,
            'users_per_audience' => 10000,
            'workspaces' => 100,
            'activities' => [
                'count' => 500,
                'audiences_per_activity' => 5,
            ],
            'workspace_members' => 250,
            'workspace_discussions' => 250,
            'workspace_discussion_comments' => 250,
            'workspace_discussion_replies' => 250,
            'organisations' => [4, 4],
            'positions' => [4, 4],
            'competencies' => [4, 4],
            'competency_assignments' => 100,
            'job_assignments_for_user' => 2,
            'enrolments' => 100,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 75, // <-- Note this is a percentage
        ];
    }

    /**
     * Tweak numbers here
     *
     * @return array
     */
    public function get_xl_size() {
        return [
            'users' => 75000,
            'courses' => 1000,
            'audiences' => 100,
            'users_per_audience' => 25000,
            'workspaces' => 1000,
            'activities' => [
                'count' => 1200,
                'audiences_per_activity' => 8,
            ],
            'workspace_members' => 500,
            'workspace_discussions' => 750,
            'workspace_discussion_comments' => 750,
            'workspace_discussion_replies' => 750,
            'organisations' => [5, 5],
            'positions' => [5, 5],
            'competencies' => [5, 5],
            'competency_assignments' => 70,
            'job_assignments_for_user' => 2,
            'enrolments' => 70,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 80, // <-- Note this is a percentage
        ];
    }

    /**
     * Tweak numbers here
     *
     * @return array
     */
    public function get_xxl_size() {
        return [
            'users' => 150000,
            'courses' => 1000,
            'audiences' => 120,
            'users_per_audience' => 50000,
            'workspaces' => 1000,
            'activities' => [
                'count' => 2500,
                'audiences_per_activity' => 10,
            ],
            'workspace_members' => 1000,
            'workspace_discussions' => 1000,
            'workspace_discussion_comments' => 1000,
            'organisations' => [5, 5],
            'positions' => [5, 5],
            'competencies' => [5, 5],
            'competency_assignments' => 70,
            'job_assignments_for_user' => 4,
            'enrolments' => 70,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 80, // <-- Note this is a percentage
            'workspace_discussion_replies' => 1000,
        ];
    }

    /**
     * Tweak numbers here
     *
     * @return array
     */
    public function get_goliath_size() {
        return [
            'users' => 300000,
            'courses' => 1000,
            'audiences' => 140,
            'users_per_audience' => 100000,
            'workspaces' => 1000,
            'activities' => [
                'count' => 5000,
                'audiences_per_activity' => 15,
            ],
            'workspace_members' => 1000,
            'workspace_discussions' => 1000,
            'workspace_discussion_comments' => 1000,
            'organisations' => [5, 5],
            'positions' => [5, 5],
            'competencies' => [6, 5],
            'competency_assignments' => 70,
            'job_assignments_for_user' => 5,
            'enrolments' => 70,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 80, // <-- Note this is a percentage
            'workspace_discussion_replies' => 1000,
        ];
    }

    public function create_users() {
        $this->output('Creating users...');

        $size = $this->get_item_size('users');

        builder::get_db()->transaction(function () use ($size) {
            $users = [];
            $total = $size / BATCH_INSERT_MAX_ROW_COUNT;
            $done = 0;
            for ($c = 1; $c <= $size; $c++) {
                $users[] = (object)user::new()->create_for_bulk();
                if (count($users) >= BATCH_INSERT_MAX_ROW_COUNT) {
                    builder::get_db()->insert_records('user', $users);
                    $users = [];

                    self::show_progress($done, $total);
                }
            }

            if (!empty($users)) {
                builder::get_db()->insert_records('user', $users);
            }
        });

        // We mostly need only the userid,
        // to save memory we only load those
        $this->users = user::load_existing_ids();

        return $this;
    }

    public function assign_groups(bool $assign = true) {
        $this->assign_groups = $assign;

        return $this;
    }

    public function perform_comp_create_assignments() {
        if (!$this->assign_groups) {
            $this->perform_comp_create_user_assignments();
        } else {
            $this->perform_comp_create_audience_assignments();
        }

        return $this;
    }

    public function create_organisations() {
        $this->output('Creating organisations...');

        builder::get_db()->transaction(function () {
            [$depth, $count] = $this->get_item_size('organisations');

            if (!$fw = organisation_framework::repository()->order_by('id')->first()) {
                $fw = (new organisation())->create_framework();
            }

            $hierarchy = new hierarchy();
            $hierarchy->set_type(organisation::class)
                ->set_variable_count(false)
                ->set_variable_depth(false)
                ->set_framework($fw)
                ->set_depth($depth)
                ->set_count($count)
                ->create_hierarchy();

            $this->organisations = $hierarchy->get_items();
        });

        return $this;
    }

    public function create_positions() {
        $this->output('Creating positions...');

        builder::get_db()->transaction(function () {
            [$depth, $count] = $this->get_item_size('positions');

            if (!$fw = position_framework::repository()->order_by('id')->first()) {
                $fw = (new position())->create_framework();
            }

            $hierarchy = new hierarchy();
            $hierarchy->set_type(position::class)
                ->set_variable_count(false)
                ->set_variable_depth(false)
                ->set_framework($fw)
                ->set_depth($depth)
                ->set_count($count)
                ->create_hierarchy();

            $this->positions = $hierarchy->get_items();
        });

        return $this;
    }

    public function create_audiences() {
        $this->output('Creating audiences...');

        builder::get_db()->transaction(function () {
            $size = $this->get_item_size('audiences');

            $done = 0;
            for ($c = 1; $c <= $size; $c++) {
                $this->audiences[] = audience::new()->save_and_return();

                self::show_progress($done, $size);
            }
        });

        return $this;
    }

    public function add_audience_members() {
        $this->output('Adding audience members...');

        if (empty($this->users)) {
            throw new Exception('You must create users first');
        }

        if (empty($this->audiences)) {
            throw new Exception('You must create audiences first');
        }

        builder::get_db()->transaction(function () {
            $user_per_audience = $this->get_item_size('users_per_audience');

            $users = $this->users;

            $audiences = $this->audiences;

            $members = [];
            $total = (count($audiences) * $user_per_audience) / BATCH_INSERT_MAX_ROW_COUNT;
            $done = 0;
            foreach ($audiences as $audience) {
                shuffle($users);

                $users = array_slice($users, 0, $user_per_audience);
                foreach ($users as $user) {
                    $members[] = $audience->add_member($user);
                    if (count($members) >= BATCH_INSERT_MAX_ROW_COUNT) {
                        builder::get_db()->insert_records('cohort_members', $members);
                        $members = [];

                        self::show_progress($done, $total);
                    }
                }
            }

            if (!empty($members)) {
                builder::get_db()->insert_records('cohort_members', $members);
            }
        });

        return $this;
    }

    public function create_scales() {
        $this->output('Creating competency scales...');

        builder::get_db()->transaction(function () {
            $simple_scale = new competency_scale();

            $simple_scale->add_value(false, 'Incompetent', $simple_incompetent)
                ->add_value(true, 'Competent', $simple_competent)
                ->save();

            $complex_scale = new competency_scale();

            $complex_scale->add_value(false, 'Incompetent', $complex_incompetent)
                ->add_value(false, 'A little less incompetent', $complex_less_competent)
                ->add_value(true, 'Competent ', $complex_competent)
                ->add_value(true, 'A little more competent ', $complex_more_competent)
                ->save();

            $this->scales = [
                $simple_scale->get_data('id') => [
                    'incompetent' => $simple_incompetent,
                    'competent' => $simple_competent,
                ],
                $complex_scale->get_data('id') => [
                    'incompetent' => $complex_incompetent,
                    'less_incompetent' => $complex_less_competent,
                    'competent' => $complex_competent,
                    'more_competent' => $complex_more_competent,
                ]
            ];
        });

        return $this;
    }

    public function create_competencies() {
        $this->output('Creating competencies...');

        builder::get_db()->transaction(function () {
            [$depth, $count] = $this->get_item_size('competencies');

            $this->competency_hierarchy = new hierarchy();

            $scale_ids = array_keys($this->scales);

            foreach ($scale_ids as $scale_id) {
                $this->competency_hierarchy->set_framework((new competency())->create_framework(['scale' => $scale_id]));
            }

            $this->competency_hierarchy->set_type(competency::class)
                ->set_variable_count(false)
                ->set_variable_depth(false)
                ->set_depth($depth)
                ->set_count($count)
                ->create_hierarchy();

            // Let's eager load scales
            collection::new(
                array_map(
                    function (item $item) {
                        return $item->get_data();
                    },
                    $this->competency_hierarchy->get_items()
                )
            )->load('scale');
        });

        return $this;
    }

    public function perform_comp_create_organisation_assignments() {

        return $this;
    }

    public function perform_comp_create_position_assignments() {

        return $this;
    }

    public function perform_comp_create_audience_assignments() {
        $this->output('Creating audience assignments...');

        if ($this->competency_hierarchy === null) {
            throw new Exception('You must create competency hierarchy first');
        }

        builder::get_db()->transaction(function () {
            $competencies = $this->get_competencies('competency_assignments');

            foreach ($this->audiences as $audience) {
                foreach ($competencies as $competency) {
                    static::competency_generator()
                        ->assignment_generator()
                        ->create_cohort_assignment($competency->get_data('id'), $audience->get_data()->id);
                }
            }
        });

        return $this;
    }

    public function perform_comp_create_user_assignments() {
        $this->output('Creating user assignments...');

        if ($this->competency_hierarchy === null) {
            throw new Exception('You must create competency hierarchy first');
        }

        builder::get_db()->transaction(function () {
            $competencies = $this->get_competencies('competency_assignments');

            foreach ($this->users as $user) {
                foreach ($competencies as $competency) {
                    static::competency_generator()
                        ->assignment_generator()
                        ->create_user_assignment($competency->get_data('id'), $user);
                }
            }
        });

        return $this;
    }

    public function create_courses() {
        $this->output('Creating courses...');

        $count = $this->get_item_size('courses');

        for ($c = 1; $c <= $count; $c++) {
            $this->courses[] = course::new()->save_and_return();
        }

        return $this;
    }

    public function add_linked_courses() {
        $this->output('Adding linked courses');

        if (empty($this->courses_with_users)) {
            throw new Exception('You must create courses and enrol users first');
        }

        builder::get_db()->transaction(function () {
            $count = $this->get_item_size('courses_per_criterion');
            $variable = $this->get_item_size('variable_courses_per_criterion');

            foreach ($this->competency_hierarchy->get_items() as $item) {
                $count = $variable ? rand(1, $count) : $count;

                $courses = array_map(
                    function ($id) {
                        return [
                            'id' => $this->courses_with_users[$id]->get_data('id'),
                            'mandatory' => false,
                        ];
                    },
                    array_rand($this->courses_with_users, $count)
                );

                linked_courses::set_linked_courses($item->get_data('id'), $courses);
            }
        });

        return $this;
    }

    public function enrol_users(bool $with_completions = true) {
        $this->output('Enrolling users...');

        if (empty($this->users) || empty($this->courses)) {
            throw new Exception('Users and courses must be created first!');
        }

        $percentage = $this->get_item_size('enrolments');

        $users = array_rand($this->users, $this->get_percentage(count($this->users), $percentage));
        $courses = array_rand($this->courses, $this->get_percentage(count($this->courses), $percentage));

        $this->users_with_courses = array_filter($this->users, function ($key) use ($users) {
            return in_array($key, $users);
        }, ARRAY_FILTER_USE_KEY);

        $this->courses_with_users = array_filter($this->courses, function ($key) use ($courses) {
            return in_array($key, $courses);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($courses as $course) {
            foreach ($users as $user) {
                $this->courses[$course]->enrol($this->users[$user]);
            }
        }

        return $this;
    }

    public function create_course_completions() {
        $this->output('Creating course completions...');

        // We'll be creating completions for courses with users
        $percentage = $this->get_item_size('completions');

        $count = $this->get_percentage(count($this->courses_with_users), $percentage);

        $courses_to_complete = array_rand($this->courses_with_users, $count);

        foreach ($courses_to_complete as $course_to_complete) {
            foreach ($this->courses_with_users[$course_to_complete]->get_enrolled_users() as $user) {
                $cc = new course_completion();
                $cc->for($this->courses_with_users[$course_to_complete])
                    ->by($user)
                    ->save_and_return();
            }
        }

        return $this;
    }

    public function create_course_completions_basic() {
        $this->output('Creating basic course completions...');

        builder::get_db()->transaction(function () {
            // We'll be creating completions for courses with users
            $percentage = $this->get_item_size('completions');

            $count = $this->get_percentage(count($this->courses), $percentage);

            $courses_to_complete = array_rand($this->courses, $count);
            $this->courses_with_users = $this->courses;

            $completions = [];
            $count = 0;
            foreach ($courses_to_complete as $course_to_complete) {
                // Create completions for all users
                foreach ($this->users as $user) {
                    $cc = new course_completion_basic();
                    $completions[] = $cc->for($this->courses[$course_to_complete])
                        ->by($user)
                        ->save_and_return();

                    $count++;
                    if ($count == BATCH_INSERT_MAX_ROW_COUNT) {
                        $this->bulk_insert_completions($completions);
                        $completions = [];
                        $count = 0;
                    }
                }
            }

            if (!empty($completions)) {
                $this->bulk_insert_completions($completions);
            }
            unset($completions);
            unset($courses_to_complete);
        });

        return $this;
    }

    private function bulk_insert_completions(array $completions) {
        builder::get_db()->insert_records(
            'course_completions',
            array_map(
                function (item $item) {
                    return (object)$item->get_data();
                },
                $completions
            )
        );
    }

    public function perform_comp_create_criteria_set(competency $competency) {
        // Create criteria group - on activate
        // Create criteria group - completion and child competencies
        // Create criteria group - completion and linked course
        // Manual rating pathway

        $scale = $this->scales[$competency->get_data()->scale->id];

        if (!$scale) {
            throw new Exception('Something went wrong scale value is not found');
        }

        $on_activate = criteria_group::new()
            ->for($competency)
            ->set_value($scale['incompetent'])
            ->add_criterion(on_activate::new()->for($competency)->save_and_return())
            ->save_and_return();

        $linked_course = criteria_group::new()
            ->for($competency)
            ->set_value($scale['competent'])
            ->add_criterion($this->create_course_completion_criterion())
            ->add_criterion($this->create_child_competency_criterion($competency))
            ->save_and_return();

        if (count($scale) === 4) {
            $another_one = criteria_group::new()
                ->for($competency)
                ->set_value($scale['less_incompetent'])
                ->add_criterion($this->create_course_completion_criterion())
                ->add_criterion($this->create_linked_courses_criterion($competency))
                ->save_and_return();
        }
    }

    public function create_linked_courses_criterion(competency $competency): linked_courses_criterion {
        return linked_courses_criterion::new()
            ->set_aggregation_method(2)
            ->set_required_items(1)
            ->for($competency)
            ->save_and_return();
    }

    public function perform_comp_create_criteria() {
        $this->output('Creating criteria...');

        if (!$this->competency_hierarchy) {
            throw new Exception('You must generate your competency hierarchy first');
        }

        builder::get_db()->transaction(function () {
            foreach ($this->competency_hierarchy->get_items() as $item) {
                $this->perform_comp_create_criteria_set($item);
            }
        });

        return $this;
    }

    protected function create_pathways() {
        return $this;
    }

    protected function get_competencies(string $key = null) {
        $competencies = $this->competency_hierarchy->get_items();
        $ids = array_rand($competencies, $this->get_percentage(count($competencies), $key ? $this->get_item_size($key) : 100));

        return array_filter(
            $competencies,
            function ($key) use ($ids) {
                return in_array($key, $ids);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    protected function create_child_competency_criterion(competency $competency): ?child_competency {
        // No criterion if there is no child competencies
        if ($competency->get_data()->children()->count() === 0) {
            return null;
        }

        return child_competency::new()
            ->for($competency)
            ->set_aggregation_method(2)
            ->set_required_items(2)
            ->save_and_return();
    }

    protected function create_course_completion_criterion(): course_completion_criterion {
        $criterion = course_completion_criterion::new();

        $count = $this->get_item_size('courses_per_criterion');
        $variable = $this->get_item_size('variable_courses_per_criterion');

        $count = $variable ? rand(1, $count) : $count;

        // Now let's pick courses
        $courses = array_rand($this->courses_with_users, $count);

        foreach ($courses as $course) {
            $criterion->add_course($this->courses_with_users[$course]);
        }

        return $criterion->save_and_return();
    }

    protected function create_job_assignments_for_user() {
        $size = $this->get_item_size('job_assignments_for_user');
        if (!$size) {
            return $this;
        }
        $this->output('Creating job assignments...');

        if (empty($this->users)) {
            throw new coding_exception("Cannot create workspace member when users are empty");
        }

        $job_assignment_creator = new job_assignments_creator($this->users, $this->positions, $this->organisations);
        $job_assignment_creator->generate($size);

        return $this;
    }

    protected function perform_act_create_activities() {
        $this->output('Creating activities ...');
        $activity_size = $this->get_item_size('activities');
        $configuration_manager = new preset_configurations();

        builder::get_db()->transaction(
            function () use ($activity_size, $configuration_manager) {
                $done = 0;
                for ($x = 0; $x <= $activity_size['count']; $x++) {
                    $config = $configuration_manager->get_a_configuration();
                    $config['track']['audiences'] = $this->get_random_audiences($activity_size['audiences_per_activity']);
                    (activity_item::new())->create_activity($config);

                    self::show_progress($done, $activity_size['count']);
                }
            }
        );
        $this->output('All activities created');

        return $this;
    }

    private function perform_act_expand_track_user_assignments() {
        $this->output('Expanding user assignments...');
        (new expand_task())->expand_all();

        $this->user_assignments_expanded = true;
        return $this;
    }

    private function perform_act_generate_instances() {
        if (!$this->user_assignments_expanded) {
            $this->perform_act_expand_track_user_assignments();
        }

        $this->output('Generating subject & participant instance data for activities...');
        (new subject_instance_creation())->generate_instances();

        return $this;
    }

    private function get_random_audiences($count) {
        if (empty($this->audiences)) {
            throw new coding_exception("You'll need to create audiences & audience users first.");
        }
        $audience_keys = array_rand($this->audiences, $count);
        $audience_ids = [];

        if ($audience_keys === null) {
            throw new coding_exception('Trying to pick more audiences than created');
        }

        // Cater for the case when there's only one
        if (!is_array($audience_keys)) {
            $audience_keys = [$audience_keys];
        }

        foreach ($audience_keys as $audience_key) {
            $audience_ids[] = $this->audiences[$audience_key]->get_data()->id;
        }

        return $audience_ids;
    }

    protected function perform_act_create_random_responses() {
        $this->output('Creating random responses for activities...');
        // todo: create_random_responses.
    }


    public function engage_create_workspaces() {
        $this->output('Creating workspaces ...');
        $size = $this->get_item_size('workspaces');

        if (!is_numeric($size)) {
            return $this;
        }

        builder::get_db()->transaction(
            function () use ($size) {
                $workspaces = [];
                for ($x = 0; $x <= $size; $x++) {
                    $workspaces[] = (workspace::new())->create_workspace();
                }

                $this->workspaces = $workspaces;
            }
        );

        return $this;
    }

    public function engage_create_workspace_members() {
        $this->output('Creating workspace members ...');
        $size = $this->get_item_size('workspace_members');

        if (empty($this->users)) {
            throw new coding_exception("Cannot create workspace member when users are empty");
        }

        builder::get_db()->transaction(
            function () use ($size) {
                $generator = App::generator();

                /** @var \container_workspace_generator $workspace_generator */
                $workspace_generator = $generator->get_plugin_generator('container_workspace');

                foreach ($this->workspaces as $workspace) {
                    $i = 0;
                    foreach ($this->users as $user) {
                        if (isguestuser($user)) {
                            continue;
                        }

                        $workspace_generator->create_self_join_member($workspace, $user);
                        $i++;

                        if ($i == $size) {
                            break;
                        }
                    }
                }
            }
        );

        return $this;
    }

    public function engage_create_workspace_discussions() {
        $this->output('Creating workspace discussions ...');
        $size = $this->get_item_size('workspace_discussions');

        builder::get_db()->transaction(
            function () use ($size) {
                foreach ($this->workspaces as $workspace) {
                    $workspace_id = $workspace->get_id();
                    $query = new member_query($workspace_id);
                    $paginator = member_loader::get_members($query);

                    while (true) {
                        $members = $paginator->get_items()->all();
                        foreach ($members as $member) {
                            $user_id = $member->get_user_id();
                            for ($c = 0; $c <= $size; $c++) {
                                $this->engage_create_workspace_discussion($workspace->get_id(), $user_id);
                            }
                        }

                        $next_cursor = $paginator->get_next_cursor();
                        if (null === $next_cursor) {
                            break;
                        }

                        $query->set_cursor($next_cursor);
                        $paginator = member_loader::get_members($query);
                    }
                }
            }
        );

        return $this;
    }

    public function engage_create_workspace_discussion($workspace_id, $author_id) {
        $generator = App::generator();
        $faker = App::faker();

        /** @var \container_workspace_generator $workspace_generator */
        $workspace_generator = $generator->get_plugin_generator('container_workspace');
        $workspace_generator->create_discussion(
            $workspace_id,
            $faker->text,
            null,
            FORMAT_JSON_EDITOR,
            $author_id
        );

        return $this;
    }

    public function engage_create_workspace_discussion_comments_replies() {
        $this->output('Creating workspace discussion comments and replies...');
        $comment_size = $this->get_item_size('workspace_discussion_comments');
        $reply_size = $this->get_item_size('workspace_discussion_replies');

        builder::get_db()->transaction(
            function () use ($reply_size, $comment_size) {
                $generator = App::generator();
                $faker = App::faker();

                /** @var \totara_comment_generator $comment_generator */
                $comment_generator = $generator->get_plugin_generator('totara_comment');
                foreach ($this->workspaces as $workspace) {
                    $workspace_id = $workspace->get_id();

                    $discussion_query = new discussion_query($workspace_id);
                    $discussion_paginator = discussion_loader::get_discussions($discussion_query);

                    while (true) {
                        $discussions = $discussion_paginator->get_items()->all();

                        foreach ($discussions as $discussion) {
                            $member_query = new member_query($workspace_id);
                            $member_paginator = member_loader::get_members($member_query);

                            while (true) {
                                $members = $member_paginator->get_items()->all();
                                foreach ($members as $member) {
                                    for ($i = 0; $i < $comment_size; $i++) {
                                        $comment = $comment_generator->create_comment(
                                            $discussion->get_id(),
                                            'container_workspace',
                                            $discussion::AREA,
                                            $faker->text,
                                            FORMAT_JSON_EDITOR,
                                            $member->get_user_id()
                                        );

                                        foreach ($members as $inner_member) {
                                            for ($x = 0; $x < $reply_size; $x++) {
                                                $comment_generator->create_reply(
                                                    $comment->get_id(),
                                                    $faker->text,
                                                    FORMAT_JSON_EDITOR,
                                                    $inner_member->get_user_id()
                                                );
                                            }
                                        }

                                    }
                                }

                                $member_next_cursor = $member_paginator->get_next_cursor();
                                if (null === $member_next_cursor) {
                                    break;
                                }

                                $member_query->set_cursor($member_next_cursor);
                                $member_paginator = member_loader::get_members($member_query);
                            }
                        }

                        $discussion_next_cursor = $discussion_paginator->get_next_cursor();
                        if (null === $discussion_next_cursor) {
                            break;
                        }

                        $discussion_query->set_cursor($discussion_next_cursor);
                        $discussion_paginator = discussion_loader::get_discussions($discussion_query);
                    }
                }
            }
        );

        return $this;
    }

    /**
     * Show dot and overall progress
     *
     * @param int $done
     * @param int $total
     */
    public static function show_progress(int &$done, int $total) {
        $done++;
        echo '.';

        if ($done % 50 === 0) {
            $percentage_done = round($done * 100 / $total, 0);
            echo "$percentage_done%" . PHP_EOL;
        }
    }

}