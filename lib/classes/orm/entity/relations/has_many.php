<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package core_orm
 */

namespace core\orm\entity\relations;

use core\orm\collection;
use core\orm\entity\entity;

/**
 * Class has_many
 *
 * Represents one to many relationship between entities
 */
class has_many extends relation {

    /**
     * Allow saving child models
     *
     * @var bool
     */
    protected $can_save = true;

    /**
     * This method should apply necessary constraints when loading a relation for a single entity
     *
     * @return void
     */
    public function constraints_for_entity() {
        $this->repo->where(
            $this->get_foreign_key(),
            $this->entity->get_attribute($this->get_key())
        );
    }

    /**
     * Apply constraints for loading relation for collection of items and map them back to the collection.
     *
     * @param string $name
     * @param collection $collection
     * @return void
     */
    public function load_for_collection($name, collection $collection) {
        // Extracting keys to load related objects by.
        $keys = $collection->pluck($this->get_key());

        // Load possible values
        $results = $this->repo->where($this->get_foreign_key(), $keys)->get();

        // Now iterate over original collection and append the results there
        $collection->map(function ($item) use ($results, $name) {
            /** @var entity $item */
            $item->relate($name, $results->filter($this->get_foreign_key(), $item->{$this->get_key()}));

            return $item;
        });
    }

}
