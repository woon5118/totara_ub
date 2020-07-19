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

namespace mod_perform\state\subject_instance\condition;

use mod_perform\entities\activity\participant_instance;
use mod_perform\state\condition;
use mod_perform\state\participant_instance\complete;

defined('MOODLE_INTERNAL') || die();

/**
 * Class all_participant_instances_complete
 */
class all_participant_instances_complete extends condition {

    public function pass(): bool {
        /** @var participant_instance[] $participant_instances */
        $participant_instances = $this->object->participant_instances->all();
        foreach ($participant_instances as $participant_instance) {
            if ((int)$participant_instance->progress !== complete::get_code()) {
                return false;
            }
        }
        return true;
    }
}
