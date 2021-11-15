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

namespace totara_reportedcontent\hook;

use totara_reportedcontent\review;

/**
 * Class remove_review_content
 *
 * @package totara_reportedcontent\hook
 */
class remove_review_content extends  \totara_core\hook\base {
    /**
     * @var review
     */
    public $review;

    /**
     * @var bool
     */
    public $success;

    /**
     * @param review $review
     */
    public function __construct(review $review) {
        $this->review = $review;
        $this->success = false;
    }
}