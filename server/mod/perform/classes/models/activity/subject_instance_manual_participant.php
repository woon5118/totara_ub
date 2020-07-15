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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Marco Song <marco.song@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use totara_form\model;
use \mod_perform\entities\activity\subject_instance_manual_participant as manual_participant_entity;

class subject_instance_manual_participant extends model {
    /**
     * @var manual_participant_entity
     */
    protected $entity;

    /**
     * Create a new manual participant
     *
     * @param int $subject_instance_id
     * @param int $core_relationship_id
     * @param array $user_ids
     * @param int $created_by
     * @return mixed
     * @throws \Throwable
     */
    public static function create(int $subject_instance_id, int $core_relationship_id, array $user_ids, int $created_by) {
        global $DB;

        return $DB->transaction(
            function () use ($subject_instance_id, $core_relationship_id, $user_ids, $created_by) {
                $manual_relationship_entities = manual_participant_entity::repository()
                    ->where('subject_instance_id', $subject_instance_id)
                    ->where('core_relationship_id', $core_relationship_id)
                    ->get();
                if ($manual_relationship_entities->count() == 0) {
                    foreach ($user_ids as $user_id) {
                        $manual_relationship_entity = new manual_participant_entity();
                        $manual_relationship_entity->subject_instance_id = $subject_instance_id;
                        $manual_relationship_entity->core_relationship_id = $core_relationship_id;
                        $manual_relationship_entity->user_id = $user_id;
                        $manual_relationship_entity->created_by = $created_by;
                        $manual_relationship_entity->save();
                        $manual_relationship_entities->append($manual_relationship_entity);
                    }
                }
                return $manual_relationship_entities;
            }
        );
    }
}
