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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\data_providers\activity;

use core\collection;
use core\entities\user;
use core\entities\user_repository;
use core\orm\entity\repository;
use core_user\access_controller;
use Dompdf\FrameReflower\Page;
use mod_perform\data_providers\provider;

/**
 * Class selectable_users
 *
 * Gets users that can be selected by the current user for manual participation, etc.
 *
 * @package mod_perform\data_providers\activity
 */
class selectable_users extends provider {

    /**
     * @inheritDoc
     */
    protected function build_query(): repository {
        $repository = user::repository()
            ->select_profile_summary_card_fields()
            ->filter_by_not_guest()
            ->filter_by_not_deleted()
            ->order_by_full_name();

        // Somewhat arbitrary number - we want to make the result set small-ish for performance.
        // TODO: Evaluate performance and see if we still need this in TL-26294
        $repository->limit(30);

        return $repository;
    }

    /**
     * @param user_repository|repository $repository
     * @param string $substring
     */
    protected function filter_query_by_fullname(repository $repository, string $substring): void {
        $repository->filter_by_full_name($substring);
    }

    /**
     * @param user_repository|repository $repository
     * @param int[] $user_ids_to_exclude
     */
    protected function filter_query_by_exclude_users(repository $repository, array $user_ids_to_exclude): void {
        $repository->where_not_in('id', $user_ids_to_exclude);
    }

    /**
     * Restrict the returned users to what the current user is allowed to view.
     *
     * @return collection|user[]
     */
    protected function process_fetched_items(): collection {
        return $this->items
            ->filter(static function (user $user) {
                // TODO: Improve performance and make sure it works with multi-tenancy in TL-26294
                return access_controller::for($user->get_record())
                    ->can_view_field('fullname');
            });
    }

}
