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
use core\orm\query\builder;
use core\orm\query\field;

/**
 * Class has_many_through
 *
 * Represents one to many relationship between entities connected through another entity
 */
class has_many_through extends relation {

    /**
     * Intermediate entity model class
     *
     * @var string
     */
    protected $intermediate;

    /**
     * Intermediate entity key
     *
     * @var string
     */
    protected $intermediate_key;

    /**
     * Intermediate entity foreign key
     *
     * @var string
     */
    protected $intermediate_foreign_key;

    public function __construct(entity $entity, string $intermediate,
        string $related,
        string $foreign_key,
        string $intermediate_foreign_key,
        string $key,
        string $intermediate_key = 'id'
    ) {
        $this->intermediate_foreign_key = $intermediate_foreign_key;
        $this->intermediate_key = $intermediate_key;
        $this->intermediate = $intermediate;

        parent::__construct($entity, $related, $foreign_key, $key);
    }

    /**
     * This method should apply necessary constraints when loading a relation for a single entity
     *
     * @return void
     */
    public function constraints_for_entity() {
        $this->repo->join($this->intermediate::TABLE, $this->get_foreign_key(), $this->get_intermediate_key())
            ->where(new field($this->get_intermediate_foreign_key(), builder::table($this->intermediate::TABLE)), $this->entity->{$this->get_key()});
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

        $intermediate_builder = builder::table($this->intermediate::TABLE);

        // Load possible values
        $results = $this->repo
            ->select($this->repo->get_table() . '.*')
            ->add_select($intermediate_builder->get_table() . '.' . $this->get_intermediate_foreign_key() . ' as ' . $this->get_intermediate_key_name())
            ->join($this->intermediate::TABLE, $this->get_foreign_key(), $this->get_intermediate_key())
            ->where(new field($this->get_intermediate_foreign_key(), $intermediate_builder), $keys)
            ->get();

        // Now iterate over original collection and append the results there
        $collection->map(function ($item) use ($results, $name) {
            /** @var entity $item */
            $item->relate($name, $results->filter($this->get_intermediate_key_name(), $item->{$this->get_key()}));

            return $item;
        });

        $results->transform(function (entity $entity) {
            // We add this key temporarily to link children to the parent,
            // Since we do want to return entities in a valid state,
            // We'll unset it
            unset($entity->{$this->get_intermediate_key_name()});

            return $entity;
        });
    }

    /**
     * Delete related models
     *
     * @return $this
     */
    public function delete() {
        // Let's get IDS to delete
        $ids = $this->repo->select('id')->get()->pluck('id');

        if (!empty($ids)) {
            $this->related::repository()
                ->where('id', 'in', $ids)
                ->delete();
        }

        return $this;
    }

    /**
     * Update related models
     *
     * @param array $attributes Attributes to update
     * @return $this
     */
    public function update(array $attributes) {
        // Let's get IDS to delete
        $ids = $this->repo->select('id')->get()->pluck('id');

        if (!empty($ids)) {
            $this->related::repository()
                ->where('id', 'in', $ids)
                ->update($attributes);
        }

        return $this;
    }

    /**
     * Get intermediate entity key
     *
     * @return string
     */
    public function get_intermediate_key() {
        return $this->intermediate_key;
    }

    /**
     * Get intermediate entity foreign key
     *
     * @return string
     */
    public function get_intermediate_foreign_key() {
        return $this->intermediate_foreign_key;
    }

    /**
     * Get temporary key name that is used to connect models
     *
     * @return string
     */
    public function get_intermediate_key_name() {
        return $this->intermediate::TABLE . '___' . $this->get_intermediate_foreign_key();
    }

}
