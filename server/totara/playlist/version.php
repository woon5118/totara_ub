<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_playlist
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version = 2020090100;          // The current module version (Date: YYYYMMDDXX).
$plugin->requires = 2017111309;         // Requires this Totara version.
$plugin->component = 'totara_playlist';

$plugin->dependencies = [
    'totara_engage' => 2019101201,
    'totara_topic' => 2019112700,
    'totara_comment' => 2019101500,
    'editor_weka' => 2019111800
];
