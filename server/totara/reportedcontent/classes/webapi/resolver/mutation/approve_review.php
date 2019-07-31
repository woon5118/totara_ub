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

use context_system;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use totara_reportedcontent\review;

/**
 * Class approve_review
 *
 * @package totara_reportedcontent\webapi\resolver\mutation
 */
final class approve_review implements mutation_resolver {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return review
     */
    public static function resolve(array $args, execution_context $ec): review {
        global $USER;
        require_login();

        // Can only approve if you've got the capability
        $has_capability = has_capability('totara/reportedcontent:manage', context_system::instance(), $USER);
        if (!$has_capability) {
            throw new \moodle_exception('nopermission', 'totara_reportedcontent');
        }

        $review_id = $args['review_id'];
        $review = review::from_id($review_id);
        if (!$review || !$review->exists()) {
            throw new \coding_exception("Review '{$review_id}' does not exist.");
        }

        $review->do_review(review::DECISION_APPROVE, $USER->id);

        return $review;
    }
}