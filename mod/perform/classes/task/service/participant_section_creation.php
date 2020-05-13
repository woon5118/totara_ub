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
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\task\service;

use core\collection;
use core\orm\query\builder;
use mod_perform\entities\activity\participant_section;
use mod_perform\entities\activity\section;
use mod_perform\entities\activity\section_relationship;
use mod_perform\state\participant_section\not_started;
use stdClass;

/**
 * Class participant_section_creation
 *
 * @package mod_perform\task\service
 */
class participant_section_creation {

    /**
     * @var array
     */
    private $participant_sections = [];

    /**
     * Maximum number of participant sections aggregated before bulk insert.
     *
     * @var int
     */
    private $buffer_count = BATCH_INSERT_MAX_ROW_COUNT;

    /**
     * Generate participant sections for a list of participant instances.
     *
     * @param collection|participant_instance_dto[] $participant_instances
     *
     * @return void
     */
    public function generate_sections(collection $participant_instances): void {
        builder::get_db()->transaction(
            function () use ($participant_instances) {
                $activity_relationship_id = array_unique($participant_instances->pluck('activity_relationship_id'));
                $section_ids = $this->get_section_ids_for_activity_relationships($activity_relationship_id);

                foreach ($participant_instances as $participant_instance) {
                    foreach ($section_ids[$participant_instance->activity_relationship_id] as $section_id) {
                        $data = new stdClass();
                        $data->section_id = $section_id;
                        $data->status = not_started::get_code();
                        $data->created_at = time();
                        $data->participant_instance_id = $participant_instance->id;
                        $this->aggregate_participant_section($data);
                    }
                }
                $this->save_participant_sections();
            }
        );

    }

    /**
     * Get section ids for the activity relationship ids.
     *
     * @param array $activity_relationship_id
     * @return array
     */
    private function get_section_ids_for_activity_relationships(array $activity_relationship_id): array {
        $relationship_sections = section_relationship::repository()
            ->where_in('activity_relationship_id', $activity_relationship_id)
            ->select(['id', 'section_id', 'activity_relationship_id'])
            ->get();
        $result = [];

        foreach ($relationship_sections as $relationship_section) {
            if (!isset($result[$relationship_section->activity_relationship_id])) {
                $result[$relationship_section->activity_relationship_id] = [];
            }
            $result[$relationship_section->activity_relationship_id][] = $relationship_section->section_id;
        }

        return $result;
    }

    /**
     * Aggregates the participant_sections to create.
     *
     * @param stdClass $data
     * @return void
     */
    private function aggregate_participant_section(stdClass $data): void {
        $this->participant_sections[] = $data;

        if (count($this->participant_sections) === $this->buffer_count) {
            $this->save_participant_sections();
        }
    }

    /**
     * Inserts records into the database.
     *
     * @return void
     */
    private function save_participant_sections(): void {
        if (empty($this->participant_sections)) {
            return;
        }

        builder::get_db()->insert_records(
            participant_section::TABLE,
            $this->participant_sections
        );
        $this->participant_sections = [];
    }
}
