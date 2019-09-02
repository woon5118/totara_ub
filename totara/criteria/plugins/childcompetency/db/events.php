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
 * @package criteria_coursecompletion
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\hierarchy_competency\event\competency_created',
        'callback' => '\criteria_childcompetency\observer::competency_created',
    ],
    [
        'eventname' => '\hierarchy_competency\event\competency_moved',
        'callback' => '\criteria_childcompetency\observer::competency_moved',
    ],
    [
        'eventname' => '\hierarchy_competency\event\competency_deleted',
        'callback' => '\criteria_childcompetency\observer::competency_deleted',
    ],
];
