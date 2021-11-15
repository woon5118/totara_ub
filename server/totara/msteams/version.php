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
 * @author  Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_msteams
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2020100100;       // The current module version (Date: YYYYMMDDXX).
$plugin->requires = 2017111309;       // Requires this Moodle version.
$plugin->component = 'totara_msteams';
$plugin->dependencies = [
    'totara_engage' => 2019101200,
    'auth_oauth2' => 2019111800,
    // Note: soft dependency of block_current_learning
    // 'block_current_learning' => 2019102300,
    'theme_msteams' => 2020032500,
];
