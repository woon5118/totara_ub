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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace criteria_linkedcourses\task;

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;
use criteria_linkedcourses\metadata_processor;
use totara_competency\entities\competency;
use totara_competency\pathway;

// TODO: We will not need this task once we have the migration / system upgrade code
//       We only need to do this once on upgrade after data migration to perform criteria
//       From then on the adhoc task on edit will handle changes in linked courses
//       Leaving the task for now until migration is sorted.

class update_linked_course_criteria extends scheduled_task {
    public function get_name() {
        return get_string('updatelinkedcoursecriteria', 'criteria_linkedcourses');
    }

    public function execute() {
        global $DB;

        metadata_processor::update_item_links(null);
    }
}