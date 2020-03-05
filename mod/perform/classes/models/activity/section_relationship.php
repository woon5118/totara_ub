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
use mod_perform\entities\activity\section;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\section as section_model;

class section_relationship extends model {

    /**
     * @var section_relationship_entity
     */
    protected $entity;

    /**
     * @return string
     */
    public static function get_entity_class(): string {
        return section_relationship_entity::class;
    }

    /**
     * Create a section relationship and keep the activity_relationship records in sync.
     *
     * @param int $section_id
     * @param string $class_name
     * @return static
     */
    public static function create(int $section_id, string $class_name): self {
        global $DB;

        if (!in_array($class_name, self::get_all_class_names())) {
            throw new \coding_exception("Invalid class_name: {$class_name}");
        }
        $section = section::repository()->find($section_id);
        if (!$section) {
            throw new \coding_exception('Specified section id does not exist');
        }
        $activity = activity_model::load_by_id($section->activity_id);
        require_capability('mod/perform:manage_activity', $activity->get_context());

        return $DB->transaction(function () use ($section_id, $class_name, $activity) {
            $relationship_entity = activity_relationship_entity::repository()
                ->where('activity_id', $activity->get_id())
                ->where('class_name', $class_name)
                ->get()
                ->first();
            // Create relationship record only if it doesn't exist already.
            if (!$relationship_entity) {
                $relationship_entity = new activity_relationship_entity();
                $relationship_entity->activity_id = $activity->get_id();
                $relationship_entity->class_name = $class_name;
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
     * Delete a section relationship and keep the activity_relationship records in sync.
     *
     * @param int $section_id
     * @param string $class_name
     * @return bool
     */
    public static function delete(int $section_id, string $class_name): bool {
        global $DB;

        if (!in_array($class_name, self::get_all_class_names())) {
            throw new \coding_exception("Invalid class_name: {$class_name}");
        }
        $section = section::repository()->find($section_id);
        if (!$section) {
            throw new \coding_exception('Specified section id does not exist');
        }
        $activity = activity_model::load_by_id($section->activity_id);
        require_capability('mod/perform:manage_activity', $activity->get_context());

        /** @var activity_relationship_entity $activity_relationship_entity */
        $activity_relationship_entity = activity_relationship_entity::repository()
            ->where('activity_id', $activity->get_id())
            ->where('class_name', $class_name)
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

    // TODO: Change this when we have relationship classes.
    private static function get_all_class_names(): array {
        return [
            'subject',
            'manager',
            'appraiser',
        ];
    }
}
