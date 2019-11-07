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

namespace totara_competency\event;

defined('MOODLE_INTERNAL') || die();

use core\event\base;
use totara_competency\entities\competency_assignment_user;

abstract class assignment_user extends base {

    /**
     * Create instance of event.
     *
     * @param competency_assignment_user $assignment_user
     * @param string $assignment_type
     * @return self
     */
    public static function create_from_assignment_user(competency_assignment_user $assignment_user, ?string $assignment_type = null) {
        $data = [
            'objectid' => $assignment_user->id,
            'relateduserid' => $assignment_user->user_id,
            'other' => [
                'assignment_id' => $assignment_user->assignment_id,
                'competency_id' => $assignment_user->competency_id,
                'type' => $assignment_type ?? null,
            ],
            'context' => \context_system::instance()
        ];
        /** @var static $event */
        $event = static::create($data);
        $event->add_record_snapshot('totara_assignment_competency_users', (object)$assignment_user->get_attributes_raw());
        return $event;
    }

}
