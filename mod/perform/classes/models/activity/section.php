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

use coding_exception;
use core\orm\collection;
use core\orm\entity\model;
use core\orm\query\builder;
use mod_perform\entities\activity\element as element_entity;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\section_element as section_element_entity;
use stdClass;

/**
 * Class section
 *
 * A section of an activity, which defines the layout of elements (question) and the participants that can answer them.
 *
 * @property-read int $id ID
 * @property-read int $activity_id
 * @property-read string $title
 * @property-read string $display_title
 * @property-read activity $activity
 * @property-read int $sort_order
 * @property-read collection|section_element[] $section_elements
 * @property-read collection|section_relationship[] $section_relationships
 *
 * @package mod_perform\models\activity
 */
class section extends model {

    protected $entity_attribute_whitelist = [
        'id',
        'activity_id',
        'title',
        'sort_order',
        'created_at',
        'updated_at',
    ];

    protected $model_accessor_whitelist = [
        'activity',
        'display_title',
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
     * Creates a new section
     *
     * @param activity $activity
     * @param string $title
     * @param int|null $sort_order if order is 0 or null the section will be added at the end
     * @return static
     */
    public static function create(activity $activity, string $title = '', ?int $sort_order = null): self {
        $is_last_section = $sort_order <= 0;
        $new_sort_order = self::get_new_sort_order($activity);

        // Just making sure we are keeping the sequence
        if (empty($sort_order) || $sort_order > $new_sort_order) {
            $sort_order = $new_sort_order;
        }

        $entity = new section_entity();
        $entity->activity_id = $activity->id;
        $entity->title = $title;
        $entity->sort_order = $sort_order;
        $entity->save();

        if (!$is_last_section && $sort_order !== $new_sort_order) {
            self::update_sort_order($entity);
        }

        return self::load_by_entity($entity);
    }

    /**
     * Get new sort order if it's a new section added at the end
     *
     * @param activity $activity
     * @return int
     */
    protected static function get_new_sort_order(activity $activity): int {
        return section_entity::repository()
            ->where('activity_id', $activity->id)
            ->count() + 1;
    }

    /**
     * Update the sort order of all sections coming after the given one
     *
     * @param section_entity $section_entity
     * @return void
     */
    protected static function update_sort_order(section_entity $section_entity): void {
        $sql = "
            UPDATE {perform_section}
            SET sort_order = sort_order + 1
            WHERE activity_id = :activity_id
                AND id != :section_id
                AND sort_order >= :sort_order
        ";

        $params = [
            'activity_id' => $section_entity->activity_id,
            'section_id' => $section_entity->id,
            'sort_order' => $section_entity->sort_order,
        ];

        builder::get_db()->execute($sql, $params);
    }

    /**
     * @return activity
     */
    public function get_activity(): activity {
        return activity::load_by_entity($this->entity->activity);
    }

    /**
     * Get the title of this section.
     * If there is no title, then just show a default placeholder string.
     *
     * @return string
     */
    public function get_display_title(): string {
        if (isset($this->entity->title) && trim($this->entity->title) !== '') {
            return $this->entity->title;
        }

        return get_string('untitled_section', 'mod_perform');
    }

    /**
     * Get any array of all section elements in this section, indexed and sorted by sort_order
     *
     * @return collection|section_element[]
     */
    public function get_section_elements(): collection {
        $section_element_models = [];

        foreach ($this->entity->section_elements as $section_element_entity) {
            $section_element_models[$section_element_entity->sort_order] =
                section_element::load_by_entity($section_element_entity);
        }

        ksort($section_element_models);

        return new collection($section_element_models);
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
     * Get section elements summary
     *
     * @return stdClass
     */
    public function get_section_elements_summary(): stdClass {

        $total_count = section_element_entity::repository()
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
            'other_element_count'     => $total_count - ($required_count + $optional_count),
        ];
    }

    /**
     * Update the title of this section.
     *
     * @param string $title
     * @return $this
     */
    public function update_title(string $title): self {
        $this->entity->title = $title;
        $this->entity->save();
        return $this;
    }

    /**
     * Update section relationships by a list of class names.
     *
     * @param array[] $relationship_updates
     *
     * @return section
     */
    public function update_relationships(array $relationship_updates): self {
        // Figure out which relationships to remove and which to add.

        builder::get_db()->transaction(function () use ($relationship_updates) {
            $existing_section_relationships = $this->get_section_relationships();
            foreach ($relationship_updates as $relationship_update) {
                $core_relationship_id = $relationship_update['core_relationship_id'];

                /** @var section_relationship $section_relationship */
                $section_relationship = $existing_section_relationships->find(
                    function ($section_relationship) use ($core_relationship_id) {
                        return $section_relationship->core_relationship_id === $core_relationship_id;
                    }
                );

                if ($section_relationship) {
                    unset($relationship_update['core_relationship_id']);
                }
                $section_relationship
                    ? $section_relationship->update_can_view($relationship_update['can_view'])
                    : section_relationship::create(
                        $this->get_id(),
                        $relationship_update['core_relationship_id'],
                        $relationship_update['can_view']
                    );
            }

            $relationship_ids = array_column($relationship_updates, 'core_relationship_id');
            foreach ($existing_section_relationships as $section_relationship) {
                if (!in_array($section_relationship->core_relationship_id, $relationship_ids)) {
                    section_relationship::delete_with_properties($this->get_id(), $section_relationship->core_relationship_id);
                }
            }
        });

        // Refresh entity cache.
        $this->entity->load_relation('section_relationships');
        return $this;
    }

    /**
     * Check if the sort orders on the section elements are valid and throw an exception if not
     *
     * @throws coding_exception when the ordering is not valid
     */
    private function validate_sort_orders(): void {
        $section_elements = $this->get_section_elements();

        // If there are no items then sorting can't be invalid.
        if ($section_elements->count() < 1) {
            return;
        }

        $sort_orders = array_unique($section_elements->pluck('sort_order'));

        if (count($sort_orders) != count($section_elements)) {
            throw new coding_exception('Section element sort orders are not unique!');
        }

        sort($sort_orders);

        if (reset($sort_orders) != 1 || end($sort_orders) != count($sort_orders)) {
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

    /**
     * Delete the section
     */
    public function delete(): void {
        $this->entity->delete();

        // Make sure the sort orders of the sections following the deleted one get recalculated
        self::recalculate_sort_order($this->entity->activity_id);
    }

    /**
     * Recalculate sort_order for given activity
     *
     * @param int $activity_id
     */
    protected static function recalculate_sort_order(int $activity_id): void {
        builder::get_db()->transaction(function () use ($activity_id) {
            $sections = section_entity::repository()
                ->where('activity_id', $activity_id)
                ->order_by('sort_order')
                ->order_by('id')
                ->get_lazy();

            $sort_order = 1;
            foreach ($sections as $section) {
                if ($section->sort_order != $sort_order) {
                    $section->sort_order = $sort_order;
                    $section->save();
                }
                $sort_order++;
            }
        });
    }

}
