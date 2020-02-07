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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

/**
 * perform activity generator
 *
 * Usage:
 *    $generator = $this->getDataGenerator()->get_plugin_generator('mod_perform');
 */
class mod_perform_generator extends component_generator_base {

    public function create_activity($data = []) {
        $activity = new mod_perform\entities\activity\activity();
        $activity->name = ($data['name']) ?? "test performance activity";
        $activity->status = mod_perform\entities\activity\activity::STATUS_ACTIVE;
        $activity->timemodified = time();
        $activity->save();
    }
}