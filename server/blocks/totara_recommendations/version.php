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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Vernon Denny <vernon.denny@totaralearning.com>
 * @package block_totara_recommendations
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2020100100;       // The current module version (Date: YYYYMMDDXX).
$plugin->requires = 2017111309;       // Requires this Moodle version.
$plugin->component = 'block_totara_recommendations'; // To check on upgrade, that module sits in correct place
$plugin->dependencies = array(
    'ml_recommender' => 2020072200,
    'block_totara_recently_viewed' => 2020060900,
);
