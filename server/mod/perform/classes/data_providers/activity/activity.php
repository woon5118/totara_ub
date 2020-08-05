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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\data_providers\activity;

use core\orm\collection;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\models\activity\activity as activity_model;

class activity {

    /**
     * @var collection
     */
    protected $items;

    /**
     * Fetch activities from the database
     *
     * @return $this
     */
    public function fetch() {
        $this->fetch_activities();

        return $this;
    }

    /**
     * Fetch activities that can be managed by the logged in user.
     *
     * @return $this
     */
    protected function fetch_activities() {
        $repo = activity_entity::repository()
            ->filter_by_visible()
            ->order_by('id');
        $entities = $repo->get();
        $this->items = [];

        foreach ($entities as $entity) {
            $item = activity_model::load_by_entity($entity);
            if ($item->can_manage() || $item->can_view_participation_reporting()) {
                $this->items[] = $item;
            }
        }
        return $this;
    }

    /**
     * get items for the model
     *
     * @return collection
     */
    public function get() {
        return $this->items;
    }
}