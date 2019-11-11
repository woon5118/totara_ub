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
 * @author Riana Rossow <riana.rossow@totaralearning.com>
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package criteria_coursecompletion
 */

use core\event\course_completed;
use criteria_coursecompletion\observer\course as course_observer;
use totara_completioneditor\event\course_completion_edited;

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => course_completed::class,
        'callback' => course_observer::class.'::course_completion_changed',
    ],
    [
        'eventname' => course_completion_edited::class,
        'callback' => course_observer::class.'::course_completion_changed',
    ],
];
