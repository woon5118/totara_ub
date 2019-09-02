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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

use totara_competency\entities\competency;
use criteria_linkedcourses\metadata_processor;
use pathway_criteria_group\criteria_group;
use criteria_linkedcourses\linkedcourses;
use totara_competency\linked_courses;

class criteria_linkedcourses_metadata_processor_testcase extends advanced_testcase {

    private function set_up_pathway_with_linked_courses_criteria($competency, $linktype = linkedcourses::LINKTYPE_ALL) {
        $linked_course_criterion = new linkedcourses();
        $linktype = ['metakey' => linkedcourses::METADATA_LINKTYPE_KEY, 'metavalue' => $linktype];
        $linked_course_criterion->set_metadata([$linktype]);

        $pathway = new criteria_group();
        $pathway->set_competency($competency);
        $pathway->set_status(criteria_group::PATHWAY_STATUS_ACTIVE);
        $pathway->set_scale_value($competency->scale->scale_values->first());
        $pathway->add_criterion($linked_course_criterion);
        $pathway->save();
    }

    public function test_update_item_links_no_data() {
        global $DB;

        $this->resetAfterTest();

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);

        metadata_processor::update_item_links($comp->id);

        // Ensure no history or configuration change entries were logged
        $this->assertSame(0, $DB->count_records('totara_competency_configuration_change', ['comp_id' => $comp->id]));
        $this->assertSame(0, $DB->count_records('totara_competency_configuration_history', ['comp_id' => $comp->id]));
    }

    public function test_update_item_links_criteria_with_no_courses() {
        global $DB;

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new competency($comp->id);

        $linked_course_criterion = new linkedcourses();

        $pathway = new criteria_group();
        $pathway->set_competency($competency);
        $pathway->set_status(criteria_group::PATHWAY_STATUS_ACTIVE);
        $pathway->set_scale_value($competency->scale->scale_values->first());
        $pathway->add_criterion($linked_course_criterion);
        $pathway->save();

        metadata_processor::update_item_links($competency->id);

        $this->assertCount(0, $DB->get_records('totara_criteria_item'));
        $this->assertSame(0, $DB->count_records('totara_competency_configuration_change', ['comp_id' => $comp->id]));
        $this->assertSame(0, $DB->count_records('totara_competency_configuration_history', ['comp_id' => $comp->id]));
    }

    public function test_update_item_links_criteria_with_one_course() {
        global $DB;

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new competency($comp->id);

        $this->set_up_pathway_with_linked_courses_criteria($competency);

        $course = $this->getDataGenerator()->create_course();
        linked_courses::set_linked_courses($competency->id, [['id' => $course->id, 'linktype' => PLAN_LINKTYPE_MANDATORY]]);

        metadata_processor::update_item_links($competency->id);

        $criterion_record = $DB->get_record('totara_criteria', ['plugin_type' => 'linkedcourses']);

        $items = $DB->get_records('totara_criteria_item');
        $this->assertCount(1, $items);
        $item = array_pop($items);
        $this->assertEquals('course', $item->item_type);
        $this->assertEquals($course->id, $item->item_id);
        $this->assertEquals($criterion_record->id, $item->criterion_id);

        $this->assertSame(1, $DB->count_records('totara_competency_configuration_change', ['comp_id' => $comp->id]));
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_history', ['comp_id' => $comp->id]));
    }

    public function test_update_item_links_criteria_with_changes() {
        global $DB;

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new competency($comp->id);

        $this->set_up_pathway_with_linked_courses_criteria($competency);

        $add = $this->getDataGenerator()->create_course();
        $keep = $this->getDataGenerator()->create_course();
        $remove = $this->getDataGenerator()->create_course();
        linked_courses::set_linked_courses(
            $competency->id,
            [['id' => $keep->id, 'linktype' => PLAN_LINKTYPE_MANDATORY], ['id' => $remove->id, 'linktype' => PLAN_LINKTYPE_MANDATORY]]
        );

        metadata_processor::update_item_links($competency->id);
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_change', ['comp_id' => $comp->id]));
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_history', ['comp_id' => $comp->id]));

        $items = $DB->get_records('totara_criteria_item');
        $this->assertCount(2, $items);
        $keep_item_id = null;
        $remove_item_id = null;
        $add_item_id = null;
        foreach($items as $item) {
            if ($item->item_id == $keep->id) {
                $keep_item_id = $item->id;
            } else if ($item->item_id == $remove->id) {
                $remove_item_id = $item->id;
            } else {
                $this->fail('Extra item added');
            }
        }
        $this->assertNotNull($keep_item_id);
        $this->assertNotNull($remove_item_id);

        // At this point, we keep the $keep course, add $add. $remove gets removed.
        linked_courses::set_linked_courses(
            $competency->id,
            [['id' => $keep->id, 'linktype' => PLAN_LINKTYPE_MANDATORY], ['id' => $add->id, 'linktype' => PLAN_LINKTYPE_MANDATORY]]
        );

        // Wait a sec to ensure timestamps are different
        $this->waitForSecond();
        metadata_processor::update_item_links($competency->id);
        $this->assertSame(2, $DB->count_records('totara_competency_configuration_change', ['comp_id' => $comp->id]));
        $this->assertSame(2, $DB->count_records('totara_competency_configuration_history', ['comp_id' => $comp->id]));

        $items = $DB->get_records('totara_criteria_item');
        $this->assertCount(2, $items);

        foreach($items as $item) {
            if ($item->item_id == $add->id) {
                $add_item_id = $item->id;
            }

            // Ensure that the $keep_item's row wasn't replaced
            if ($item->item_id == $keep->id) {
                $this->assertEquals($item->id, $keep_item_id);
            }
        }

        // Make sure the $add item was added
        $this->assertNotNull($add_item_id);

        // Final step. Remove all courses from linked.
        // The idea of this test is to check it does remove when there are zero courses linked rather
        // than ignore thinking there's nothing to do.

        linked_courses::set_linked_courses(
            $competency->id,
            []
        );

        // Wait a sec to ensure timestamps are different
        $this->waitForSecond();
        metadata_processor::update_item_links($competency->id);
        $this->assertSame(3, $DB->count_records('totara_competency_configuration_change', ['comp_id' => $comp->id]));
        $this->assertSame(3, $DB->count_records('totara_competency_configuration_history', ['comp_id' => $comp->id]));

        $this->assertEquals(0, $DB->count_records('totara_criteria_item'));
    }

    public function test_update_item_links_with_mandatory_only() {
        global $DB;

        /** @var totara_hierarchy_generator $hierarchy_generator */
        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);
        $competency = new competency($comp->id);

        $this->set_up_pathway_with_linked_courses_criteria($competency, linkedcourses::LINKTYPE_MANDATORY);

        $mandatory = $this->getDataGenerator()->create_course();
        $optional = $this->getDataGenerator()->create_course();
        linked_courses::set_linked_courses(
            $competency->id,
            [['id' => $mandatory->id, 'linktype' => PLAN_LINKTYPE_MANDATORY], ['id' => $optional->id, 'linktype' => PLAN_LINKTYPE_OPTIONAL]]
        );

        metadata_processor::update_item_links($competency->id);
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_change', ['comp_id' => $comp->id]));
        $this->assertSame(1, $DB->count_records('totara_competency_configuration_history', ['comp_id' => $comp->id]));

        $items = $DB->get_records('totara_criteria_item');
        $this->assertCount(1, $items);

        $this->assertCount(1, $DB->get_records('totara_criteria_item', ['item_id' => $mandatory->id]));
    }
}
