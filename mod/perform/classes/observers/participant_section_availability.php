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

namespace mod_perform\observers;

use core\event\base;
use mod_perform\event\participant_section_progress_updated;
use mod_perform\models\response\participant_section;
use mod_perform\state\participant_section\closed;
use mod_perform\state\participant_section\complete;
use mod_perform\state\participant_section\participant_section_availability as availability_state;

class participant_section_availability {

    /**
     * When progress status of a participant section is updated to complete,
     * the availability of participant section is closed is close on completion is enabled.
     *
     * @param base|participant_section_progress_updated $event
     */
    public static function close_completed_section_availability(base $event) {
        $participant_section = participant_section::load_by_id($event->objectid);

        if (self::can_switch($participant_section)) {
            $participant_section->get_availability_state()->close();
        }
    }

    /**
     * Conditions to allow closing the availability.
     *
     * @param participant_section $participant_section
     * @return bool
     */
    private static function can_switch(participant_section $participant_section): bool {
        return $participant_section->get_progress_state() instanceof complete
            && $participant_section->get_section()->get_activity()->close_on_completion;
    }
}