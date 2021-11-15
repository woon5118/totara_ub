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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
namespace container_workspace\loader\discussion;

use container_workspace\discussion\discussion;
use container_workspace\query\discussion\query;
use container_workspace\entity\workspace_discussion;
use container_workspace\query\discussion\sort;
use core\orm\pagination\offset_cursor_paginator;
use core\orm\query\builder;
use core\orm\query\order;
use core\orm\query\subquery;
use totara_comment\entity\comment as comment_entity;
use totara_comment\comment;
use container_workspace\workspace;
use totara_reaction\entity\reaction as reaction_entity;

/**
 * Loader class for discussions within a workspace
 */
final class loader {
    /**
     * loader constructor.
     * Preventing this class from construction
     */
    private function __construct() {
    }

    /**
     * @param query $query
     * @return offset_cursor_paginator
     */
    public static function get_discussions(query $query): offset_cursor_paginator {
        global $CFG;

        $builder = builder::table(workspace_discussion::TABLE, 'wd');
        $builder->join(['user', 'u'], 'wd.user_id', 'u.id');

        // Make sure that the discussion's id is distinct.
        $builder->select([
            "wd.id AS discussion_id",
            "wd.course_id AS discussion_workspace_id",
            "wd.user_id AS discussion_user_id",
            "wd.content AS discussion_content",
            "wd.content_format AS discussion_content_format",
            "wd.content_text AS discussion_content_text",
            "wd.time_pinned AS discussion_time_pinned",
            "wd.time_created AS discussion_time_created",
            "wd.time_modified AS discussion_time_modified",
            "wd.time_deleted AS discussion_time_deleted",
            "wd.reason_deleted AS discussion_reason_deleted",
            "wd.timestamp AS discussion_timestamp",
            "u.id AS user_id",
            "u.email AS user_email",
            "u.picture AS user_picture",
            "u.imagealt AS user_image_alt"
        ]);

        // Now add user fields.
        $user_fields_sql = get_all_user_name_fields(true, 'u', null, 'user_');
        $builder->add_select_raw($user_fields_sql);

        // Include counts for comments
        $builder->add_select((new subquery(function (builder $builder) {
            $builder->from(comment_entity::TABLE, 'c')
                ->select_raw('COUNT(*)')
                ->where_field('instanceid', 'wd.id')
                ->where('c.component', workspace::get_type())
                ->where('c.area', discussion::AREA)
                ->where_null('c.parentid');
        }))->as('discussion_total_comments'));

        // Include counts for reactions
        $builder->add_select((new subquery(function (builder $builder) {
            $builder->from(reaction_entity::TABLE, 'r')
                ->select_raw('COUNT(*)')
                ->where_field('instanceid', 'wd.id')
                ->where('r.component', workspace::get_type())
                ->where('r.area', discussion::AREA);
        }))->as('discussion_total_reactions'));

        $builder->results_as_arrays();
        $builder->map_to([self::class, 'create_discussion']);

        $workspace_id = $query->get_workspace_id();
        $builder->where('wd.course_id', $workspace_id);

        // Check for search term.
        $search_term = $query->get_search_term();
        if (null !== $search_term && '' !== $search_term) {
            // Filter results by the discussion content, or any comment/reply underneath the discussion
            require_once("{$CFG->dirroot}/totara/core/searchlib.php");

            $builder->where(function (builder $builder) use ($search_term) {
                $keywords = totara_search_parse_keywords($search_term);
                [$search_sql, $search_params] = totara_search_get_keyword_where_clause(
                    $keywords,
                    ['wd.content_text'],
                    SQL_PARAMS_NAMED
                );

                // Comment checks can be expensive so we lookup with an exists check instead
                [$search2_sql, $search2_params] = totara_search_get_keyword_where_clause(
                    $keywords,
                    ['contenttext'],
                    SQL_PARAMS_NAMED
                );
                $exists_builder = comment_entity::repository()
                    ->select('instanceid')
                    ->where_field('instanceid', 'wd.id')
                    ->where('component', workspace::get_type())
                    ->where('area', discussion::AREA)
                    ->where_raw($search2_sql, $search2_params)
                    ->get_builder();

                $builder->or_where_raw($search_sql, $search_params)
                    ->or_where_exists($exists_builder);
            });
        }

        // Check for pinned discussion
        $pinned = $query->get_pinned_value();
        if (null !== $pinned) {
            if ($pinned) {
                $builder->where_not_null('wd.time_pinned');

                // The latest pinned post will be put at the top.
                $builder->order_by('wd.time_pinned', order::DIRECTION_DESC);
            } else {
                $builder->where_null('wd.time_pinned');
            }
        }

        if (null === $pinned || false === $pinned) {
            // Sort should only work with none pinned query. Otherwise, pinned query will have to sort
            // by the pinned time.
            $sort = $query->get_sort();

            if (sort::is_recent($sort)) {
                // Most recently updated discussion at the top, and following up by the older ones.
                $builder->order_by('wd.timestamp', order::DIRECTION_DESC);
            } else if (sort::is_posted_date($sort)) {
                // Most recently created post at the top, and following up to the older one.
                $builder->order_by('wd.time_created', order::DIRECTION_DESC);
            }
        }

        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }

    /**
     * This function should only be used for the builder to build the discussion
     *
     * @param array $record
     * @return discussion
     *
     * @internal
     */
    public static function create_discussion(array $record): discussion {
        $map = [
            'id' => 'discussion_id',
            'course_id' => 'discussion_workspace_id',
            'user_id' => 'discussion_user_id',
            'content' => 'discussion_content',
            'content_format' => 'discussion_content_format',
            'content_text' => 'discussion_content_text',
            'time_pinned' => 'discussion_time_pinned',
            'time_created' => 'discussion_time_created',
            'time_modified' => 'discussion_time_modified',
            'time_deleted' => 'discussion_time_deleted',
            'reason_deleted' => 'discussion_reason_deleted',
        ];

        $entity = new workspace_discussion();

        foreach ($map as $attribute => $record_attribute) {
            if (!array_key_exists($record_attribute, $record)) {
                throw new \coding_exception(
                    "The array record does not have attribute '{$record_attribute}'"
                );
            }

            $entity->set_attribute($attribute, $record[$record_attribute]);
        }

        // Start mapping user's record.
        $user = [];
        $user_fields = get_all_user_name_fields(false, 'u', 'user_');

        // Adding user fields for email and id.
        $user_fields['email'] = 'user_email';
        $user_fields['id'] = 'user_id';
        $user_fields['picture'] = 'user_picture';
        $user_fields['imagealt'] = 'user_image_alt';

        foreach ($user_fields as $field => $sql_field) {
            if (!array_key_exists($sql_field, $record)) {
                debugging("The array record does not have field '{$sql_field}'", DEBUG_DEVELOPER);
                continue;
            }

            $user[$field] = $record[$sql_field];
        }

        $user = (object) $user;
        $discussion = discussion::from_entity($entity, $user);

        $discussion->set_total_comments((int) $record['discussion_total_comments']);
        $discussion->set_total_reactions((int) $record['discussion_total_reactions']);

        return $discussion;
    }
}