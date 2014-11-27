<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2014 onwards Totara Learning Solutions LTD
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

namespace totara_hierarchy\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract Event used as the base by each competency_assignment,
 * triggered when a competency_assignment assignment is created.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - competencyid  The id of the related competency
 *      - instanceid    The id of the related pos/org
 * }
 *
 * @author David Curry <david.curry@totaralms.com>
 * @package totara_competency_assignment
 */
abstract class competency_unassigned extends \core\event\base {

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
     * @param   \stdClass $instance A competency_assignment assignment record.
     * @return  assignment_created
     */
    public static function create_from_instance(\stdClass $instance) {
        $data = array(
            'objectid' => $instance->id,
            'context' => \context_system::instance(),
            'other' => array(
                'competencyid' => $instance->competencyid,
                'instanceid' => $instance->instanceid, // The id of the pos/org.
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
     * Get competency_assignment assignment record.
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
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $competencyid = $this->data['other']['competencyid'];
        $instanceid = $this->data['other']['instanceid'];

        return "The competency: {$competencyid} was unassigned from the {$this->prefix}: {$instanceid}";
    }

    public function get_legacy_logdata() {
        $urlparams = array('id' => $this->data['other']['instanceid'], 'prefix' => $this->prefix);

        $logdata = array();
        $logdata[] = SITEID;
        $logdata[] = $this->prefix;
        $logdata[] = 'delete competency assignment';
        $logdata[] = new \moodle_url('/totara/hierarchy/item/view.php', $urlparams);
        $logdata[] = "{$this->prefix}: {$this->data['other']['instanceid']} - competency: {$this->data['other']['competencyid']}";

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

        if (!isset($this->other['competencyid'])) {
            throw new \coding_exception('competencyid must be set in $other');
        }

        if (!isset($this->other['instanceid'])) {
            throw new \coding_exception('instanceid must be set in $other');
        }
    }
}
