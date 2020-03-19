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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform;

use admin_externalpage;
use context_coursecat;
use moodle_url;

/**
 * The settings in this class are deliberately not part of the usual settings.php.
 *
 * Currently the settings.php file in the plugin is not parsed unless the user
 * has the moodle/site:config or totara/core:modconfig capability.
 *
 * This class is currently read by the admin/settings/perform.php which does not require
 * one of those two capabilities
 *
 * @see TL-24292 for a possible generic way to implement this in the future
 *
 * @package mod_perform
 */
class settings {

    public static function init_public_settings(\admin_root $admin_root) {
        $context = null;
        $system_context = \context_system::instance();
        // If the user has the capability on the system level he should be able to access the pages
        // otherwise we need to check the capability on the category level.
        // We check the system context first as to avoid unintentionally creating the default category.
        // TODO Technically this is not the right way of doing it as there could be an override in the category,
        //      see TL-24324 for more details what happens if the category is
        //      automatically created by util::get_default_category_id();
        //      Find a better solution to have the default category not auto created.
        if (has_capability('mod/perform:view_manage_activities', $system_context)) {
            $context = $system_context;
        } else {
            $category_id = util::get_default_category_id();
            $category_context = context_coursecat::instance($category_id);

            if (has_capability('mod/perform:view_manage_activities', $category_context)) {
                $context = $category_context;
            }
        }

        if ($context) {
            $admin_root->add(
                'performactivities',
                new admin_externalpage(
                    'mod_perform_manage_activities',
                    get_string('menu_title_activity_management', 'mod_perform'),
                    new moodle_url("/mod/perform/manage/activity/index.php"),
                    'mod/perform:view_manage_activities',
                    false,
                    $context
                )
            );
        }
    }

}