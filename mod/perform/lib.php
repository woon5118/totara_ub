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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package mod_perform
 */

use core_user\output\myprofile\node;
use core_user\output\myprofile\tree;
use mod_perform\controllers\activity\user_activities;

/**
 * Required in order to prevent failures in tests.
 */
function perform_add_instance($data) {
    return null;
}

function perform_update_instance($data) {
    return true;
}

function perform_delete_instance($id) {
    return true;
}

/**
 * Add user performance activity list link to the user profile page
 *
 * @param tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $this_user
 * @return bool
 * @throws coding_exception
 * @throws moodle_exception
 */
function mod_perform_myprofile_navigation(tree $tree, $user, $is_current_user) {
    // You can only view your own performance activities for now.
    if (!$is_current_user) {
        return false;
    }

    $tree->add_node(
        new node(
            'miscellaneous',
            'performance_activities',
            get_string('user_activities:page_title', 'mod_perform'),
            null,
            user_activities::get_url()
        )
    );

    return true;
}