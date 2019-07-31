<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @package totara_playlist
 */
namespace totara_playlist\totara_comment;

use totara_comment\comment;
use totara_comment\resolver;
use totara_engage\access\access_manager;
use totara_playlist\playlist;

/**
 * Comment resolver for playlist
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
        $playlist = playlist::from_id($instanceid);
        return access_manager::can_access($playlist, $actorid);
    }

    /**
     * @param comment $comment
     * @param int     $actorid
     *
     * @return bool
     */
    public function is_allow_to_delete(comment $comment, int $actorid): bool {
        $instanceid = $comment->get_instanceid();

        $playlist = playlist::from_id($instanceid);
        return access_manager::can_access($playlist, $actorid);
    }

    /**
     * @param comment $comment
     * @param int $actorid
     *
     * @return bool
     */
    public function is_allow_to_update(comment $comment, int $actorid): bool {
        $instanceid = $comment->get_instanceid();

        $playlist = playlist::from_id($instanceid);
        return access_manager::can_access($playlist);
    }

    /**
     * @param int       $playlistid
     * @param string    $area
     * @return int
     */
    public function get_context_id(int $playlistid, string $area): int {
        $playlist = playlist::from_id($playlistid);
        return $playlist->get_contextid();
    }
}