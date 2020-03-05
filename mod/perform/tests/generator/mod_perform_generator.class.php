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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

use container_perform\perform as perform_container;
use core_container\module\module;
use mod_perform\models\activity\section_relationship as section_relationship_model;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\section;
use mod_perform\models\activity\element;
use mod_perform\models\activity\section_element;

/**
 * Perform generator
 */
class mod_perform_generator extends component_generator_base {

    /**
     * Create a performance activity and a performance container to contain it
     *
     * @param array $data
     * @return activity
     */
    public function create_activity_in_container($data = []): activity {
        global $DB;

        $container_data = new stdClass();
        $container_data->name = $data['container_name'] ?? "test performance container";
        $container_data->category = \mod_perform\util::get_default_categoryid();

        return $DB->transaction(function () use ($data, $container_data) {
            $container = perform_container::create($container_data);

            // Create a performance activity inside the new performance container.
            $activity_data = new \stdClass();
            $name = $data['activity_name'] ?? "test performance activity";
            $description = $data['description'] ?? "test description";
            $status = $data['activity_status'] ?? activity::STATUS_ACTIVE;

            /** @var perform_container $container */
            $activity = activity::create($container, $name, $description, $status);

            return activity::load_by_id($activity->get_id());
        });
    }

    /**
     * Creates only a performance activity module
     *
     * This function is required by module generators.
     *
     * @param array $data
     * @return module
     */
    public function create_instance($data = []): module {
        $name = $data['name'] ?? "test performance activity";
        $description = $data['description'] ?? "test description";
        $status = $data['status'] ?? activity::STATUS_ACTIVE;

        $container = perform_container::from_id($data['course']);

        /** @var perform_container $container */
        activity::create($container, $name, $description, $status);

        $modules = $container->get_section(0)->get_all_modules();
        $module = reset($modules);

        return $module;
    }

    public function create_section(activity $activity, $data = []): section {
        $title =  $data['title'] ?? "test Section";
        return section::create($activity, $title);
    }

    public function create_section_element(section $section, element $element) {
        return section_element::create($section, $element);
    }

    public function create_element($data = []) {
        $title = $data['title'] ?? "test title";
        $plugin_name = $data['plugin_name'] ?? "short_text";
        $identifier = $data['identifier'] ?? 1;

        return element::create($plugin_name, $title, $identifier);
    }

    public function create_section_relationship(section $section, array $data): section_relationship_model {
        return section_relationship_model::create($section->get_id(), $data['class_name']);
    }
}