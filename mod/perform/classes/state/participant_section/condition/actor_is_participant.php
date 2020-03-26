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

namespace mod_perform\state\participant_section\condition;

use core\entities\user;
use mod_perform\state\condition;

defined('MOODLE_INTERNAL') || die();

/**
 * Class actor_is_participant
 */
class actor_is_participant extends condition {

    public function pass(): bool {
        return (int)$this->object->participant_instance->participant_id === (int)user::logged_in()->id;
    }

    public function get_failure(): array {
        return ['actor_is_participant' => get_string('condition_actor_is_participant_fail', 'mod_perform')];
    }
}
