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

namespace mod_perform\state\participant_instance\condition;

use mod_perform\entities\activity\participant_section;
use mod_perform\state\condition;
use mod_perform\state\participant_section\complete;
use mod_perform\state\participant_section\in_progress;

defined('MOODLE_INTERNAL') || die();

/**
 * Class at_least_one_section_started
 */
class at_least_one_section_started extends condition {

    public function pass(): bool {
        /** @var participant_section[] $sections */
        $sections = $this->object->participant_sections->all();
        foreach ($sections as $section) {
            if (in_array((int)$section->progress, [in_progress::get_code(), complete::get_code()], true)) {
                return true;
            }
        }
        return false;
    }
}
