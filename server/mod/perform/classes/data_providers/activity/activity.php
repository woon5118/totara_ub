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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\data_providers\activity;

use core\collection;
use core\orm\entity\repository;
use mod_perform\data_providers\provider;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\models\activity\activity as activity_model;

/**
 * Class activity.
 *
 * @package mod_perform\data_providers\activity
 *
 * @method collection|activity_model[] get
 */
class activity extends provider {

    /**
     * @inheritDoc
     */
    protected function build_query(): repository {
        return activity_entity::repository()
            ->filter_by_visible()
            ->order_by('id');
    }

    /**
     * Restrict the returned activities to what the current user is allowed to view.
     *
     * @return collection|activity_model[]
     */
    protected function process_fetched_items(): collection {
        return $this->items
            ->map_to(activity_model::class)
            ->filter(static function (activity_model $activity) {
                return $activity->can_manage() || $activity->can_view_participation_reporting();
            });
    }
}