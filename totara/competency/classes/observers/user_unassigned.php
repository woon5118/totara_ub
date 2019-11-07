<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\observers;

use totara_competency\event\assignment_user_unassigned;
use totara_competency\models\assignment_user;
use totara_competency\settings;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer
 */
class user_unassigned {

    /**
     * Triggered via assignment_user_unassigned event.
     *
     * @param assignment_user_unassigned $event
     * @return bool true on success
     */
    public static function unassigned(assignment_user_unassigned $event) {
        $data = $event->get_data();
        $competency_id = $data['other']['competency_id'] ?? null;
        $user_id = $data['relateduserid'];

        // Create a new assignment for continuous tracking if setting is enabled
        if (settings::is_continuous_tracking_enabled()) {
            (new assignment_user($user_id))->create_system_assignment($competency_id);
        }

        // Delete related competency records if unassign behaviour is set up that way
        if (!settings::should_unassign_keep_records()) {
            // TODO TL-20480 implement actual behaviour
        }

        return true;
    }

}
