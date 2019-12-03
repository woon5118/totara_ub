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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_manual
 */

namespace pathway_manual\models\roles;

use totara_job\job_assignment;

class manager extends role {

    /**
     * Get the display name for this role.
     *
     * @return string
     */
    public static function get_display_name(): string {
        return get_string('manager', 'totara_job');
    }

    /**
     * Get the position in which the manager role should be displayed in a list.
     *
     * @return int
     */
    public static function get_display_order(): int {
        return 20; // 2nd in list.
    }

    /**
     * Is the current user the manager of the specified user?
     *
     * @param int $subject_user
     * @return bool
     */
    public static function has_for_user(int $subject_user): bool {
        return has_capability('totara/competency:rate_other_competencies', \context_user::instance($subject_user));
    }

}
