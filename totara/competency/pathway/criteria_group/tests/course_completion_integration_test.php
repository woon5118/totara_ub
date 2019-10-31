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
 * @package pathway_criteria_group
 */

use pathway_criteria_group\criteria_group;
use totara_competency\pathway;
use criteria_coursecompletion\coursecompletion;
use totara_competency\achievement_configuration;
use totara_criteria\criterion;

class pathway_criteria_group_course_completion_integration_testcase extends advanced_testcase {

    public function test_course_completion_leads_to_comp_achievement_via_cron() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        /** @var tassign_competency_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('tassign_competency');

        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();

        // Create course completion before setting up the competency and assignment as we want its event
        // to be ignored while we test cron operations.
        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $course1->id, 'userid' => $user1->id]);
        $completion->mark_complete();

        $competency1 = $competency_generator->create_competency();

        $pathway = new criteria_group();
        $pathway->set_competency($competency1);
        /** @var \totara_competency\entities\scale_value $expected_value1 */
        $expected_value1 = $competency1->scale->values()
            ->order_by('sortorder', 'asc')
            ->first();

        $pathway->set_scale_value($expected_value1);

        $course_completion_criteria = new coursecompletion();
        $course_completion_criteria->set_aggregation_method(coursecompletion::AGGREGATE_ALL);
        $course_completion_criteria->add_items([$course1->id]);

        $pathway->add_criterion($course_completion_criteria);

        $pathway->save();

        $configuration = new achievement_configuration($competency1);
        $configuration->set_aggregation_type('highest');
        $configuration->save_aggregation();

        $assignment1 = $assignment_generator->create_user_assignment($competency1->id, $user1->id);
        $model = new \tassign_competency\models\assignment_actions();
        $model->activate([$assignment1->id]);
        $expand_task = new \tassign_competency\expand_task($DB);
        $expand_task->expand_all();

        $this->assertEquals(1, $DB->count_records('totara_assignment_competency_users'));

        // At this point we have the competency with it's configured criteria and aggregation.
        // We also have a user with an active and expanded assignment to that competency.
        // The course was also complete, but that fact has not yet entered the criteria system.
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));
        $this->assertEquals(0, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_criteria_item_record'));

        $this->assertEquals(false, $DB->get_field('totara_criteria_item_record', 'criterion_met', []));
        $this->assertEquals(false, $DB->get_field('totara_competency_pathway_achievement', 'scale_value_id', []));

        (new pathway_criteria_group\task\aggregate())->execute();

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));
        $this->assertEquals(0, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(1, $DB->count_records('totara_criteria_item_record'));

        $this->assertEquals(0, $DB->get_field('totara_criteria_item_record', 'criterion_met', []));
        $this->assertEquals(false, $DB->get_field('totara_competency_pathway_achievement', 'scale_value_id', []));

        (new totara_criteria\task\evaluate_items())->execute();

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));
        $this->assertEquals(0, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(1, $DB->count_records('totara_criteria_item_record'));

        $this->assertEquals(1, $DB->get_field('totara_criteria_item_record', 'criterion_met', []));
        $this->assertEquals(false, $DB->get_field('totara_competency_pathway_achievement', 'scale_value_id', []));

        (new pathway_criteria_group\task\aggregate())->execute();

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));
        $this->assertEquals(1, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(1, $DB->count_records('totara_criteria_item_record'));

        $this->assertEquals(1, $DB->get_field('totara_criteria_item_record', 'criterion_met', []));
        $this->assertEquals($expected_value1->id, $DB->get_field('totara_competency_pathway_achievement', 'scale_value_id', []));

        (new totara_competency\task\competency_achievement_aggregation())->execute();

        $this->assertEquals(1, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(1, $DB->count_records('totara_competency_achievement_via'));
        $this->assertEquals(1, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(1, $DB->count_records('totara_criteria_item_record'));

        $this->assertEquals(1, $DB->get_field('totara_criteria_item_record', 'criterion_met', []));
        $this->assertEquals($expected_value1->id, $DB->get_field('totara_competency_pathway_achievement', 'scale_value_id', []));

        $comp_record = $DB->get_record('totara_competency_achievement', []);
        $this->assertEquals($expected_value1->id, $comp_record->scale_value_id);
    }

    public function test_course_completion_leads_to_comp_achievement_via_events() {
        global $DB;

        /** @var totara_competency_generator $competency_generator */
        $competency_generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        /** @var tassign_competency_generator $assignment_generator */
        $assignment_generator = $this->getDataGenerator()->get_plugin_generator('tassign_competency');

        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();

        $competency1 = $competency_generator->create_competency();

        $pathway = new criteria_group();
        $pathway->set_competency($competency1);
        /** @var \totara_competency\entities\scale_value $expected_value1 */
        $expected_value1 = $competency1->scale->values()
            ->order_by('sortorder', 'asc')
            ->first();

        $pathway->set_scale_value($expected_value1);

        $course_completion_criteria = new coursecompletion();
        $course_completion_criteria->set_aggregation_method(coursecompletion::AGGREGATE_ALL);
        $course_completion_criteria->add_items([$course1->id]);

        $pathway->add_criterion($course_completion_criteria);

        $pathway->save();

        $configuration = new achievement_configuration($competency1);
        $configuration->set_aggregation_type('highest');
        $configuration->save_aggregation();

        $assignment1 = $assignment_generator->create_user_assignment($competency1->id, $user1->id);
        $model = new \tassign_competency\models\assignment_actions();
        $model->activate([$assignment1->id]);
        $expand_task = new \tassign_competency\expand_task($DB);
        $expand_task->expand_all();

        $this->assertEquals(1, $DB->count_records('totara_assignment_competency_users'));

        // At this point we have the competency with it's configured criteria and aggregation.
        // We also have a user with an active and expanded assignment to that competency.
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));
        $this->assertEquals(0, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_criteria_item_record'));

        $this->assertEquals(false, $DB->get_field('totara_criteria_item_record', 'criterion_met', []));
        $this->assertEquals(false, $DB->get_field('totara_competency_pathway_achievement', 'scale_value_id', []));

        // We just need to run this task to add the item record for the user.
        // Doing this prior to any events.
        (new \pathway_criteria_group\task\aggregate())->execute();

        $this->assertEquals(0, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(0, $DB->count_records('totara_competency_achievement_via'));
        $this->assertEquals(0, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(1, $DB->count_records('totara_criteria_item_record'));

        $this->assertEquals(0, $DB->get_field('totara_criteria_item_record', 'criterion_met', []));
        $this->assertEquals(false, $DB->get_field('totara_competency_pathway_achievement', 'scale_value_id', []));

        // Complete the course and the rest should be done via a series of events following that.
        /** @var completion_completion $completion */
        $completion = new completion_completion(['course' => $course1->id, 'userid' => $user1->id]);

        $completion->mark_complete();

        // Ordered these according to when they happen.
        $this->assertEquals(1, $DB->count_records('totara_criteria_item_record'));
        $this->assertEquals(1, $DB->count_records('totara_competency_pathway_achievement'));
        $this->assertEquals(1, $DB->count_records('totara_competency_achievement'));
        $this->assertEquals(1, $DB->count_records('totara_competency_achievement_via'));

        $this->assertEquals(1, $DB->get_field('totara_criteria_item_record', 'criterion_met', []));
        $this->assertEquals($expected_value1->id, $DB->get_field('totara_competency_pathway_achievement', 'scale_value_id', []));

        $comp_record = $DB->get_record('totara_competency_achievement', []);
        $this->assertEquals($expected_value1->id, $comp_record->scale_value_id);
    }
}
