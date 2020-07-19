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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package core_container
 */
namespace core_container;

use core_container\facade\category_id_number_provider;
use core_container\facade\category_name_provider;

/**
 * A helper to help on creating a default course category.
 */
final class container_category_helper {
    /**
     * Just a helper function to concat the container_type with the parent $category id,
     * as we would want this kind of concat to be the same across the system.
     *
     * @param string $container_type
     * @param int $category_id
     *
     * @return string
     */
    private static function build_id_number(string $container_type, int $category_id): string {
        return "{$container_type}-{$category_id}";
    }

    /**
     * Finding the default category's id given by the container's type and the actor - which is $user_id in this case.
     *
     *
     * @param string        $container_type
     * @param bool          $create_on_missing
     *
     * @param string|null   $id_number          If id_number is not provided, it will be concatenate between the
     *                                          container_type and the parent's id, or if the container class
     *                                          is implementing the interface {@see category_id_number_provider}
     *                                          then the function itself will try to invoke
     *                                          {@see category_id_number_provider::get_container_category_id_number()}
     *
     * @param int|null      $user_id            If it is not provided, user in session will be used.
     *
     * @return int|null
     */
    public static function get_default_category_id(string $container_type, bool $create_on_missing = true,
                                                    ?string $id_number = null, ?int $user_id = null): ?int {
        global $USER, $CFG, $DB;

        if (null === $user_id || 0 === $user_id) {
            $user_id = $USER->id;
        }

        // This is the very top level of course categories.
        $parent_id = 0;

        // Multi-tenancy compatible.
        if (!empty($CFG->tenantsenabled)) {
            // Search for $tenant_id.
            if ($user_id == $USER->id && !empty($USER->tenantid)) {
                $tenant_id = $USER->tenantid;
            } else {
                $tenant_id = $DB->get_field('user', 'tenantid', ['id' => $user_id]);
            }

            if (!empty($tenant_id)) {
                $parent_id = $DB->get_field('tenant', 'categoryid', ['id' => $tenant_id]);
            }
        }

        $class_name = factory::get_container_class($container_type);
        $interfaces = class_implements($class_name);

        if (null === $id_number || '' === $id_number) {
            // Generate the default unique id number for category, based on the category's parent id.
            // And this category should be the one where container is belong to
            $id_number = static::build_id_number($container_type, $parent_id);

            if (in_array(category_id_number_provider::class, $interfaces)) {
                // If the container is implementing interface provide_category_id_number, then it will try to
                // call to the provided API.
                $id_number = call_user_func([$class_name, 'get_container_category_id_number']);
            }
        }

        $params = [
            'idnumber' => $id_number,
            'parent' => $parent_id
        ];

        $category_id = $DB->get_field('course_categories', 'id', $params);
        if (!empty($category_id)) {
            return $category_id;
        }

        if ($create_on_missing) {
            $new_category = static::create_container_category($container_type, $parent_id, $id_number);
            return $new_category->id;
        }

        return null;
    }

    /**
     * The function is for create a new record of table {course_categories} with provided parameters.
     *
     * If the parameter $name is not provided, it will try to look it up the container type whether
     * the container class is implement the interface {@see category_name_provider}. If it is, then it will
     * try to invoke {@see category_name_provider::get_container_category_name()}
     *
     * If the parameter $id_number is not provided, it will try to look it up the container type whether
     * the container class is implement the interface {@see category_id_number_provider}. If it is, then
     * it will try to invoke {@see category_id_number_provider::get_container_category_id_number()}
     *
     * @param string        $container_type
     * @param int           $parent_category_id
     * @param string|null   $id_number
     * @param string|null   $name
     *
     * @return \coursecat
     */
    public static function create_container_category(string $container_type, int $parent_category_id,
                                                     string $id_number = null, ?string $name = null): \coursecat {
        $class_name = factory::get_container_class($container_type);
        $interfaces = class_implements($class_name);

        // Finding the name for the category.
        if (null === $name || '' === $name) {
            if (in_array(category_name_provider::class, $interfaces)) {
                $name = call_user_func([$class_name, 'get_container_category_name']);
            } else {
                // Otheriwse just use the default language string for the name.
                $name = get_string('default_category', 'core_container');
            }
        }

        // Find the id number for the category.
        if (null === $id_number || '' === $id_number) {
            $id_number = static::build_id_number($container_type, $parent_category_id);

            if (in_array(category_id_number_provider::class, $interfaces)) {
                $id_number = call_user_func([$class_name, 'get_container_category_id_number']);
            }
        }

        $is_system = call_user_func([$class_name, 'is_using_system_category']);

        $record = new \stdClass();
        $record->name = $name;
        $record->idnumber = $id_number;
        $record->parent = $parent_category_id;
        $record->timemodified = time();
        $record->issystem = $is_system ? 1 : 0;

        return \coursecat::create($record);
    }
}