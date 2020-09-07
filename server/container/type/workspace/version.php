<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */

defined('MOODLE_INTERNAL') || die();

/* NOTE: the following version number must be bumped during each major or minor Totara release. */

$plugin->version  = 2020090101;                 // The current module version (Date: YYYYMMDDXX).
$plugin->requires = 2017111309;                 // Requires this Moodle version.
$plugin->component = 'container_workspace';          // To check on upgrade, that module sits in correct place

$plugin->dependencies = [
    'totara_engage' => 2019101202,
    'editor_weka' => 2019111800,
    'totara_comment' => 2019101500,
    'enrol_self' => 2017111300,
    'enrol_manual' => 2017111300
];