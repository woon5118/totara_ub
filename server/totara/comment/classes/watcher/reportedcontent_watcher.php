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
 * @package totara_comment
 */

namespace totara_comment\watcher;

use totara_comment\comment;
use totara_reportedcontent\hook\remove_review_content;

final class reportedcontent_watcher {
    /**
     * @param remove_review_content $hook
     * @return void
     */
    public static function delete_comment(remove_review_content $hook): void {
        // These components all use comments in the same way, so are removed here
        $valid_components = [
            'engage_article',
            'totara_playlist',
            'test_component',
        ];

        if (!in_array($hook->review->get_component(), $valid_components)) {
            return;
        }

        $comment = comment::from_id($hook->review->get_item_id());
        $comment->soft_delete(null, comment::REASON_DELETED_REPORTED);

        $hook->success = true;
    }
}