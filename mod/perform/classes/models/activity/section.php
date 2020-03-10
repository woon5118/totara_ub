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

/**
 * Class section
 *
 * This class contains the methods related to performance activity section
 * All the activity section entity properties accessible via this class
 *
 * @property-read int $id ID
 * @property-read string $title
 * @property-read activity $activity
 * @property-read section_element[] $section_elements
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
    public static function create(activity $activity, string $title): self {
        $entity = new section_entity();
        $entity->activity_id = $activity->id;
        $entity->title = $title;
        $entity->save();

        return self::load_by_entity($entity);
    }

    /**
     * @return activity
     */
    public function get_activity(): activity {
        return activity::load_by_entity($this->entity->activity);
    }

    /**
     * Get any array of all section elements in this section, indexed and sorted by sort_order
     *
     * @return array
     */
    public function get_section_elements(): array {
        $section_element_models = [];

        foreach ($this->entity->section_elements as $section_element_entity) {
            $section_element_models[$section_element_entity->sort_order] =
                section_element::load_by_entity($section_element_entity);
        }

        ksort($section_element_models);

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
<<<<<<< Updated upstream
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
                section_relationship::delete_with_properties($this->get_id(), $delete_class_name);
            }
        });

        // Refresh entity cache.
        $this->entity->load_relation('activity_relationships');
        $this->entity->load_relation('section_relationships');
        return $this;
    }

    /**
     * Check if the sort orders on the section elements are valid and throw an exception if not
     *
     * @throws \coding_exception when the ordering is not valid
     */
    private function validate_sort_orders(): void {
        $section_elements = $this->get_section_elements();

        // If there are no items then sorting can't be invalid.
        if (empty($section_elements)) {
            return;
        }

        $sort_orders = array_unique(array_column($section_elements, 'sort_order'));

        if (count($sort_orders) != count($section_elements)) {
            throw new \coding_exception('Section element sort orders are not unique!');
        }

        sort($sort_orders);

        if (reset($sort_orders) != 1 or end($sort_orders) != count($sort_orders)) {
            throw new \coding_exception('Section element sort orders are not consecutive starting at 1!');
        }
    }

    /**
     * Add the given element to this section
     *
     * Note that the element will be added at the end of the list of existing elements. To position it elsewhere,
     * move the element after adding it.
     *
     * @param element $element
     * @return section_element
     */
    public function add_element(element $element): section_element {
        $section_element = section_element::create(
            $this,
            $element,
            count($this->get_section_elements()) + 1
        );

        return $section_element;
    }

    /**
     * Remove the given section elements from this section
     *
     * Will automatically re-order all remaining section elements.
     *
     * @param section_element[] $remove_section_elements
     */
    public function remove_section_elements(array $remove_section_elements): void {
        global $DB;

        if (empty($remove_section_elements)) {
            return;
        }

        $DB->transaction(function () use ($remove_section_elements) {
            foreach ($remove_section_elements as $section_element) {
                if ($section_element->get_section()->id != $this->id) {
                    throw new \coding_exception('Cannot delete a section element that does not belong to this section');
                }
                $section_element->delete();
            }

            // Reorder the remaining section elements.
            $section_elements = $this->get_section_elements();

            $i = 0;
            foreach ($section_elements as $section_element) {
                $i++;
                if ($section_element->sort_order != $i) {
                    $section_element->update_sort_order($i);
                }
            }

            // No need to validate sort orders because we've just resorted everything.
        });
    }

    /**
     * Move the specified set of section elements
     *
     * Will fail if the resulting sorting is not valid (all unique and sequential from 1).
     *
     * @param section_element[] $move_section_elements where $key is the new sort order and $value is the section element
     */
    public function move_section_elements(array $move_section_elements): void {
        global $DB;

        if (empty($move_section_elements)) {
            return;
        }

        $DB->transaction(function () use ($move_section_elements) {
            foreach ($move_section_elements as $sort_order => $section_element) {
                if ($section_element->get_section()->id != $this->id) {
                    throw new \coding_exception('Cannot move a section element that does not belong to this section');
                }
                $section_element->update_sort_order($sort_order);
            }

            $this->validate_sort_orders();
        });
    }
}
