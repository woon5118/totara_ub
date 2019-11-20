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

namespace degeneration\items;

use core\orm\query\builder;
use degeneration\App;
use totara_competency\entities\course as course_entity;

class course_completion extends item {

    /**
     * @var course
     */
    protected $for = null;

    /**
     * User to complete a course
     *
     * @var user
     */
    protected $by = null;

    /**
     * Create for course
     *
     * @param course $course
     * @return $this
     */
    public function for(course $course) {
        $this->for = $course;

        return $this;
    }

    /**
     * Created by user
     *
     * @param user $user
     * @return $this
     */
    public function by(user $user) {
        $this->by = $user;

        return $this;
    }

    /**
     * Save a user
     *
     * @return bool
     */
    public function save(): bool {

        if (!$this->by) {
            throw new \Exception('You must set user to create completion record');
        }

        if (!$this->for) {
            throw new \Exception('You must set user to create completion record');
        }

        $completion = new \completion_completion([
            'course' => $this->for->get_data('id'),
            'userid' => $this->by->get_data()->id,
        ]);

        $completion->mark_complete(time());

        $this->data = $completion;

        // $c1 = $this->generator()->create_course(['shortname' => 'origin', 'fullname' => 'Original Course', 'summary' => 'DESC', 'summaryformat' => FORMAT_MOODLE]);
        // $completion_generator->enable_completion_tracking($c1);
        // // Create an activity with completion and set it as a course criteria.
        // $completiondefaults = array(
        //     'completion' => COMPLETION_TRACKING_AUTOMATIC,
        //     'completionview' => COMPLETION_VIEW_REQUIRED
        // );
        // $act1 = $generator->create_module('certificate', array('course' => $c1->id), $completiondefaults);

        //$properties = $this->evaluate_properties();

        //$course = (array) App::generator()->create_course($properties);

        return true;
    }

    /**
     * Get course completion generator
     *
     * @return \core_completion_generator
     */
    public function generator() {
        return App::generator()->get_plugin_generator('core_completion');

    }



    /**
     * Get list of properties to be added to the generated item
     *
     * @return array
     */
    public function get_properties(): array {
        return [];
    }
}