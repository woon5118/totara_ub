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

use restore_path_element;
use mod_perform\models\activity\activity;

global $CFG;
require_once($CFG->dirroot . '/backup/moodle2/restore_stepslib.php');

class restore_activity_structure_step extends \restore_activity_structure_step {

    protected function define_structure() {
        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element(
            'perform',
            '/activity/perform'
        );

        $paths[] = new restore_path_element(
            'track',
            '/activity/perform/tracks/track'
        );

        $paths[] = new restore_path_element(
            'section',
            '/activity/perform/sections/section'
        );

        $paths[] = new restore_path_element(
            'element',
            '/activity/perform/elements/element'
        );

        $paths[] = new restore_path_element(
            'relationship',
            '/activity/perform/relationships/relationship'
        );

        $paths[] = new restore_path_element(
            'section_element',
            '/activity/perform/sections/section/section_elements/section_element'
        );

        $paths[] = new restore_path_element(
            'section_relationship',
            '/activity/perform/sections/section/section_relationships/section_relationship'
        );

        $paths[] = new restore_path_element(
            'track_assignment',
            '/activity/perform/tracks/track/track_assignments/track_assignment'
        );

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_perform($data) {
        global $DB;

        $data = (object)$data;

        $data->course = $this->get_courseid();
        $data->status = 0; // Lets keep it draft

        $is_cloning = $this->get_setting_value('is_cloning');
        if ($is_cloning && !preg_match('/^multilang:/', $data->name)) {
            $data->name = $data->name . get_string('activity_name_restore_suffix', 'mod_perform');
        }

        // Keeping or moving these times makes little sense, but it is the expected Moodle way...
        $data->created_at = $this->apply_date_offset($data->created_at);
        $data->updated_at = $this->apply_date_offset($data->updated_at);

        $new_item_id = $DB->insert_record('perform', $data);
        $this->apply_activity_instance($new_item_id);
    }

    protected function process_section($data) {
        global $DB;

        $data = (object)$data;
        $old_id = $data->id;

        $data->activity_id = $this->get_new_parentid('perform');

        // Keeping or moving these times makes little sense, but it is the expected Moodle way...
        $data->created_at = $this->apply_date_offset($data->created_at);
        $data->updated_at = $this->apply_date_offset($data->updated_at);

        $new_item_id = $DB->insert_record('perform_section', $data);
        $this->set_mapping('perform_section', $old_id, $new_item_id);
    }

    protected function process_element($data) {
        global $DB;

        $data = (object)$data;
        $old_id = $data->id;

        $activity_id = $this->get_new_parentid('perform');
        $data->context_id = activity::load_by_id($activity_id)->get_context()->id;

        $new_item_id = $DB->insert_record('perform_element', $data);
        $this->set_mapping('perform_element', $old_id, $new_item_id);
    }

    protected function process_section_element($data) {
        global $DB;

        $data = (object)$data;
        $old_id = $data->id;

        $data->section_id = $this->get_mappingid('perform_section', $data->section_id);
        $data->element_id = $this->get_mappingid('perform_element', $data->element_id);

        $new_item_id = $DB->insert_record('perform_section_element', $data);
        $this->set_mapping('perform_section_element', $old_id, $new_item_id);
    }

    protected function process_section_relationship($data) {
        global $DB;

        $data = (object)$data;
        $old_id = $data->id;

        // Keeping or moving these times makes little sense, but it is the expected Moodle way...
        $data->created_at = $this->apply_date_offset($data->created_at);

        $data->section_id = $this->get_mappingid('perform_section', $data->section_id);

        $new_item_id = $DB->insert_record('perform_section_relationship', $data);
        $this->set_mapping('perform_section_relationship', $old_id, $new_item_id);
    }

    protected function process_track($data) {
        global $DB;

        $data = (object)$data;
        $old_id = $data->id;

        $data->activity_id = $this->get_new_parentid('perform');

        // Keeping or moving these times makes little sense, but it is the expected Moodle way...
        $data->created_at = $this->apply_date_offset($data->created_at);
        $data->updated_at = $this->apply_date_offset($data->updated_at);

        $new_item_id = $DB->insert_record('perform_track', $data);
        $this->set_mapping('perform_track', $old_id, $new_item_id);
    }

    protected function process_track_assignment($data) {
        global $DB;

        $data = (object)$data;
        $old_id = $data->id;

        $data->track_id = $this->get_mappingid('perform_track', $data->track_id);

        // Keeping or moving these times makes little sense, but it is the expected Moodle way...
        $data->created_at = $this->apply_date_offset($data->created_at);
        $data->updated_at = $this->apply_date_offset($data->updated_at);

        $new_item_id = $DB->insert_record('perform_track_assignment', $data);
        $this->set_mapping('perform_track_assignment', $old_id, $new_item_id);
    }

    protected function process_relationship($data) {
        global $DB;

        $data = (object)$data;
        $old_id = $data->id;

        $data->activity_id = $this->get_new_parentid('perform');

        // Keeping or moving these times makes little sense, but it is the expected Moodle way...
        $data->created_at = $this->apply_date_offset($data->created_at);

        $new_item_id = $DB->insert_record('perform_relationship', $data);
        $this->set_mapping('perform_relationship', $old_id, $new_item_id);
    }

}