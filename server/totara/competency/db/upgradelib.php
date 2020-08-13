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
 * @package totara_competency
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This function is used to update web service definitions for core if we're upgrading from moodle.
 * This should be temporary until these core services are moved to GQL
 *
 * @return void
 */
function totara_competency_install_core_services() {
    // Let's check whether we need to do anything at all
    global $DB, $CFG;

    // If it's already been created, no point to waste resources on running descriptions upgrade
    if ($DB->record_exists('external_functions', ['name' => 'core_user_index'])) {
        return;
    }

    require_once $CFG->libdir . '/db/upgradelib.php';

    // This will refresh external services from core without an explicit version bumps
    external_update_descriptions('moodle');
}
