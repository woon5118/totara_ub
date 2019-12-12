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

use core\orm\entity\entity;
use core\orm\entity\repository;
use core\orm\collection;
use coding_exception;
use core\orm\query\builder;

/**
 * Class relation
 * This class outlines a scaffolding for defining a relationship between entities
 *
 * @mixin builder
 */
abstract class relation {

    /**
     * A flag indicating whether this relation is able to save related models.
     *
     * @var bool
     */
    protected $can_save = false;

    /**
     * Related entity repository to fetch data
     *
     * @var repository
     */
    protected $repo = null;

    /**
     * Local key
     *
     * @var string
     */
    protected $key = '';

    /**
     * Foreign key of the relation
     *
     * @var string
     */
    protected $foreign_key = null;

    /**
     * Related model class name
     *
     * @var string
     */
    protected $related = null;

    /**
     * INTERNAL flag to apply constraints needed when lazy-loading a model
     *
     * @var bool
     */
    protected static $apply_entity_constraints = true;

    /**
     * Link to a parent entity
     * Storing a link doesn't play a a significant role when using eager-loading.
     *
     * @var entity|null
     */
    protected $entity = null;

    /**
     * relation constructor.
     *
     * @param entity $entity Parent entity link
     * @param string $related Related model class name
     * @param string $foreign_key
     * @param string $key
     */
    public function __construct(entity $entity, string $related, string $foreign_key, string $key = 'id') {

        if (!is_subclass_of($related, entity::class, true)) {
            throw new coding_exception("'$related' must be a valid entity subclass.");
        }

        $this->foreign_key = $foreign_key;
        $this->key = $key;
        $this->related = $related;
        $this->set_entity($entity);

        // Related is entity::class
        $this->repo = call_user_func($related . '::repository');

        $this->apply_constraints_for_entity();
    }

    /**
     * Set "parent" entity
     *
     * @param entity $entity
     * @return $this
     */
    protected function set_entity(entity $entity) {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity repository for the related entity
     *
     * @return repository
     */
    public function get_repo() {
        return $this->repo;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function get_key() {
        return $this->key;
    }

    /**
     * Get foreign key
     *
     * @return string
     */
    public function get_foreign_key() {
        return $this->foreign_key;
    }

    /**
     * Get related model class name
     *
     * @return string
     */
    public function get_related() {
        return $this->related;
    }

    /**
     * Save new related entities
     *
     * @param entity[]|entity $children
     * @return $this
     */
    public function save($children) {
        if (!$this->can_save) {
            throw new coding_exception('This relation does not allow saving models...');
        }

        if (!($this->entity->exists() ?? false)) {
            throw new coding_exception('Your parent entity must be defined and exist...');
        }

        if (!$this->entity->has_attribute($this->get_key()) ||
            is_null($this->entity->get_attribute($this->get_key()))
        ) {
            throw new coding_exception("Entity must have '{$this->get_key()}' attribute set and be other than null");
        }

        if (!is_array($children)) {
            $children = [$children];
        }

        $collection = new collection($children);

        $collection->map(function (entity $item) {
            if (!$item instanceof $this->related) {
                throw new coding_exception("Related model must be an instance of '{$this->related}'");
            }

            $item->set_attribute($this->get_foreign_key(), $this->entity->get_attribute($this->get_key()));
            $item->save();
        });

        return $this;
    }

    /**
     * Allow access to related repository by chaining methods on the relationship
     *
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments) {
        // We attempt to call a method that doesn't exist directly on the repository,
        // If a method doesn't exist on the repository you will get an exception anyway...
        $result = $this->get_repo()->{$name}(...$arguments);

        // Let's see whether we need to return native results to the user...
        // Same logic applied for methods forwarded from repository to builder.
        if ($result !== $this->get_repo()) {
            return $result;
        }

        return $this;
    }

    /**
     * Conditionally apply constraints when results are lazy-loaded or queried on the go.
     */
    protected function apply_constraints_for_entity() {
        if (self::$apply_entity_constraints) {
            $this->constraints_for_entity();
        }
    }

    /**
     * INTERNAL
     *
     * See description below.
     *
     * Enable lazy loading constraints when instantiating the relationship.
     *
     * @param bool $yes
     */
    public static function apply_entity_constraints($yes = true) {
        self::$apply_entity_constraints = $yes;
    }

    /**
     * INTERNAL
     *
     * Disable lazy loading constraints when instantiating the relationship.
     *
     * Ok the idea here is that it is used during eager-loading relations for a collections of objects
     * since a different logic is applied there, this method is called from repository before the relations are loaded
     * as instead of applying individual entity related key condition we aggregate all possible key values from the whole
     * collection and then filtering it down for individual items. After relation is loaded the flag is returned to its
     * default state using the function above...
     */
    public static function do_not_apply_entity_constraints() {
        self::$apply_entity_constraints = false;
    }

    /**
     * INTERNAL
     *
     * Return whether lazy loading constraints are enabled
     */
    public static function should_apply_entity_constraints(): bool {
        return self::$apply_entity_constraints;
    }

    /**
     * A function to load related models for a collection which each individual relation should implement
     *
     * @param string $name Relation name to append to the model
     * @param collection $collection Collection of models to append the relation to
     * @return $this
     */
    abstract public function load_for_collection($name, collection $collection);

    /**
     * By default this relationship loads a collection, overrideable for example when you need only one result.
     *
     * @return collection
     */
    public function load_for_entity() {
        return $this->get_repo()->get();
    }

    /**
     * A function to apply constraints when loading relationships directly for a single entity
     *
     * @return $this
     */
    abstract public function constraints_for_entity();
}
