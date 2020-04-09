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

namespace mod_perform\state\subject_instance;

use core\event\base;
use mod_perform\event\subject_instance_progress_updated;
use mod_perform\models\activity\subject_instance;
use mod_perform\state\state_event;
use mod_perform\state\subject_instance\condition\not_all_participant_instances_complete;
use mod_perform\state\transition;

defined('MOODLE_INTERNAL') || die();

/**
 * This class represents the "complete" progress status of a subject instance.
 *
 * @package mod_perform
 */
class complete extends subject_instance_progress implements state_event {

    public static function get_name(): string {
        return 'COMPLETE';
    }

    public static function get_code(): int {
        return 20;
    }

    public function get_transitions(): array {
        return [
            transition::to(new in_progress($this->object))->with_conditions([
                not_all_participant_instances_complete::class,
            ]),
        ];
    }

    public function update_progress(): void {
        if ($this->can_switch(in_progress::class)) {
            $this->object->switch_state(in_progress::class);
        }
    }

    public function get_event(): base {
        /** @var subject_instance $subject_instance */
        $subject_instance = $this->get_object();
        return subject_instance_progress_updated::create_from_subject_instance($subject_instance);
    }
}
