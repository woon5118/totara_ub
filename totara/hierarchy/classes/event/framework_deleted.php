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
 * Abstract Event used as the base by each hierarchy,
 * triggered when a hierarchy framework is deleted.
 *
 * @property-read array $other {
 *      Extra information about the event.
 * }
 *
 * @author David Curry <david.curry@totaralms.com>
 * @package totara_hierarchy
 */
abstract class framework_deleted extends \core\event\base {

    /**
     * Flag for prevention of direct create() call.
     * @var bool
     */
    protected static $preventcreatecall = true;

    /**
     * The database record used to create the event.
     * @var \stdClass
     */
    protected $framework;

    /**
     * Create instance of event.
     *
     * @param   \stdClass $instance A hierarchy framework record.
     * @return  framework_deleted
     */
    public static function create_from_instance(\stdClass $instance) {
        $data = array(
            'objectid' => $instance->id,
            'context' => \context_system::instance(),
            'other' => array(),
        );

        self::$preventcreatecall = false;
        $event = self::create($data);
        $event->framework = $instance;
        $event->add_record_snapshot($event->objecttable, $instance);
        self::$preventcreatecall = true;

        return $event;
    }

    /**
     * Get hierarchy framework record.
     *
     * NOTE: to be used from observers only.
     *
     * @return \stdClass
     */
    public function get_framework() {
        if ($this->is_restored()) {
            throw new \coding_exception('get_framework() is intended for event observers only');
        }
        return $this->framework;
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The {$this->prefix} framework {$this->objectid} was deleted";
    }

    public function get_legacy_logdata() {
        $urlparams = array('prefix' => $this->prefix);

        $logdata = array();
        $logdata[] = SITEID;
        $logdata[] = $this->prefix;
        $logdata[] = 'framework delete';
        $logdata[] = new \moodle_url('/totara/hierarchy/framework/index.php', $urlparams);
        $logdata[] = "{$this->prefix} framework: {$this->objectid}";

        return $logdata;
    }

    protected function validate_data() {
        if (self::$preventcreatecall) {
            throw new \coding_exception('cannot call create() directly, use create_from_instance() instead.');
        }

        parent::validate_data();
    }
}
