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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

use core_container\hook\module_supported_in_container;
use mod_perform\watcher\activity;
use mod_perform\hook\subject_instances_created;
use mod_perform\watcher\participant_instances;
use mod_perform\watcher\participant_sections;
use mod_perform\hook\participant_instances_created;
use core_user\hook\allow_view_profile_field;
use mod_perform\watcher\notification;
use mod_perform\watcher\user;

$watchers = [
    [
        'hookname' => module_supported_in_container::class,
        'callback' => [activity::class, 'filter_module'],
    ],
    [
        'hookname' => subject_instances_created::class,
        'callback' => [participant_instances::class, 'create_participants'],
    ],
    [
        'hookname' => participant_instances_created::class,
        'callback' => [participant_sections::class, 'create_participant_sections'],
    ],
    [
        'hookname' => allow_view_profile_field::class,
        'callback' => [user::class, 'allow_view_profile_field'],
    ],
    [
        'hookname' => subject_instances_created::class,
        'callback' => [notification::class, 'create_subject_instances'],
    ],
];
