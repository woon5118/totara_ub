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
use mod_perform\entities\activity\section_relationship as section_relationship_entity;

/**
 * Class section_relationship
 *
 * A relationship is used to define which participants should participate in a section.
 *
 * @property-read int $id ID
 * @property-read int $section_id
 * @property-read int $activity_relationship_id
 * @property-read bool $can_view
 * @property-read bool $can_answer
 * @property-read section $section
 * @property-read activity_relationship $activity_relationship
 *
 * @package mod_perform\models\activity
 */
class section_relationship extends model {

    protected $entity_attribute_whitelist = [
        'id',
        'section_id',
        'activity_relationship_id',
        'can_view',
        'can_answer',
    ];

    protected $model_accessor_whitelist = [
        'section',
        'activity_relationship',
    ];

    /**
     * @var section_relationship_entity
     */
    protected $entity;

    /**
     * @return string
     */
    protected static function get_entity_class(): string {
        return section_relationship_entity::class;
    }

    /**
     * Create a section relationship
     *
     * @param section $section
     * @param activity_relationship $activity_relationship
     * @return static
     */
    public static function create(section $section, activity_relationship $activity_relationship): self {
        $section_relationship_entity = new section_relationship_entity();
        $section_relationship_entity->section_id = $section->id;
        $section_relationship_entity->activity_relationship_id = $activity_relationship->id;
        // Can view/answer always set to true for now.
        $section_relationship_entity->can_view = 1;
        $section_relationship_entity->can_answer = 1;
        $section_relationship_entity->save();

        return self::load_by_entity($section_relationship_entity);
    }

    /**
     * Delete a section relationship
     *
     * Management of the activity relationship is NOT handled here. These must be manually cleaned up if no longer needed.
     */
    public function delete() {
        $this->entity->delete();
    }

    /**
     * Get the section that this section relationship belongs to
     *
     * @return section
     */
    public function get_section(): section {
        return section::load_by_entity($this->entity->section);
    }

    /**
     * Get the activity relationship which is being linked to the section
     *
     * @return activity_relationship
     */
    public function get_activity_relationship(): activity_relationship {
        return activity_relationship::load_by_entity($this->entity->activity_relationship);
    }

}
