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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\section;
use mod_perform\models\activity\section_element;
use mod_perform\models\activity\track;
use mod_perform\state\activity\draft;

/**
 * @group perform
 */
class mod_perform_activity_clone_model_helper_testcase extends advanced_testcase {

    /**
     * Test activity clone
     */
    public function test_clone(): void {

        $this->setAdminUser();

        $data_generator = self::getDataGenerator();
        /** @var mod_perform_generator $perform_generator */
        $perform_generator = $data_generator->get_plugin_generator('mod_perform');

        /** @var activity $activity */
        $activity = $perform_generator->create_full_activities()->first();
        /** @var section $section */
        $section = $activity->sections->first();

        $element = $perform_generator->create_element();
        $section_element = $perform_generator->create_section_element($section, $element);

        $entity = activity_entity::repository()->find($activity->get_id());
        $new_activity = activity::load_by_entity($entity)->clone(true);

        $this->assertEquals($activity->name . get_string('activity_name_restore_suffix', 'mod_perform'), $new_activity->name);
        $this->assertEquals($activity->type, $new_activity->type);
        $this->assertEquals(draft::get_code(), $new_activity->status);
        $this->assertEquals($activity->close_on_completion, $new_activity->close_on_completion);
        $this->assertEquals($activity->description, $new_activity->description);
        $this->assertGreaterThanOrEqual($activity->created_at, $new_activity->created_at);
        $this->assertGreaterThanOrEqual($activity->updated_at, $new_activity->updated_at);

        $old_sections = $activity->get_sections();
        $new_sections = $new_activity->get_sections();

        $this->assertEquals(count($old_sections), count($new_sections));

        $old_sections = $old_sections->all(true);
        /** @var section $old_section */
        foreach ($old_sections as $key => $old_section) {
            if (!$new_section = $new_sections->find('title', $old_section->title)) {
                $this->fail('Section was not cloned');
            }
            $this->assertEquals($old_section->title, $new_section->title);
            $this->assertEquals($new_activity->id, $new_section->activity_id);
            $this->assertGreaterThanOrEqual($old_section->created_at, $new_section->created_at);
            $this->assertGreaterThanOrEqual($old_section->updated_at, $new_section->updated_at);

            $old_section_relationships = $old_section->get_section_relationships();
            $new_section_relationships = $new_section->get_section_relationships();
            $this->assertEquals(count($old_section_relationships), count($new_section_relationships));

            $old_section_elements = $old_section->get_section_elements();
            $new_section_elements = \core\collection::new($new_section->get_section_elements());
            $this->assertEquals(count($old_section_elements), count($new_section_elements));

            /** @var section_element $old_section_element */
            foreach ($old_section_elements as $section_element_key => $old_section_element) {
                if (!$new_section_element = $new_section_elements->find('section_id', $new_section->id)) {
                    $this->fail('Section element was not cloned');
                }
                $this->assertEquals($old_section_element->sort_order, $new_section_element->sort_order);
                unset($old_section_elements[$section_element_key]);
            }
            $this->assertEmpty($old_section_elements);

            unset($old_sections[$key]);
        }
        $this->assertEmpty($old_sections);

        $old_relationships = $activity->get_relationships();
        $new_relationships = $new_activity->get_relationships();
        $this->assertEquals(count($old_relationships), count($new_relationships));

        $old_tracks = $activity->get_tracks();
        $new_tracks = $new_activity->get_tracks();
        $this->assertEquals(count($old_tracks), count($new_tracks));

        /** @var track $new_track */
        if (!$new_track = $new_tracks->find('activity_id', $new_activity->get_id())) {
            $this->fail('Track was not cloned');
        }
        /** @var track $old_track */
        if (!$old_track = $old_tracks->find('activity_id', $activity->get_id())) {
            $this->fail('Old track was not found');
        }

        $old_track_assignments = $old_track->get_assignments();
        $new_track_assignments = $new_track->get_assignments();
        $this->assertEquals(count($old_track_assignments), count($new_track_assignments));
    }
}