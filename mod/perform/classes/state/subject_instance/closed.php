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

namespace mod_perform\state\subject_instance;

use mod_perform\state\transition;

defined('MOODLE_INTERNAL') || die();

/**
 * This class represents the "closed" availability status of a subject instance.
 *
 * @package mod_perform
 */
class closed extends subject_instance_availability {

    public static function get_name(): string {
        return 'CLOSED';
    }

    public static function get_code(): int {
        return 10;
    }

    public function get_transitions(): array {
        return [
            //The subject instance in open state.
            transition::to(new open($this->object)),
        ];
    }

    public function switch_state(): void {
        $this->object->switch_state(open::class);
    }
}
