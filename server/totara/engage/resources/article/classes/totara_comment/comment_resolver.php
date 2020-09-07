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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package engage_article
 */
namespace engage_article\totara_comment;

use totara_comment\comment;
use totara_comment\resolver;
use totara_engage\access\access_manager;
use engage_article\totara_engage\resource\article;

/**
 * Comment resolver for engage_article
 */
final class comment_resolver extends resolver {
    /**
     * @param string $area
     * @return bool
     */
    private function is_valid_area(string $area): bool {
        return in_array($area, ['comment']);
    }

    /**
     * @param int    $instanceid
     * @param string $area
     * @param int    $actorid
     *
     * @return bool
     */
    public function is_allow_to_create(int $instanceid, string $area, int $actorid): bool {
        if (!$this->is_valid_area($area)) {
            return false;
        }

        // If user can access to the instance, meaning that user can create the comment.
        $article = article::from_resource_id($instanceid);
        return access_manager::can_access($article, $actorid);
    }

    /**
     * @param comment $comment
     * @param int     $actorid
     *
     * @return bool
     */
    public function is_allow_to_update(comment $comment, int $actorid): bool {
        $owner_id = $comment->get_userid();

        return (access_manager::can_manage_engage(\context_user::instance($owner_id), $actorid) || $actorid == $owner_id);
    }

    /**
     * @param int $resourceid
     * @param string $area
     * @return int
     */
    public function get_context_id(int $resourceid, string $area): int {
        $article = article::from_resource_id($resourceid);
        $context = $article->get_context();

        return $context->id;
    }

    /**
     * @param comment $comment
     * @param int     $actorid
     *
     * @return bool
     */
    public function is_allow_to_delete(comment $comment, int $actorid): bool {
        $owner_id = $comment->get_userid();
        return (access_manager::can_manage_engage(\context_user::instance($owner_id), $actorid) || $actorid == $owner_id);
    }
}