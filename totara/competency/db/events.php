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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => \hierarchy_competency\event\competency_updated::class,
        'callback' => \totara_competency\observers\competency::class.'::updated',
    ],
    [
        'eventname' => \hierarchy_competency\event\competency_created::class,
        'callback' => \totara_competency\observers\competency::class.'::created',
    ],
    [
        'eventname' => \hierarchy_competency\event\competency_deleted::class,
        'callback' => \totara_competency\observers\competency::class.'::deleted',
    ],
    // Assignment events
    [
        'eventname' => \totara_competency\event\assignment_created::class,
        'callback'  => \totara_competency\observers\assignment::class.'::created'
    ],
    [
        'eventname' => \totara_competency\event\assignment_activated::class,
        'callback'  => \totara_competency\observers\assignment::class.'::activated'
    ],
    [
        'eventname' => \totara_competency\event\assignment_archived::class,
        'callback'  => \totara_competency\observers\assignment::class.'::archived'
    ],
    [
        'eventname' => \totara_competency\event\assignment_deleted::class,
        'callback'  => \totara_competency\observers\assignment::class.'::deleted'
    ],
    [
        // TODO if you are introducing another observer for this event, please consider moving logging there
        // to avoid clashes
        'eventname' => \totara_competency\event\assignment_user_assigned::class,
        'callback'  => \totara_competency\observers\user_log::class.'::log'
    ],
    [
        // TODO if you are introducing another observer for this event, please consider moving logging there
        // to avoid clashes
        'eventname' => \totara_competency\event\assignment_user_archived::class,
        'callback'  => \totara_competency\observers\user_log::class.'::log'
    ],
    [
        'eventname' => \totara_competency\event\assignment_user_unassigned::class,
        'callback'  => \totara_competency\observers\user_unassigned::class.'::observe'
    ],
    // Reacting to deleting competencies:
    [
        'eventname' => \hierarchy_competency\event\competency_deleted::class,
        'callback'  => \totara_competency\observers\competency_deleted::class.'::observe'
    ],
    // Reacting to deleting user groups:
    [
        'eventname' => \core\event\user_deleted::class,
        'callback'  => \totara_competency\observers\user_deleted::class.'::observe'
    ],
    [
        'eventname' => \core\event\cohort_deleted::class,
        'callback'  => \totara_competency\observers\audience_deleted::class.'::observe'
    ],
    [
        'eventname' => \hierarchy_position\event\position_deleted::class,
        'callback'  => \totara_competency\observers\position_deleted::class.'::observe'
    ],
    [
        'eventname' => \hierarchy_organisation\event\organisation_deleted::class,
        'callback'  => \totara_competency\observers\organisation_deleted::class.'::observe'
    ],
    [
        'eventname' => '\tassign_competency\event\assignment_user_assigned',
        'callback' => 'totara_competency\observer::user_assigned',
    ],
    [
        'eventname' => '\tassign_competency\event\assignment_user_unassigned',
        'callback' => 'totara_competency\observer::user_unassigned',
    ],
    [
        'eventname' => '\tassign_competency\event\assignment_user_archived',
        'callback' => 'totara_competency\observer::user_archived',
    ],
];
