<?php
/**
 *
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
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 * @author Sam Hemelyrk <sam.hemelryk@totaralearning.com>
 * @package core_user
 *
 */

namespace core_user\hook;

use totara_core\hook\base;

/**
 * Class allow_view_profile
 *
 * Hook to allow components and plugins to grant access to one user to
 * view another user's profile.
 *
 */
class allow_view_profile extends base {

    /**
     * @var int $target_user_id The user whose profile is being viewed.
     */
    public $target_user_id;

    /**
     * @var int $viewing_user_id The user who is trying to view the target user's profile.
     */
    public $viewing_user_id;

    /**
     * @var bool $allow_view_profile Whether the viewing user should be allowed to view the target user's profile
     */
    private $allow_view_profile = false;

    /**
     * allow_view_profile constructor.
     * @param int $target_user_id
     * @param int $viewing_user_id
     */
    public function __construct(int $target_user_id, int $viewing_user_id) {
        $this->target_user_id = $target_user_id;
        $this->viewing_user_id = $viewing_user_id;
    }

    public function give_permission() {
        $this->allow_view_profile = true;
    }

    public function has_permission(): bool {
        return $this->allow_view_profile;
    }
}