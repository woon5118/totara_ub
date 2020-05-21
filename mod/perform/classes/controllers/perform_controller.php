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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers;

use moodle_url;
use navigation_node_collection;
use totara_core\advanced_feature;
use totara_mvc\controller;
use totara_mvc\view;

/**
 * Common logic across all perform pages.
 *
 * @package mod_perform\controllers
 */
abstract class perform_controller extends controller {

    /**
     * Checks and call require_login if parameter is set, can be overridden if special set up is needed
     *
     * @return $this
     */
    protected function authorize() {
        // We do not want to redirect due to not being enrolled
        // we cannot prevent this when passing the course.
        // In this case we do a normal require_login first to capture
        // generic errors, like not being logged in, etc.
        require_login(null, $this->auto_login_guest);

        // Then we'll do a second require_login to capture errors due to not being able to access the course.
        // This i quite a hacky approach but the only way at the moment
        [$context, $course, $cm] = get_context_info_array($this->context->id);
        if ($course) {
            try {
                require_login($course, $this->auto_login_guest, $cm, true, true);
            } catch (\require_login_exception $exception) {
                $this->page->set_cm($cm, $course);
                echo $this->action_invalid()->render();
                exit();
            }
        }

        return $this;
    }

    public function init_page_object() {
        advanced_feature::require('performance_activities');
        parent::init_page_object();
    }

    public function action() {
        $this->remove_breadcrumbs();
    }

    protected function remove_breadcrumbs() {
        // Remove course-related settings blocks.
        $this->get_settings()->remove('categorysettings');
        $this->get_settings()->remove('modulesettings');
        $this->get_settings()->remove('courseadmin');

        // Remove course-related breadcrumbs.
        $this->get_breadcrumbs()->remove('courses');
    }

    /**
     * @return view
     * @throws \coding_exception
     */
    public function action_invalid() {
        $this->remove_breadcrumbs();
        $notification = view::core_renderer()->notification(get_string('error_access_permission_missing', 'mod_perform'), 'error');
        return view::create(null, $notification);
    }

    /**
     * Collection of the settings navigation blocks to be displayed.
     *
     * @return navigation_node_collection
     */
    private function get_settings(): navigation_node_collection {
        return $this->page->settingsnav->children;
    }

    /**
     * Collection of the breadcrumb navigation nodes to be displayed.
     *
     * @return navigation_node_collection
     */
    private function get_breadcrumbs(): navigation_node_collection {
        return $this->page->navigation->children;
    }

    /**
     * The base URL for this page.
     *
     * @return string
     */
    abstract public static function get_base_url(): string;

    /**
     * The URL for this page, with params.
     *
     * @param array $params
     * @return moodle_url
     */
    final public static function get_url(array $params = []): moodle_url {
        return new moodle_url(static::get_base_url(), $params);
    }

}
