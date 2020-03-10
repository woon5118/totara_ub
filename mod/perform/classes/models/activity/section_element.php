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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use core\orm\entity\model;
use mod_perform\entities\activity\section_element as section_element_entity;

/**
 * Class section_element
 *
 * This class contains the methods related to performance activity section element
 * All the activity section element entity properties accessible via this class
 *
 * @property-read int $id ID
 * @property-read section $section immutable
 * @property-read element $element immutable
 * @property-read int $sort_order
 * @package mod_perform\models\activity
 */
class section_element extends model {

    /**
     * @var section_element_entity
     */
    protected $entity;

    /**
     * @inheritDoc
     */
    public static function get_entity_class(): string {
        return section_element_entity::class;
    }

    /**
     * @param section $section
     * @param element $element
     * @param int $sort_order
     *
     * @return static
     */
    public static function create(section $section, element $element, int $sort_order = 1): self {
        $entity = new section_element_entity();
        $entity->section_id = $section->id;
        $entity->element_id = $element->id;
        $entity->sort_order = $sort_order;
        $entity->save();

        return static::load_by_entity($entity);
    }

    /**
     * get section
     *
     * @return section
     */
    public function get_section(): section {
        $section = section::load_by_entity($this->entity->section);
        return $section;
    }

    /**
     * get Element
     *
     * @return element
     */
    public function get_element(): element {
        $element = element::load_by_entity($this->entity->element);
        return $element;
    }

    /**
     * @inheritDoc
     */
    public function __get($name) {
        switch ($name) {
            case 'section':
                return $this->get_section();
            case 'element':
                return $this->get_element();
            default:
                return parent::__get($name);
        }
    }

    /**
     * @inheritDoc
     */
    public function to_array(): array {
        $result = parent::to_array();
        $result['section'] = $this->get_section();
        $result['element'] = $this->get_element();
        return $result;
    }

    public function update_sort_order(int $sort_order) {
        $this->entity->sort_order = $sort_order;
        $this->entity->save();
    }

    /**
     * Delete the section element link
     *
     * This does not automatically delete the element.
     */
    public function delete(): void {
        $this->entity->delete();
    }
}
