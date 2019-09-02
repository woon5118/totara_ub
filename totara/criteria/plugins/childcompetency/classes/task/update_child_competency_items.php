<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace criteria_childcompetency\task;

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;
use criteria_childcompetency\items_processor;
use totara_competency\entities\competency;
use totara_competency\pathway;

// TODO: We will not need a task once we have the migration / system upgrade code
//       We only need to do this once on upgrade after data migration to perform criteria
//       From then on the event observers will handle this
//       Leaving the task for now until migration is sorted.

class update_child_competency_items extends scheduled_task {
    public function get_name() {
        return get_string('updatechildcompetencyitems', 'criteria_childcompetency');
    }

    public function execute() {
        global $DB;

        items_processor::update_items(null);
    }
}