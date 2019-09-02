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

use \core\task\adhoc_task;
use criteria_linkedcourses\metadata_processor;

/**
 * This adhoc task refreshes criteria items for linkedcourses criteria in the competency's achievement criteria
 */
class update_linked_course_items_adhoc extends adhoc_task {

    public function execute() {
        $data = $this->get_custom_data();

        if (empty($data->competency_id)) {
            throw new \coding_exception('Missing competency_id in update_linked_course_items_adhoc task');
        }

        metadata_processor::update_item_links($data->competency_id);
    }
}
