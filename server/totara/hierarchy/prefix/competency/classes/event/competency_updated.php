<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author David Curry <david.curry@totaralms.com>
 * @package totara_hierarchy
 */

namespace hierarchy_competency\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Triggered when a hierarchy is updated.
 *
 * @property-read array $other {
 *      Extra information about the event.
 * }
 *
 * @author David Curry <david.curry@totaralms.com>
 * @package totara_hierarchy
 */
class competency_updated extends \totara_hierarchy\event\hierarchy_updated {

    /**
     * In some cases we need to know what changed in comparison to the old
     * item in the database so in this case we provide the old instance as well
     * to be able to check this in the observers
     *
     * @param \stdClass $new_instance
     * @param \stdClass $old_instance
     * @return \core\event\base
     */
    public static function create_from_old_and_new(\stdClass $new_instance, \stdClass $old_instance) {
        $data = array(
            'objectid' => $new_instance->id,
            'context' => \context_system::instance(),
            'other' => [
                'old_instance' => (array)$old_instance
            ]
        );

        self::$preventcreatecall = false;
        $event = self::create($data);
        $event->add_record_snapshot($event->objecttable, $new_instance);
        self::$preventcreatecall = true;

        return $event;
    }

    /**
     * Returns hierarchy prefix.
     * @return string
     */
    public function get_prefix() {
        return 'competency';
    }

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = 'comp';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name() {
        return get_string('eventupdateditem', 'hierarchy_competency');
    }
}
