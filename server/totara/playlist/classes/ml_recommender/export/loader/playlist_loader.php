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
 * @package ml_recommender
 */
namespace totara_playlist\ml_recommender\export\loader;

use ml_recommender\export\loader\content_loader;
use totara_engage\access\access;

/**
 * Class playlist_loader
 * @package ml_recommender\export\loader
 */
final class playlist_loader extends content_loader {
    /**
     * @return int[]
     */
    public function get_all_ids(): array {
        global $DB;
        return $DB->get_fieldset_select('playlist', 'id', 'access = ?', [access::PUBLIC]);
    }

    /**
     * Returning the total of playlists, note that this will only count the public playlists
     *
     * @return int
     */
    public function get_total(): int {
        global $DB;
        return $DB->count_records('playlist', ['access' => access::PUBLIC]);
    }

    /**
     * @return string
     */
    public function get_content_type(): string {
        return 'totara_playlist';
    }
}