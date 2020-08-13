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
use totara_comment\comment;
use container_workspace\workspace;

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
        global $DB;

        $builder = builder::table(workspace_discussion::TABLE, 'wd');
        $builder->join(['user', 'u'], 'wd.user_id', 'u.id');

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
            "wd.timestamp AS discussion_timestamp",
            "u.id AS user_id",
            "u.email AS user_email",
            "u.picture AS user_picture",
            "u.imagealt AS user_image_alt"
        ]);

        // Now add user fields.
        $user_fields_sql = get_all_user_name_fields(true, 'u', null, 'user_');
        $builder->add_select_raw($user_fields_sql);

        $builder->results_as_arrays();
        $builder->map_to([static::class, 'create_discussion']);

        $workspace_id = $query->get_workspace_id();
        $builder->where('wd.course_id', $workspace_id);

        // Check for search term.
        $search_term = $query->get_search_term();
        if (null !== $search_term && '' !== $search_term) {
            $builder->left_join(
                [comment::get_entity_table(), 'tc'],
                function (builder $join): void {
                    $join->where_field('tc.instanceid', 'wd.id');
                    $join->where('tc.component', workspace::get_type());
                    $join->where('tc.area', discussion::AREA);
                }
            );

            // Where like for workspace discussion and the comment that related to the workspace.
            $discussion_like = $DB->sql_like('wd.content_text', ':discussion_search_term', false);
            $comment_like = $DB->sql_like('tc.contenttext', ':comment_search_term', false);

            $builder->where_raw(
                "({$discussion_like} OR {$comment_like})",
                [
                    'discussion_search_term' => "%{$DB->sql_like_escape($search_term)}%",
                    'comment_search_term' => "%{$DB->sql_like_escape($search_term)}%"
                ]
            );
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
            'time_modified' => 'discussion_time_modified'
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
        return discussion::from_entity($entity, $user);
    }
}