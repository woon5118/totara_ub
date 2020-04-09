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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\state\participant_section;

use core\event\base;
use mod_perform\event\participant_section_progress_updated;
use mod_perform\models\response\participant_section;
use mod_perform\state\state_event;

defined('MOODLE_INTERNAL') || die();

/**
 * This class represents the "complete" progress status of a participant section.
 *
 * @package mod_perform
 */
class complete extends participant_section_progress implements state_event {

    public static function get_name(): string {
        return 'COMPLETE';
    }

    public static function get_code(): int {
        return 20;
    }

    public function get_transitions(): array {
        return [];
    }

    public function get_event(): base {
        /** @var participant_section $participant_section */
        $participant_section = $this->get_object();
        return participant_section_progress_updated::create_from_participant_section($participant_section);
    }

    public function complete(): void {
        // Already in complete state. Do nothing.
        return;
    }

    public function on_participant_access(): void {
        // Not relevant when already complete. Do nothing.
        return;
    }
}
