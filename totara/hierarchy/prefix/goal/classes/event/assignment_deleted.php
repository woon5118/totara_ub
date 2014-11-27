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

namespace hierarchy_goal\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Abstract Event used as the base by each assignmenttype,
 * triggered when a goal is unassigned.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - goalid        The id of the goal being assigned
 *      - instanceid    The id of the item (cohort/pos/org/user)
 * }
 *
 * @author David Curry <david.curry@totaralms.com>
 * @package totara_hierarchy
 */
abstract class assignment_deleted extends \core\event\base {

    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * The database record used to create the event.
     * @var \stdClass
     */
    protected $assignment;

    /**
     * Create instance of event.
     *
     * @param   \stdClass $instance A  goal record.
     * @return  item_deleted
     */
    public static function create_from_instance(\stdClass $instance) {
        $userid = isset($instance->userid) ? $instance->userid : null;

        $data = array(
            'objectid' => $instance->id,
            'context' => \context_system::instance(),
            'relateduserid' => $userid,
            'other' => array(
                'goalid' => $instance->goalid,
                'instanceid' => $instance->instanceid,
            ),
        );

        self::$preventcreatecall = false;
        $event = self::create($data);
        $event->assignment = $instance;
        $event->add_record_snapshot($event->objecttable, $instance);
        self::$preventcreatecall = true;

        return $event;
    }

    /**
     * Get goal assignment record.
     *
     * NOTE: to be used from observers only.
     *
     * @return \stdClass
     */
    public function get_assignment() {
        if ($this->is_restored()) {
            throw new \coding_exception('get_assignment() is intended for event observers only');
        }
        return $this->assignment;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string("eventdeletedassignment", "hierarchy_goal");
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The  goal: {$this->data['other']['goalid']} was assigned to {$this->type}: {$this->data['other']['instanceid']}";
    }

    public function get_legacy_logdata() {
        $urlparams = array('id' => $this->data['other']['goalid'], 'prefix' => 'goal');
        $strassig = "goal {$this->data['other']['goalid']} - {$this->type} {$this->data['other']['instanceid']}";

        $logdata = array();
        $logdata[] = SITEID;
        $logdata[] = 'goal';
        $logdata[] = 'delete goal assignment';
        $logdata[] = new \moodle_url('/totara/hierarchy/item/view.php', $urlparams);
        $logdata[] = $strassig;

        return $logdata;
    }

    /**
     * Custom validation
     *
     * @throws \coding_exception
     * @return void
     */
    public function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call create() directly, use create_from_instance() instead.');
        }

        parent::validate_data();

        if (!isset($this->other['goalid'])) {
            throw new \coding_exception('goalid must be set in $other');
        }

        if (!isset($this->other['instanceid'])) {
            throw new \coding_exception('instanceid must be set in $other');
        }
    }
}
