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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use coding_exception;
use core\orm\entity\repository;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;

class participant_section_repository extends repository {

    /**
     * Get the first participant section for a given subject instance and participant.
     *
     * @param int $subject_instance_id
     * @param int $participant_id
     * @return participant_section
     * @throws coding_exception
     */
    public function fetch_default(int $subject_instance_id, int $participant_id): participant_section_entity {
        /** @var participant_section_entity $first_participant_section */
        $first_participant_section = participant_section_entity::repository()
            ->as('ps')
            ->join([participant_instance_entity::TABLE, 'pi'], 'ps.participant_instance_id', 'pi.id')
            ->join([subject_instance_entity::TABLE, 'si'], 'pi.subject_instance_id', 'si.id')
            ->where('pi.participant_id', $participant_id)
            ->where('si.id', $subject_instance_id)
            ->order_by('ps.id', 'desc')
            ->first();

        if ($first_participant_section === null) {
            throw new coding_exception('No participant section found for this subject instance and given participant');
        }

        return $first_participant_section;
    }

}