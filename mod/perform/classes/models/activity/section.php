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

use core\orm\entity\model;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\section as section_model;
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

        /** @var section_model $model */
        $model = static::load_by_entity($entity);
        return $model;
    }

    /**
     * get activity
     *
     * @return activity
     */
    public function activity(): activity_model {
        /** @var activity_model $model */
        $model = activity::load_by_entity($this->entity->activity);
        return $model;
    }

    /**
     * get section elements
     *
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
                return $this->activity();
            case 'section_elements':
                return $this->get_section_elements();
            default:
                return parent::__get($name);
        }
    }

    /**
     * @inheritDoc
     */
    public function to_array(): array {
        $result = parent::to_array();
        $result['activity'] = $this->activity();
        $result['section_elements'] = $this->get_section_elements();
        return $result;
    }
}
