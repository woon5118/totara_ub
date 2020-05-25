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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\backup;

defined('MOODLE_INTERNAL') || die();

use backup;
use backup_nested_element;

global $CFG;
require_once($CFG->dirroot . '/backup/moodle2/backup_stepslib.php');

class backup_activity_structure_step extends \backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $perform = new backup_nested_element(
            'perform',
            ['id'],
            [
                'name',
                'status',
                'description',
                'created_at',
                'updated_at',
                'type_id',
                'close_on_completion',
            ]
        );

        $sections = new backup_nested_element('sections');
        $section = new backup_nested_element(
            'section',
            ['id'],
            [
                'activity_id',
                'title',
                'created_at',
                'updated_at',
            ]
        );

        $elements = new backup_nested_element('elements');
        $element = new backup_nested_element(
            'element',
            ['id'],
            [
                'plugin_name',
                'title',
                'identifier',
                'data',
                'context_id',
                'is_required'
            ]
        );

        $section_elements = new backup_nested_element('section_elements');
        $section_element = new backup_nested_element(
            'section_element',
            ['id'],
            [
                'section_id',
                'element_id',
                'sort_order',
            ]
        );

        $section_relationships = new backup_nested_element('section_relationships');
        $section_relationship = new backup_nested_element(
            'section_relationship',
            ['id'],
            [
                'section_id',
                'activity_relationship_id',
                'can_view',
                'can_answer',
                'created_at',
            ]
        );

        $element_responses = new backup_nested_element('element_responses');
        $element_response = new backup_nested_element(
            'element_response',
            ['id'],
            [
                'section_element_id',
                'participant_instance_id',
                'response_data',
            ]
        );

        $tracks = new backup_nested_element('tracks');
        $track = new backup_nested_element(
            'track',
            ['id'],
            [
                'activity_id',
                'description',
                'status',
                'created_at',
                'updated_at',
                'schedule_fixed_from',
                'schedule_fixed_to',
                'schedule_is_open',
                'schedule_is_fixed',
                'schedule_dynamic_count_from',
                'schedule_dynamic_count_to',
                'schedule_dynamic_unit',
                'schedule_dynamic_direction',
                'schedule_needs_sync',
                'due_date_is_enabled',
            ]
        );

        $track_assignments = new backup_nested_element('track_assignments');
        $track_assignment = new backup_nested_element(
            'track_assignment',
            ['id'],
            [
                'track_id',
                'type',
                'user_group_type',
                'user_group_id',
                'created_by',
                'created_at',
                'updated_at',
                'expand',
            ]
        );

        $relationships = new backup_nested_element('relationships');
        $relationship = new backup_nested_element(
            'relationship',
            ['id'],
            [
                'activity_id',
                'core_relationship_id',
                'created_at',
            ]
        );

        $perform->add_child($elements);
        $elements->add_child($element);

        $perform->add_child($sections);
        $sections->add_child($section);
        $section->add_child($section_elements);
        $section_elements->add_child($section_element);
        $section->add_child($section_relationships);
        $section_relationships->add_child($section_relationship);


        $perform->add_child($tracks);
        $tracks->add_child($track);
        $track->add_child($track_assignments);
        $track_assignments->add_child($track_assignment);

        $perform->add_child($relationships);
        $relationships->add_child($relationship);

        // Define sources (in the same order as above).
        $perform->set_source_table('perform', ['id' => backup::VAR_ACTIVITYID]);

        $track->set_source_table('perform_track', ['activity_id' => backup::VAR_PARENTID]);
        $track_assignment->set_source_table('perform_track_assignment', ['track_id' => backup::VAR_PARENTID]);

        $section->set_source_table('perform_section', ['activity_id' => backup::VAR_PARENTID]);
        $section_element->set_source_table('perform_section_element', ['section_id' => backup::VAR_PARENTID]);
        $section_relationship->set_source_table('perform_section_relationship', ['section_id' => backup::VAR_PARENTID]);

        $relationship->set_source_table('perform_relationship', ['activity_id' => backup::VAR_PARENTID]);

        $element->set_source_sql(
            "SELECT pe.*
               FROM {perform_element} pe
               JOIN {perform_section_element} pse ON pse.element_id = pe.id
               JOIN {perform_section} ps ON pse.section_id = ps.id
              WHERE ps.activity_id = :activity_id",
            ['activity_id' => backup::VAR_PARENTID]
        );

        $perform->annotate_ids('perform_type', 'type_id');
        $relationship->annotate_ids('totara_core_relationship', 'core_relationship_id');

        return $this->prepare_activity_structure($perform);
    }

}