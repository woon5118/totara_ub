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
 * @package tassign_competency
 */


$assignments = [
    'tassign_competency_assignment_index' => [
        'classname'     => \tassign_competency\services\assignment::class,
        'methodname'    => 'index',
        'description'   => 'Returns a list of current assignments',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'tassign/competency:view',
    ],

    'tassign_competency_assignment_show' => [
        'classname'     => \tassign_competency\services\assignment::class,
        'methodname'    => 'index',
        'description'   => 'Return a single competency assignment',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'tassign/competency:view',
    ],

    'tassign_competency_assignment_create' => [
        'classname'     => \tassign_competency\services\assignment::class,
        'methodname'    => 'create',
        'description'   => 'Create a competency assignment(s) from a basket',
        'type'          => 'write',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'tassign/competency:manage',
    ],

    'tassign_competency_assignment_create_from_baskets' => [
        'classname'     => \tassign_competency\services\assignment::class,
        'methodname'    => 'create_from_baskets',
        'description'   => 'Create a competency assignment(s) from a basket',
        'type'          => 'write',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'tassign/competency:manage',
    ],

    'tassign_competency_assignment_action' => [
        'classname'     => \tassign_competency\services\assignment::class,
        'methodname'    => 'action',
        'description'   => 'Run action on one or multiple assignments (activate, archive, delete)',
        'type'          => 'write',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'tassign/competency:manage',
    ],
];

$competencies = [
    'tassign_competency_competency_index' => [
        'classname'     => \tassign_competency\services\competency::class,
        'methodname'    => 'index',
        'description'   => 'Returns a list of all competencies',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'tassign/competency:view',
    ],

    'tassign_competency_competency_show' => [
        'classname'     => \tassign_competency\services\competency::class,
        'methodname'    => 'show',
        'description'   => 'Returns one competency',
        'type'          => 'read',
        'loginrequired' => true,
        'ajax'          => true,
        'capabilities'  => 'tassign/competency:view',
    ],
];

$functions = array_merge($assignments, $competencies);
