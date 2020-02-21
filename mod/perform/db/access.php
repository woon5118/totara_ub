<?php
/*
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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    // View the performance activities management page, where users can create and manage performance activities.
    'mod/perform:view_manage_activities' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => [
            'performanceactivitycreator' => CAP_ALLOW,
        ]
    ],
    // Create a performance activity. Additonally requires the capability to access the management page and create
    // a performance container.
    'mod/perform:create_activity' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => [
            'performanceactivitycreator' => CAP_ALLOW,
        ]
    ],
    // Manage a performance activity. Automatically granted when a user creates a performance activity.
    // Addtionally requires 'mod/perform:view_manage_activities' to access the page where creation is
    // done - if a user created a performance activity themselves then they must already have the capability
    // to view the management page.
    'mod/perform:manage_activity' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'performanceactivitymanager' => CAP_ALLOW,
        ]
    ],
];