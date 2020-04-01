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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\event;

defined('MOODLE_INTERNAL') || die();

class track_user_assigned_bulk extends track_user_assignment {

    protected $user_ids = [];

    protected $is_bulk = true;

    public function get_user_id(): ?int {
        return null;
    }

    public function get_user_ids(): array {
        return $this->other['user_ids'];
    }

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'perform_track_user_assignment';
    }

    public static function create_from_user_assignments(
        int $track_id,
        array $subject_user_ids = [],
        ?string $assignment_type = null
    ) {
        $data = [
            'objectid' => $track_id,
            'other' => [
                'user_ids' => $subject_user_ids,
                'type' => $assignment_type ?? null,
            ],
            // TODO Should this be an activity context?
            'context' => \context_system::instance()
        ];
        return static::create($data);
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_track_user_assigned', 'mod_perform');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return 'User has been assigned to a track';
    }

}
