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

use stdClass;
use coding_exception;
use core\orm\collection;
use core\orm\entity\model;
use core\orm\query\builder;
use mod_perform\entities\activity\activity_relationship;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\entities\activity\element as element_entity;


/**
 * Class section
 *
 * A section of an activity, which defines the layout of elements (question) and the participants that can answer them.
 *
 * @property-read int $id ID
 * @property-read int $activity_id
 * @property-read string $title
 * @property-read activity $activity
 * @property-read collection|section_element[] $section_elements
 * @property-read collection|section_relationship[] $section_relationships
 *
 * @package mod_perform\models\activity
 */
class section extends model {

    protected $entity_attribute_whitelist = [
        'id',
        'activity_id',
        'created_at',
        'updated_at',
    ];

    protected $model_accessor_whitelist = [
        'activity',
        'title',
        'section_elements',
        'section_relationships',
        'participant_sections',
        'section_elements_summary',
    ];

    /**
     * @var section_entity
     */
    protected $entity;

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return section_entity::class;
    }

    /**
     * @param activity $activity
     * @param string $title
     *
     * @return static
     */
    public static function create(activity $activity, string $title = ''): self {
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
     * Get the title of this section.
     * If there is no title, then just show what section number this is for the activity.
     *
     * @return string
     */
    public function get_title(): string {
        if (trim($this->entity->title) !== '') {
            return $this->entity->title;
        }

        $sections_before_this_section = section_entity::repository()
            ->where('activity_id', $this->activity_id)
            ->where('id', '<', $this->id)
            ->count();

        return get_string('section_default_name', 'mod_perform', $sections_before_this_section + 1);
    }

    /**
     * Get any array of all section elements in this section, indexed and sorted by sort_order
     *
     * @return section_element[]
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
     * Get a list of all section relationships that this section has
     *
     * @return collection|section_relationship[]
     */
    public function get_section_relationships(): collection {
        return $this->entity->section_relationships->map_to(section_relationship::class);
    }

    /**
     * Get the activity relationships that exist due to this section (not for all sections)
     *
     * @return collection|activity_relationship[]
     */
    public function get_activity_relationships(): collection {
        return $this->entity->activity_relationships;
    }

    /**
     * Get section elements summary
     * @return stdClass
     */
    public function get_section_elements_summary(): stdClass {

        $total_count= section_element_entity::repository()
            ->where('section_id', $this->id)
            ->count();

        $required_count = section_element_entity::repository()
            ->join([element_entity::TABLE, 'element'], 'element_id', 'id')
            ->where('section_id', $this->id)
            ->where('element.is_required', 1)
            ->count();

        $optional_count = section_element_entity::repository()
            ->join([element_entity::TABLE, 'element'], 'element_id', 'id')
            ->where('section_id', $this->id)
            ->where('element.is_required', 0)
            ->count();

        return (object)[
            'required_question_count' => $required_count,
            'optional_question_count' => $optional_count,
            'other_element_count'     => $total_count-($required_count+$optional_count),
        ];
    }

    /**
     * Update section relationships by a list of class names.
     *
     * @param array[] $relationships_updates
     *
     * @return section
     */
    public function update_relationships(array $relationships_updates): self {
        // Figure out which relationships to remove and which to add.

        builder::get_db()->transaction(function () use ($relationships_updates) {
            $existing_section_relationships = $this->get_section_relationships();
            foreach ($relationships_updates as $relationship_update) {
                $core_relationship_id = $relationship_update['id'];

                /** @var section_relationship $section_relationship */
                $section_relationship = $existing_section_relationships->find(
                    function($section_relationship) use ($core_relationship_id) {
                        return $section_relationship->relationship->id === $core_relationship_id;
                    }
                );

                if ($section_relationship) {
                    unset($relationship_update['id']);
                }
                $section_relationship
                    ? $section_relationship->update_can_view($relationship_update['can_view'])
                    : section_relationship::create($this->get_id(), $relationship_update['id'], $relationship_update['can_view']);
            }

            $relationship_ids = array_column($relationships_updates, 'id');
            foreach ($existing_section_relationships as $section_relationship) {
                if (!in_array($section_relationship->relationship->id, $relationship_ids, true)) {
                    section_relationship::delete_with_properties($this->get_id(), $section_relationship->relationship->id);
                }
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
            throw new coding_exception('Section element sort orders are not unique!');
        }

        sort($sort_orders);

        if (reset($sort_orders) != 1 or end($sort_orders) != count($sort_orders)) {
            throw new coding_exception('Section element sort orders are not consecutive starting at 1!');
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
                if ($section_element->section_id != $this->id) {
                    throw new coding_exception('Cannot delete a section element that does not belong to this section');
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
                if ($section_element->section_id != $this->id) {
                    throw new coding_exception('Cannot move a section element that does not belong to this section');
                }
                $section_element->update_sort_order($sort_order);
            }

            $this->validate_sort_orders();
        });
    }

    /**
     * Determine if the given user can response to this section.
     *
     * @param int $user_id
     * @return bool
     */
    public function can_respond(int $user_id): bool {
        return true;
    }
}
