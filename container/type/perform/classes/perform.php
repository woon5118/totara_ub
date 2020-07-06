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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @package container_perform
 */

namespace container_perform;

use core\orm\query\builder;
use core_container\{container, facade\category_name_provider};
use mod_perform\models\activity\activity;

/**
 * Container for performance activities
 *
 * While this container was primarily designed to contain a single performance activity, it is possible
 * to extend it to allow multiple performance activities or even other types of activity.
 */
class perform extends container implements category_name_provider {

    /*
     * @const string Default category name string.
     */
    const DEFAULT_CATEGORY_NAME = 'performance-activities';

    /**
     * @param activity $activity
     * @return self
     */
    public static function from_activity(activity $activity): self {
        /** @var self $perform_container */
        $perform_container = static::from_id($activity->course);

        return $perform_container;
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
            $categoryid = static::get_default_category_id();
            if (0 == $categoryid) {
                // Nope, this user is not able to add a performance activity container.
                return false;
            }

            $context = \context_coursecat::instance($categoryid);
        }

        return has_capability('container/perform:create', $context, $userid);
    }

    /**
     * @inheritDoc
     */
    public function get_view_url(): \moodle_url {
        return new \moodle_url('');
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

    /**
     * @inheritDoc
     */
    public static function normalise_data_on_create(\stdClass $data): \stdClass {
        $data = parent::normalise_data_on_create($data);

        // Perform containers do not have formats.
        $data->format = 'none';

        return $data;
    }

    /**
     * Delete all course/container records and the related perform activity and it's children.
     *
     * @see
     */
    public function delete(): void {
        builder::get_db()->transaction(function () {
            // Delete the mod perform specific records first because the context
            // record is required to create the activity deleted event.
            activity::load_by_container_id($this->get_id())->delete();

            parent::delete();
        });
    }

    /**
     * A flag to tell whether the container is belonging to the category where
     * it is not maintain-able by the users. Which means it is only being maintained by the
     * system only and these categories that are holding this container will not be shown
     * to the page.
     *
     * @return bool
     */
    public static function is_using_system_category(): bool {
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function get_container_category_name(): string {
        return self::DEFAULT_CATEGORY_NAME;
    }

}