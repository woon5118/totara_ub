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

use core\orm\collection;
use degeneration\items\audience;
use degeneration\items\competency;
use degeneration\items\competency_scale;
use degeneration\items\course;
use degeneration\items\course_completion;
use degeneration\items\criteria\child_competency;
use degeneration\items\criteria\on_activate;
use degeneration\items\criteria\course_completion as course_completion_criterion;
use degeneration\items\criteria\linked_courses as linked_courses_criterion;
use degeneration\items\item;
use degeneration\items\organisation;
use degeneration\items\pathways\criteria_group;
use degeneration\items\position;
use degeneration\items\user;
use hierarchy_organisation\entities\organisation_framework;
use hierarchy_position\entities\position_framework;
use totara_competency\entities\competency_framework;
use totara_competency\linked_courses;

class performance_testing extends App {

    /**
     * @var user[]
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
     *
     */
    public function generate() {
        $this->create_users()
        //->create_organisations()
        //->create_positions()
        //->create_audiences()
            ->create_scales()
            ->create_competencies()
        //->create_organisation_assignments()
        //->create_position_assignments()
        //->create_audience_assignments()
            ->create_user_assignments()
            ->create_courses()
            ->enrol_users()
            ->create_course_completions()
            ->add_linked_courses()
            ->create_criteria()
            ;
        //->create_job_assignments();
    }

    public function create_users() {
        $this->output('Creating users...');

        $size = $this->get_item_size('users');

        for ($c = 1; $c <= $size; $c++) {
            $this->users[] = user::new()->save_and_return();
        }

        return $this;
    }

    public function create_organisations() {
        $this->output('Creating organisations...');

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

        return $this;
    }

    public function create_positions() {
        $this->output('Creating positions...');

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

        return $this;
    }

    public function create_audiences() {
        $this->output('Creating audiences...');

        $size = $this->get_item_size('audiences');

        for ($c = 1; $c <= $size; $c++) {
            $this->users[] = audience::new()->save_and_return();
        }

        return $this;
    }

    public function create_scales() {
        $this->output('Creating competency scales...');

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

        return $this;
    }

    public function create_competencies() {
        $this->output('Creating competencies...');

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
        collection::new(array_map(function (item $item) {
            return $item->get_data();
        }, $this->competency_hierarchy->get_items()))->load('scale');

        return $this;
    }

    public function create_organisation_assignments() {

        return $this;
    }

    public function create_position_assignments() {

        return $this;
    }

    public function create_audience_assignments() {

        return $this;
    }

    public function create_user_assignments() {
        $this->output('Creating user assignments...');

        if ($this->competency_hierarchy === null) {
            throw new \Exception('You must create competency hierarchy first');
        }

        $competencies = $this->competency_hierarchy->get_items();

        $percentage = $this->get_item_size('assignments');

        $competency_ids = array_rand($competencies, $this->get_percentage(count($competencies), $percentage));

        foreach ($this->users as $user) {
            foreach ($competency_ids as $competency_id) {
                static::competency_generator()->assignment_generator()->create_user_assignment($competencies[$competency_id]->get_data('id'), $user->get_data()->id);
            }
        }

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
            throw new \Exception('You must create courses and enrol users first');
        }

        $count = $this->get_item_size('courses_per_criterion');
        $variable = $this->get_item_size('variable_courses_per_criterion');

        foreach ($this->competency_hierarchy->get_items() as $item) {

            $count = $variable ? rand(1, $count) : $count;

            $courses = array_map(function($id) {
                return [
                    'id' => $this->courses_with_users[$id]->get_data('id'),
                    'mandatory' => false,
                ];
            }, array_rand($this->courses_with_users, $count)
            );

            linked_courses::set_linked_courses($item->get_data('id'), $courses);
        }

