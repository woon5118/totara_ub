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
use mod_perform\entities\activity\activity_relationship as activity_relationship_entity;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;

/**
 * Class activity_relationship
 *
 * A relationship defines collections of participants (as opposed to subjects) who will be included in an activity.
 *
 * @property-read int $id ID
 * @property-read int $activity_id
 * @property-read string $class_name Name of relationship class.
 * @property-read int $created_at
 * @property-read activity $activity
 * @property-read collection|section_relationship[] $section_relationships
 *
 * @package mod_perform\models\activity
 */
class activity_relationship extends model {

    protected $entity_attribute_whitelist = [
        'id',
        'activity_id',
        'class_name',
        'created_at',
    ];

    protected $model_accessor_whitelist = [
        'activity',
        'section_relationships',
    ];

    /**
     * @var activity_relationship_entity
     */
    protected $entity;

    /**
     * @return string
     */
    protected static function get_entity_class(): string {
        return activity_relationship_entity::class;
    }

    // TODO: Change this when we have relationship classes.
    private static function get_all_class_names(): array {
        return [
            'subject',
            'manager',
            'appraiser',
        ];
    }

    /**
     * Create an activity relationship
     *
     * TODO replace class_name with relationship_id when we build a generic relationship component.
     *
     * @param activity $activity
     * @param string $class_name
     * @return static
     */
    public static function create_with_class_name(activity $activity, string $class_name): self {
        if (!in_array($class_name, self::get_all_class_names())) {
            throw new \coding_exception("Invalid class_name: {$class_name}");
        }

        $entity = new activity_relationship_entity();
        $entity->activity_id = $activity->get_id();
        $entity->class_name = $class_name;
        $entity->save();

        return self::load_by_entity($entity);
    }

    /**
     * Try to find the activity relationship for the given values
     *
     * If not found, this function returns null.
     *
     * TODO replace class_name with relationship_id when we build a generic relationship component.
     *
     * @param activity $activity
     * @param string $class_name
     * @return activity_relationship|null
     */
    public static function find_with_class_name(activity $activity, string $class_name): ?self {
        if (!in_array($class_name, self::get_all_class_names())) {
            throw new \coding_exception("Invalid class_name: {$class_name}");
        }

        $entity = activity_relationship_entity::repository()
            ->where('activity_id', $activity->id)
            ->where('class_name', $class_name)
            ->get()
            ->first();

        if (!empty($entity)) {
            return self::load_by_entity($entity);
        } else {
            return null;
        }
    }

    /**
     * Delete an activity relationship
     *
     * This checks to make sure that the relationship is not still in use, and will otherwise fail.
     */
    public function delete() {
        if ($this->has_section_relationships()) {
            throw new \coding_exception('Cannot delete activity relationship because it is still in use');
        }

        $this->entity->delete();
    }

    /**
     * Get the activity that this activity relationship belongs to
     *
     * @return activity
     */
    public function get_activity(): activity {
        return activity::load_by_entity($this->entity->activity);
    }

    /**
     * Get a list of all section relationships that use this activity relationship
     *
     * @return collection|section_relationship[]
     */
    public function get_section_relationships(): collection {
        return $this->entity->section_relationships->map(function (section_relationship_entity $section_relationship_entity) {
            return section_relationship::load_by_entity($section_relationship_entity);
        });
    }

    /**
     * Determine if this activity relationship has any section relationships
     *
     * @return bool
     */
    public function has_section_relationships(): bool {
        return count($this->entity->section_relationships) > 0;
    }
}
