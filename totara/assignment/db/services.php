<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @package totara_assignment
 */

// Endpoints for the picker
$functions = [

    // List users
    'totara_assignment_user_index' => [
        'classname'     => \totara_assignment\services\user::class,
        'methodname'    => 'index',
        'description'   => 'List or search for users',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'moodle/user:viewdetails',
    ],

    // Expand user groups
    'totara_assignment_expand_user_groups_index' => [
        'classname'     => \totara_assignment\services\expand_user_groups::class,
        'methodname'    => 'index',
        'description'   => 'Expand user groups to preview what user groups do users have',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => implode(',', [
            'moodle/user:viewdetails',
            'moodle/cohort:view',
            'totara/hierarchy:viewposition',
            'totara/hierarchy:vieworganisation',
        ]),
    ],

    // List audiences
    'totara_assignment_cohort_index' => [
        'classname'     => \totara_assignment\services\cohort::class,
        'methodname'    => 'index',
        'description'   => 'List or search for audiences',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'moodle/cohort:view',
    ],

    // List position frameworks
    'totara_assignment_position_framework_index' => [
        'classname'     => \totara_assignment\services\position_framework::class,
        'methodname'    => 'index',
        'description'   => 'List or search for position frameworks',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'totara/hierarchy:viewpositionframeworks',
    ],

    // List positions
    'totara_assignment_position_index' => [
        'classname'     => \totara_assignment\services\position::class,
        'methodname'    => 'index',
        'description'   => 'List or search for positions',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'totara/hierarchy:viewposition',
    ],

    // Query position
    'totara_assignment_position_show' => [
        'classname'     => \totara_assignment\services\position::class,
        'methodname'    => 'show',
        'description'   => 'Display a single position',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'totara/hierarchy:viewposition',
    ],


    // List organisation frameworks
    'totara_assignment_organisation_framework_index' => [
        'classname'     => \totara_assignment\services\organisation_framework::class,
        'methodname'    => 'index',
        'description'   => 'List or search for organisation frameworks',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'totara/hierarchy:vieworganisationframeworks',
    ],

    // List organisations
    'totara_assignment_organisation_index' => [
        'classname'     => \totara_assignment\services\organisation::class,
        'methodname'    => 'index',
        'description'   => 'List or search for organisations',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'totara/hierarchy:vieworganisation',
    ],

    // Query organisation
    'totara_assignment_organisation_show' => [
        'classname'     => \totara_assignment\services\organisation::class,
        'methodname'    => 'show',
        'description'   => 'Display a single organisation',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'totara/hierarchy:vieworganisation',
    ],
];