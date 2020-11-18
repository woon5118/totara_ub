<?php
/**
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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entity\activity;

use core\orm\entity\repository;
use core\orm\query\builder;
use mod_perform\models\activity\participant_source;

class participant_instance_repository extends repository {

    /**
     * Should one user be able to see the other users profile details in the context of mod perform
     * based on participant instance records.
     *
     * Will return true if the viewing user share a subject instance with the target user,
     * or if the the target user is the subject of a subject_instance that the viewing user is participating in.
     *
     * @param int $viewing_user_id The user requesting to view the target user
     * @param int $target_user_id The user who's
     * @return bool
     * @see subject_static_instance_repository::user_can_view_other_users_profile
     */
    public static function user_can_view_other_users_profile(int $viewing_user_id, int $target_user_id): bool {
        $shared_subject_instance = builder::table(participant_instance::TABLE)
            ->as('other_pi')
            ->select('id')
            ->where('participant_id', $target_user_id)
            ->where('participant_source', participant_source::INTERNAL)
            ->where_field('subject_instance_id', 'main_pi.subject_instance_id')
            ->where_field('id', '<>', 'main_pi.id');

        $participant_in_subject_about_target = builder::table(subject_instance::TABLE)
            ->as('si2')
            ->where('subject_user_id', $target_user_id)
            ->where_field('id', 'main_pi.subject_instance_id');

        return builder::table(participant_instance::TABLE)
            ->as('main_pi')
            ->join([subject_instance::TABLE, 'si'], 'subject_instance_id', 'id')
            ->join([track_user_assignment::TABLE, 'tua'], 'si.track_user_assignment_id', 'id')
            ->join([track::TABLE, 't'], 'tua.track_id', 'id')
            ->join([activity::TABLE, 'a'], 't.activity_id', 'id')
            ->where('participant_id', $viewing_user_id)
            ->where('participant_source', participant_source::INTERNAL)
            ->where(function (builder $builder) use ($target_user_id) {
                // Not anonymous activity OR the target is the subject of the activity that the viewer is a participant in.
                return $builder->where('a.anonymous_responses', 0)
                    ->or_where('si.subject_user_id', $target_user_id);
            })
            ->where(function (builder $builder) use ($shared_subject_instance, $participant_in_subject_about_target) {
                return $builder->where_exists($shared_subject_instance)
                    ->or_where_exists($participant_in_subject_about_target);
            })
            ->exists();
    }

    /**
     * Filter participant instances by the participant user id.
     *
     * @param int $participant_user_id
     * @return participant_instance_repository
     */
    public function filter_by_participant_user(int $participant_user_id) {
        return $this->where('participant_source', participant_source::INTERNAL)
            ->where('participant_id', $participant_user_id);
    }

    /**
     * Filter to participant instances at or below a specified context.
     *
     * @param \context $context
     * @return $this
     */
    public function filter_by_context(\context $context): self {
        // No need for restrictions for system context.
        if (get_class($context) == 'context_system') {
            return $this;
        }
        if (!$this->has_join('context')) {
            $this->join('perform_subject_instance', 'perform_participant_instance.subject_instance_id', '=', 'id')
                ->join('perform_track_user_assignment', 'perform_subject_instance.track_user_assignment_id', '=', 'id')
                ->join('perform_track', 'perform_track_user_assignment.track_id', '=', 'id')
                ->join('perform', 'perform_track.activity_id', '=', 'id')
                ->join('course', 'perform.course', '=', 'id')
                ->join('context', function (builder $joining) {
                    $joining->where_field('course.id', 'context.instanceid')
                        ->where('context.contextlevel', '=', CONTEXT_COURSE);
                });
        }
        return $this->where(function (builder $builder) use ($context) {
            $builder->where('context.id', $context->id)
                ->or_where_like_starts_with('context.path', "{$context->path}/");
        });
    }

    /**
     * Add join to filter for non-deleted participant users
     *
     * @param builder|repository $repository_or_builder
     * @param string $participant_instance_alias
     */
    public static function add_user_not_deleted_filter($repository_or_builder, string $participant_instance_alias) {
        $join_alias = 'pir_user_join';
        if (!$repository_or_builder->has_join($join_alias)) {
            $repository_or_builder->left_join(['user', $join_alias], function (builder $builder) use ($participant_instance_alias) {
                $builder->where_field('id', $participant_instance_alias.'.participant_id')
                    ->where($participant_instance_alias.'.participant_source', participant_source::INTERNAL);
            })->where(
                function (builder $builder) use ($join_alias, $participant_instance_alias) {
                    $builder->or_where($participant_instance_alias.'.participant_source', participant_source::EXTERNAL)
                        ->or_where($join_alias.'.deleted', 0);
                }
            );
        }
    }

}