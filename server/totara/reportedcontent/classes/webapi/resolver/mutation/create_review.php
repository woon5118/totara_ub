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
 * @package totara_reportedcontent
 */

namespace totara_reportedcontent\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use totara_comment\comment;
use totara_reportedcontent\hook\get_review_context;
use totara_reportedcontent\review;

/**
 * Mutation for creating a reportedcontent review.
 * Any logged in user can report content
 *
 * @package totara_reportedcontent\webapi\resolver\mutation
 */
final class create_review implements mutation_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return review
     */
    public static function resolve(array $args, execution_context $ec): review {
        global $USER;
        require_login();

        $item_id = $args['item_id'];
        $instance_id = $args['instance_id'];
        $component = $args['component'];
        $area = $args['area'];
        $url = $args['url'];

        // The assumption here is we're working with a totara_comment
        // That's true for now, but this will have to be split out into a hook or something
        // if we want to expand into reporting other non-comment items
        $comment = comment::from_id($item_id);
        if (!$comment || !$comment->exists()) {
            throw new \coding_exception("Could not find the comment to create a review for.");
        }

        // Get the correct context
        if ($component === 'test_component') {
            if (!defined('PHPUNIT_TEST') || !PHPUNIT_TEST) {
                // Not a unit test, so we just throw our exception.
                throw new \coding_exception("'test_component' is only available for unit tests");
            }
            $context_id = \context_system::instance()->id;
        } else {
            $hook = new get_review_context(
                $component,
                $instance_id,
                $area,
                $item_id
            );
            $hook->execute();

            if (!$hook->success) {
                throw new \coding_exception("Was unable to create a review, no hook observer was found or executed");
            }

            $context_id = $hook->context_id;
        }

        return review::create(
            $item_id,
            $context_id,
            $component,
            $area,
            $url,
            $comment->get_content(),
            $comment->get_format(),
            $comment->get_timecreated(),
            $comment->get_userid(),
            $USER->id
        );
    }
}