        return $this;
    }

    public function enrol_users(bool $with_completions = true) {
        $this->output('Enrolling users...');

        if (empty($this->users) || empty($this->courses)) {
            throw new \Exception('Users and courses must be created first!');
        }

        $percentage = $this->get_item_size('enrolments');

        $users = array_rand($this->users, $this->get_percentage(count($this->users), $percentage));
        $courses = array_rand($this->courses, $this->get_percentage(count($this->courses), $percentage));

        $this->users_with_courses = array_filter($this->users, function($key) use ($users) {
            return in_array($key, $users);
        }, ARRAY_FILTER_USE_KEY);

        $this->courses_with_users = array_filter($this->courses, function($key) use ($courses) {
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
            foreach ($this->courses[$course_to_complete]->get_enrolled_users() as $user) {
                $cc = new course_completion();
                $cc->for($this->courses[$course_to_complete])
                    ->by($user)
                    ->save_and_return();
            }
        }

        return $this;
    }

    public function create_criteria_set(competency $competency) {
        // Create criteria group - on activate
        // Create criteria group - completion and child competencies
        // Create criteria group - completion and linked course
        // Manual rating pathway

        $scale = $this->scales[$competency->get_data()->scale->id];

        if (!$scale) {
            throw new \Exception('Something went wrong scale value is not found');
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

    public function create_criteria() {
        $this->output('Creating criteria...');

        if (!$this->competency_hierarchy) {
            throw new \Exception('You must generate your competency hierarchy first');
        }

        foreach ($this->competency_hierarchy->get_items() as $item) {
            $this->create_criteria_set($item);
        }

        return $this;
    }

    protected function create_pathways() {
        return $this;
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

    public function create_job_assignments() {

        return $this;
    }

    //['xs', 's', 'm', 'l', 'xl', 'xxl', 'goliath'
    public function get_xs_size() {
        return [
            'users' => 10,
            'courses' => 10,
            'audiences' => 10,
            'organisations' => [3, 3],
            'positions' => [3, 3],
            'competencies' => [3, 3],
            'assignments' => 100,
            'job_assignments' => 100,
            'enrolments' => 100,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 100, // <-- Note this is a percentage
        ];
    }

    public function get_s_size() {
        return [
            'users' => 100,
            'courses' => 100,
            'audiences' => 1000,
            'organisations' => [4, 4],
            'positions' => [4, 4],
            'competencies' => [4, 4],
            'assignments' => 100,
            'job_assignments' => 50,
            'enrolments' => 100,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 75, // <-- Note this is a percentage
        ];
    }

    public function get_m_size() {
        return [
            'users' => 1000,
            'courses' => 100,
            'audiences' => 1000,
            'organisations' => [3, 4],
            'positions' => [3, 4],
            'competencies' => [3, 4],
            'assignments' => 80,
            'job_assignments' => 50,
            'enrolments' => 80,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 75, // <-- Note this is a percentage
        ];
    }

    public function get_l_size() {
        return [
            'users' => 1000,
            'courses' => 100,
            'audiences' => 1000,
            'organisations' => [4, 4],
            'positions' => [4, 4],
            'competencies' => [4, 4],
            'assignments' => 100,
            'job_assignments' => 50,
            'enrolments' => 100,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 75, // <-- Note this is a percentage
        ];
    }

    public function get_xl_size() {
        return [
            'users' => 1000,
            'courses' => 1000,
            'audiences' => 100,
            'organisations' => [5, 5],
            'positions' => [5, 5],
            'competencies' => [5, 5],
            'assignments' => 70,
            'job_assignments' => 100,
            'enrolments' => 70,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 80, // <-- Note this is a percentage
        ];
    }

    public function get_xxl_size() {
        return [
            'users' => 10000,
            'courses' => 1000,
            'audiences' => 100,
            'organisations' => [5, 5],
            'positions' => [5, 5],
            'competencies' => [5, 5],
            'assignments' => 70,
            'job_assignments' => 100,
            'enrolments' => 70,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 80, // <-- Note this is a percentage
        ];
    }

    public function get_goliath_size() {
        return [
            'users' => 100000,
            'courses' => 1000,
            'audiences' => 100,
            'organisations' => [5, 5],
            'positions' => [5, 5],
            'competencies' => [5, 5],
            'assignments' => 70,
            'job_assignments' => 100,
            'enrolments' => 70,
            'courses_per_criterion' => 5,
            'variable_courses_per_criterion' => false,
            'completions' => 80, // <-- Note this is a percentage
        ];
    }
}