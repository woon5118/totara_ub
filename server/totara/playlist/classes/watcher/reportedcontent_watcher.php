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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package totara_playlist
 */

namespace totara_playlist\watcher;

use totara_playlist\playlist;
use totara_reportedcontent\hook\get_review_context;

final class reportedcontent_watcher {
    /**
     * @param get_review_context $hook
     * @return void
     */
    public static function get_context(get_review_context $hook): void {
        if ('totara_playlist' !== $hook->component) {
            return;
        }
        $playlist = playlist::from_id($hook->instance_id);

        $hook->context_id = $playlist->get_context()->id;
        $hook->success = true;
    }
}