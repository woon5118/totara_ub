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

/**
 * Common logic across all perform pages.
 *
 * @package mod_perform\controllers
 */
abstract class perform_controller extends controller {

    public function init_page_object() {
        advanced_feature::require('performance_activities');
        parent::init_page_object();
    }

    public function action() {
        // Remove course-related settings blocks.
        $this->get_settings()->remove('categorysettings');
        $this->get_settings()->remove('modulesettings');
        $this->get_settings()->remove('courseadmin');

        // Remove course-related breadcrumbs.
        $this->get_breadcrumbs()->remove('courses');
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
