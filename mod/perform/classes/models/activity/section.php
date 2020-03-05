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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use core\orm\collection;
use core\orm\entity\model;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\section_element as section_element_model;

/**
 * Class section
 *
 * This class contains the methods related to performance activity section
 * All the activity section entity properties accessible via this class
 *
 * @package mod_perform\models\activity
 */
class section extends model {

    /**
     * @var section_entity
     */
    protected $entity;

    /**
     * @inheritDoc
     */
    public static function get_entity_class(): string {
        return section_entity::class;
    }

    /**
     * @param activity $activity
     * @param string   $title
     *
     * @return static
     */
    public static function create(activity_model $activity, string $title): self {
        $entity = new section_entity();
        $entity->activity_id = $activity->id;
        $entity->title = $title;
        $entity->save();

        return self::load_by_entity($entity);
    }

    /**
     * @return activity_model
     */
    public function get_activity(): activity_model {
        /** @var activity_model $model */
        $model = activity::load_by_entity($this->entity->activity);
        return $model;
    }

    /**
     * @return array
     */
    public function get_section_elements(): array {
        $section_element_models = [];

        foreach ($this->entity->section_elements as $section_element_entity) {
            $section_element_models[] = section_element_model::load_by_entity($section_element_entity);
        }

        return $section_element_models;
    }

    /**
     * @inheritDoc
     */
    public function __get($name) {
        switch ($name) {
            case 'activity':
                return $this->get_activity();
            case 'section_elements':
                return $this->get_section_elements();
            case 'section_relationships':
                return $this->get_section_relationships();
            default:
                return parent::__get($name);
        }
    }

    /**
     * @inheritDoc
     */
    public function to_array(): array {
        $result = parent::to_array();
        $result['activity'] = $this->get_activity();
        $result['section_elements'] = $this->get_section_elements();
        return $result;
    }

    /**
     * @return collection
     */
    public function get_activity_relationships(): collection {
        return $this->entity->activity_relationships;
    }

    /**
     * @return array
     */
    public function get_section_relationships(): array {
        return $this->entity->section_relationships->map(function (section_relationship_entity $section_relationship) {
            return section_relationship::load_by_entity($section_relationship);
        })->all();
    }

    /**
     * Update section relationships by a list of class names.
     *
     * @param array $update_relationships_class_names
     * @return section
     */
    public function update_relationships(array $update_relationships_class_names): self {
        global $DB;

        // Figure out which relationships to remove and which to add.
        $current_relationships_class_names = $this->get_activity_relationships()->pluck('class_name');
        $to_create = [];
        $to_delete = [];
        foreach ($update_relationships_class_names as $class_name) {
            if (!in_array($class_name, $current_relationships_class_names)) {
                $to_create[] = $class_name;
            }
        }
        foreach ($current_relationships_class_names as $class_name) {
            if (!in_array($class_name, $update_relationships_class_names)) {
                $to_delete[] = $class_name;
            }
        }

        $DB->transaction(function () use ($to_create, $to_delete) {
            foreach ($to_create as $create_class_name) {
                section_relationship::create($this->get_id(),  $create_class_name);
            }
            foreach ($to_delete as $delete_class_name) {
                section_relationship::delete($this->get_id(), $delete_class_name);
            }
        });

        // Refresh entity cache.
        $this->entity->load_relation('activity_relationships');
        $this->entity->load_relation('section_relationships');
        return $this;
    }
}
