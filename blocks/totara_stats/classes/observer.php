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
 * @package block_totara_stats
 */

namespace block_totara_stats;

use totara_competency\event\competency_record_updated;

class observer {

    public static function competency_record_updated(competency_record_updated $event) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/blocks/totara_stats/locallib.php');

        $user_id = $event->relateduserid;
        $competency_id = $event->other['competency_id'];

        $count = $DB->count_records(
            'block_totara_stats',
            ['userid' => $user_id, 'eventtype' => STATS_EVENT_COMP_ACHIEVED, 'data2' => $competency_id]
        );

        if (isset($scale_value)) {
            $isproficient = $DB->get_field('comp_scale_values', 'proficient', array('id' => $scale_value->get_id()));
        } else {
            $isproficient = 0;
        }

        // Check the proficiency is set to "proficient" and check for duplicate data.
        if ($isproficient && $count == 0) {
            totara_stats_add_event(time(), $user_id, STATS_EVENT_COMP_ACHIEVED, '', $competency_id);
        } else if ($isproficient == 0 && $count > 0) {
            totara_stats_remove_event($user_id, STATS_EVENT_COMP_ACHIEVED, $competency_id);
        }
    }
}