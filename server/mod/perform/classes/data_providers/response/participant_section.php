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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_userstatus
 */

namespace mod_perform\data_providers\response;

use Exception;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\models\response\participant_section as participant_section_model;

class participant_section {

    /**
     * @var int
     */
    protected $participant_id;

    /**
     * @var int
     */
    protected $participant_source;

    public function __construct(int $participant_id, int $participant_source) {
        $this->participant_id = $participant_id;
        $this->participant_source = $participant_source;
    }

    /**
     * Load the section with given data
     *
     * @param int $participant_section_id
     * @return participant_section_model|null
     */
    public function find_by_section_id(int $participant_section_id): ?participant_section_model {
        try {
            $participant_section = participant_section_model::load_by_id($participant_section_id);
        } catch (Exception $e) {
            $participant_section = null;
        }

        return $this->is_valid($participant_section) ? $participant_section : null;
    }

    /**
     * Load the section with given data
     *
     * @param int $participant_instance_id
     * @return participant_section_model|null
     */
    public function find_by_instance_id(int $participant_instance_id): ?participant_section_model {
        $participant_section_entity = participant_section_entity::repository()
            ->fetch_default($participant_instance_id, $this->participant_id);

        $participant_section = $participant_section_entity
            ? participant_section_model::load_by_entity($participant_section_entity)
            : null;

        return $this->is_valid($participant_section) ? $participant_section : null;
    }

    /**
     * Check whether this section (if given) is valid
     *
     * @param participant_section_model|null $participant_section
     * @return bool
     */
    private function is_valid(?participant_section_model $participant_section): bool {
        if ($participant_section) {
            $participant_instance = $participant_section->participant_instance;

            // If the section does belong to the current user return it;
            if ($participant_instance->participant_id == $this->participant_id
                && $this->participant_source == $participant_instance->participant_source
            ) {
                return true;
            }
        }

        return false;
    }

}