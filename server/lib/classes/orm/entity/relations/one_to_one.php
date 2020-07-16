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
use core\orm\entity\repository;
use core\orm\query\builder;

/**
 * Class has_one defines one to one relation between entities
 */
abstract class one_to_one extends relation {

    /**
     * This method should apply necessary constraints when loading a relation for a single entity
     *
     * @return void
     */
    public function constraints_for_entity() {
        $this->repo->where($this->get_foreign_key(), $this->entity->get_attribute($this->get_key()));
    }

    /**
     * This function allows to override how data is fetched from repository for one entity
     *
     * @return collection|entity|null
     */
    public function load_for_entity() {
        return $this->get_repo()
            ->unless($this->has_order_by(), function (repository $builder) {
                $builder->order_by('id');
            })
            ->first();
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

        // Chunk this to avoid too many value for IN condition
        $keys_chunked = array_chunk($keys, builder::get_db()->get_max_in_params());

        foreach ($keys_chunked as $keys) {
            // Load all related objects
            $results = $this->repo->where($this->get_foreign_key(), $keys)->get();

            $results->key_by($this->get_foreign_key());

            // Now iterate over original collection of models and inject appropriate results there.
            $collection->map(
                function (entity $item) use ($results, $name) {
                    $item->relate($name, $results->item($item->{$this->get_key()}));

                    return $item;
                }
            );
        }
    }

}
