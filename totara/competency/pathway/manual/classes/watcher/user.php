<?php
/*
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_manual
 */

namespace pathway_manual\watcher;

use core_user\hook\allow_view_profile_field;
use pathway_manual\models\rating;

class user {

    protected const ALLOWED_FIELDS = [
        'fullname',
        'profileimageurl',
    ];

    /**
     * User access hook to check if one user can view another users profile field in the context of manual ratings.
     *
     * @param allow_view_profile_field $hook
     */
    public static function allow_view_profile_field(allow_view_profile_field $hook): void {
        if ($hook->has_permission()) {
            return;
        }

        if (!in_array($hook->field, self::ALLOWED_FIELDS, true)) {
            return;
        }

        if (rating::users_share_rating($hook->viewing_user_id, $hook->target_user_id)) {
            $hook->give_permission();
        }
    }

}
