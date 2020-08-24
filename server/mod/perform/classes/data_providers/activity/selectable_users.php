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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com?
 * @package mod_perform
 */

namespace mod_perform\data_providers\activity;

use core\entities\user;
use core\entities\user_repository;
use core\orm\entity\repository;
use core\orm\query\field;
use core\tenant_orm_helper;
use mod_perform\data_providers\provider;
use mod_perform\models\activity\activity as activity_model;

/**
 * Class selectable_users
 *
 * Gets users that can be selected by the current user for manual participation, etc.
 *
 * @package mod_perform\data_providers\activity
 */
class selectable_users extends provider {

    /**
     * @var activity_model
     */
    protected $activity;

    public function __construct(activity_model $activity) {
        $this->activity = $activity;
    }

    /**
     * @inheritDoc
     */
    protected function build_query(): repository {
        $repository = user::repository()
            ->filter_by_not_guest()
            ->filter_by_not_deleted()
            ->filter_by_not_suspended()
            ->when(true, function (repository $repository) {
                tenant_orm_helper::restrict_users(
                    $repository,
                    new field('id', $repository->get_builder()),
                    $this->activity->get_context()
                );
            })
            ->order_by_full_name();

        // We limit the number of users this returns to make sure it
        // performs well. This query is not designed to list
        // all users nor does it support pagination
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

}
