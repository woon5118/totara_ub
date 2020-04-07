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
use mod_perform\entities\activity\activity_relationship as activity_relationship_entity;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use totara_core\relationship\relationship;

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
 * @property-read relationship $relationship
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
        'relationship',
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
     * @param int $section_id
     * @param int $relationship_id
     * @return static
     */
    public static function create(int $section_id, int $relationship_id): self {
        global $DB;

        $relationship = relationship::load_by_id($relationship_id);
        $section = section::load_by_id($section_id);
        $activity = activity::load_by_id($section->activity_id);
        require_capability('mod/perform:manage_activity', $activity->get_context());

        return $DB->transaction(function () use ($section_id, $relationship, $activity) {
            $relationship_entity = activity_relationship_entity::repository()
                ->where('activity_id', $activity->get_id())
                ->where('core_relationship_id', $relationship->id)
                ->get()
                ->first();
            // Create relationship record only if it doesn't exist already.
            if (!$relationship_entity) {
                $relationship_entity = new activity_relationship_entity();
                $relationship_entity->activity_id = $activity->get_id();
                $relationship_entity->core_relationship_id = $relationship->id;
                $relationship_entity->save();
            }
            $section_relationship_entity = section_relationship_entity::repository()
                ->where('section_id', $section_id)
                ->where('activity_relationship_id', $relationship_entity->id)
                ->get()
                ->first();
            // Create section_relationship record only if it doesn't exist already.
            if (!$section_relationship_entity) {
                $section_relationship_entity = new section_relationship_entity();
                $section_relationship_entity->section_id = $section_id;
                $section_relationship_entity->activity_relationship_id = $relationship_entity->id;
                // Can view/answer always set to true for now.
                $section_relationship_entity->can_view = 1;
                $section_relationship_entity->can_answer = 1;
                $section_relationship_entity->save();
            }
            return self::load_by_entity($section_relationship_entity);
        });
    }

    /**
     * Delete a section relationship
     *
     * @param int $section_id
     * @param int $relationship_id
     * @return bool
     */
    public static function delete_with_properties(int $section_id, int $relationship_id): bool {
        global $DB;

        $relationship = relationship::load_by_id($relationship_id);
        $section = section::load_by_id($section_id);
        $activity = activity::load_by_id($section->activity_id);
        require_capability('mod/perform:manage_activity', $activity->get_context());

        /** @var activity_relationship_entity $activity_relationship_entity */
        $activity_relationship_entity = activity_relationship_entity::repository()
            ->where('activity_id', $activity->get_id())
            ->where('core_relationship_id', $relationship->id)
            ->get()
            ->first();
        if (!$activity_relationship_entity) {
            // Nothing to delete.
            return false;
        }
        /** @var section_relationship_entity $section_relationship_entity */
        $section_relationship_entity = section_relationship_entity::repository()
            ->where('section_id', $section_id)
            ->where('activity_relationship_id', $activity_relationship_entity->id)
            ->get()
            ->first();
        if (!$section_relationship_entity) {
            // This should never happen.
            throw new \invalid_state_exception(
                "Record found in perform_relationship without corresponding section_relationship record. "
                . "section_id {$section_id}, activity_relationship_id {$activity_relationship_entity->id}"
            );
        }

        return $DB->transaction(function () use ($activity_relationship_entity, $section_relationship_entity) {
            $section_relationship_entity->delete();

            // Delete relationship record only if it's not in any other section.
            if (!section_relationship_entity::repository()
                ->where('activity_relationship_id', $activity_relationship_entity->id)
                ->exists()) {
                $activity_relationship_entity->delete();
            }
            return true;
        });
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
     * @return relationship
     */
    public function get_relationship(): relationship {
        return relationship::load_by_entity($this->entity->activity_relationship->relationship);
    }

}
