<?php
/**
 * This file is part of Totara Core
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package mod_perform
 */

namespace container_perform;

use coding_exception;
use context_system;
use core\orm\query\builder;
use coursecat;
use totara_competency\entity\course_categories;

final class perform_helper {

    /**
     * Remove all existing instances of perform container
     */
    public static function delete_all(): void {
        if (!has_capability('moodle/site:config', context_system::instance())) {
            throw new coding_exception('User capability to uninstall perform containers should have been checked before reaching this point!');
        }

        $category_ids = course_categories::repository()
            ->select('id')
            ->where('name', perform::get_container_category_name())
            ->get()
            ->pluck('id');

        $coursecats = coursecat::get_many($category_ids);

        builder::get_db()->transaction(function () use ($coursecats) {
            foreach ($coursecats as $coursecat) {
                // We don't delete the categories themselves since we may need them again if mod_perform is reinstalled.
                $coursecat->delete_courses_and_containers();
            }
        });
    }

}