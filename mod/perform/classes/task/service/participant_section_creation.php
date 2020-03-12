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

use coding_exception;
use core\collection;
use core\orm\query\builder;
use dml_exception;
use mod_perform\entities\activity\participant_section;
use mod_perform\entities\activity\section;
use mod_perform\models\activity\participant_section_status;
use stdClass;
use Throwable;

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
                $activity_ids = array_unique($participant_instances->pluck('activity_id'));
                $section_ids = $this->get_section_ids_for_activities($activity_ids);

                foreach ($section_ids as $section_id) {
                    $this->aggregate_participants_for_section($section_id, $participant_instances);
                }
                $this->save_participant_sections();
            }
        );

    }

    /**
     * Get section ids for the activity ids.
     *
     * @param array $activity_ids
     * @return array
     */
    private function get_section_ids_for_activities(array $activity_ids): array {
        return section::repository()
            ->where_in('activity_id', $activity_ids)
            ->select('id')
            ->get()
            ->pluck('id');
    }

    /**
     * Aggregates participants for a section.
     *
     * @param int $section_id
     * @param collection $participant_instance_list
     * @return void
     */
    private function aggregate_participants_for_section(int $section_id, collection $participant_instance_list): void {
        foreach ($participant_instance_list as $participant_instance) {
            $data = new stdClass();
            $data->section_id = $section_id;
            $data->status = participant_section_status::NOT_STARTED;
            $data->created_at = time();
            $data->participant_instance_id = $participant_instance->id;
            $this->aggregate_participant_section($data);
        }
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
