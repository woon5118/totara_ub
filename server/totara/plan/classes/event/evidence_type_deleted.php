<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package totara_plan
 */

namespace totara_plan\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Event triggered when evidence type deleted.
 *
 * @deprecated since Totara 13
 * @property-read array $other {
 * Extra information about the event.
 *    - name
 * }
 *
 */
class evidence_type_deleted extends \core\event\base {
    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * Create an event.
     * @deprecated since Totara 13
     * @param \stdClass $type
     * @return evidence_type_deleted
     */
    public static function create_from_type($type) {
        debugging('\totara_plan\event\evidence_type_deleted has been deprecated and is no longer used, please use totara_evidence\event\evidence_type_deleted instead.', DEBUG_DEVELOPER);
        $data = array(
            'objectid' => $type->id,
            'context' => \context_system::instance(),
            'other' => array('name' => $type->name),
        );

        self::$preventcreatecall = false;
        /** @var evidence_type_deleted $event */
        $event = self::create($data);
        self::$preventcreatecall = true;

        return $event;
    }

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'dp_evidence_type';
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventevidencetypedeleted', 'totara_plan');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '{$this->userid}' deleted evidence type with id '{$this->objectid}'";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return null;
    }

    /**
     * Custom validation
     *
     * @throws \coding_exception
     * @return void
     */
    public function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call create() directly');
        }
        parent::validate_data();
    }
}
