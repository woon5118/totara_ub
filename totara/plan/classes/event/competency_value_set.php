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
 * @package totara_plan
 */

namespace totara_plan\event;

use core\event\base;

defined('MOODLE_INTERNAL') || die();

class competency_value_set extends base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['objecttable'] = 'dp_plan_competency_value';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * @param \stdClass $record Record from dp_plan_competency_value
     * @return competency_value_set
     */
    public static function create_from_record($record): competency_value_set {
        /** @var competency_value_set $event */
        $event = static::create([
            'objectid' => $record->id,
            'relateduserid' => $record->user_id,
            'other' => ['competency_id' => $record->competency_id],
            'context' => \context_system::instance()
        ]);

        return $event;
    }
}
