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
 * @package container_perform
 */

namespace container_perform;

use core_container\{category, container};

/**
 * Container for performance activities
 *
 * While this container was primarily designed to contain a single performance activity, it is possible
 * to extend it to allow multiple performance activities or even other types of activity.
 */
class perform extends container {
    /*
     * @const string Default category name string.
     */
    const DEFAULT_CATEGORY_NAME = 'performance-activities';

    /**
     * @inheritDoc
     */
    public static function get_type(): string {
        return 'perform';
    }

    /**
     * @inheritDoc
     */
    public static function get_container_category(): string {
        return category::PERFORM;
    }

    /**
     * @inheritDoc
     */
    public static function can_create_instance(int $userid = null, \context_coursecat $context = null): bool {
        global $USER;

        if (null == $userid) {
            // Including zero check
            $userid = $USER->id;
        }

        if (null == $context) {
            $categoryid = static::get_default_categoryid();
            if (0 == $categoryid) {
                // Nope, this user is not able to add a performance activity container.
                return false;
            }

            $context = \context_coursecat::instance($categoryid);
        }

        return has_capability('container/perform:create', $context, $userid);
    }

    /**
     * Get the default category for performance activities.
     * If multi tenancy is turned on and the current user is part of a tenant
     * it will get the category of the tenant.
     *
     * If the category does not exist yet it will automatically create it.
     *
     * @return int
     */
    public static function get_default_categoryid(): int {
        global $CFG, $DB, $USER;
        require_once("{$CFG->dirroot}/totara/core/lib.php");

        // Default to top level as parent category
        $parent_category_id = 0;
        if (!empty($CFG->tenantsenabled) && !empty($USER->tenantid)) {
            // If multi-tenancy in use and current user is in tenant find top level tenant category instead.
            $parent_category_id = $DB->get_field('tenant', 'categoryid', ['id' => $USER->tenantid]) ?? 0;
        }

        $perform_category_id = $DB->get_field(
            'course_categories',
            'id',
            ['parent' => $parent_category_id, 'name' => self::DEFAULT_CATEGORY_NAME]
        );

        // If there is a category for performance activities already, return the ID.
        if (!empty($perform_category_id)) {
            return $perform_category_id;
        }

        // Otherwise attempt to create one.
        return self::create_default_categoryid($parent_category_id);
    }

    /**
     * @param int $parent_category_id
     * @return int
     * @throws \moodle_exception
     */
    protected static function create_default_categoryid(int $parent_category_id) {
        // No capability check as this is system behaviour to ensure category exists.
        $new_category = \coursecat::create(['name' => self::DEFAULT_CATEGORY_NAME, 'parent' => $parent_category_id]);
        return $new_category->id;
    }

    /**
     * @inheritDoc
     */
    public function get_view_url(): \moodle_url {
        return null;
    }

    /**
     * Calculate a new shortname that has not yet been used by any container.
     *
     * @param string $name Name of activity, used to help differentiate potential name.
     * @return string Shortname to use.
     * @throws \dml_exception
     */
    protected static function get_unique_shortname(string $name): string {
        global $DB;

        $possible_shortname = sha1($name . microtime());

        if ($DB->record_exists('course', ['shortname' => $possible_shortname])) {
            // SHA1 collision! Pause for long enough that microtime() will change and
            // try again.
            usleep(1);
            return self::get_unique_shortname($name);
        }

        return $possible_shortname;
    }

    /**
     * @inheritDoc
     */
    protected static function pre_create(\stdClass $data): void {
        parent::pre_create($data);

        // Shortname is not relevant to performance containers, just generate a unique one.
        $name = $data->name ?? '';
        $data->shortname = self::get_unique_shortname($name);

        // TODO will be able to remove this once it's been implemented in parent method.
        $data->containertype = self::get_type();
    }
}