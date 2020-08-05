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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity\details;

use coding_exception;
use core\orm\collection;
use core\orm\entity\model;
use core\orm\query\exceptions\record_not_found_exception;
use mod_perform\entities\activity\notification as notification_entity;
use mod_perform\models\activity\activity;
use mod_perform\notification\factory;

/**
 * The internal implementation that represents an existing performance notification setting.
 */
final class notification_real extends model implements notification_interface {

    /**
     * @inheritDoc
     */
    public function get_activity(): activity {
        return activity::load_by_id($this->entity->activity_id);
    }

    /**
     * @inheritDoc
     */
    public function get_class_key(): string {
        return $this->entity->class_key;
    }

    /**
     * @inheritDoc
     */
    public function get_active(): bool {
        return $this->entity->active;
    }

    /**
     * @inheritDoc
     */
    public function get_triggers(): array {
        $triggers = json_decode($this->entity->triggers);
        if (!is_array($triggers)) {
            $triggers = [];
        }
        return array_map(function ($value) {
            return (int)$value;
        }, $triggers);
    }

    /**
     * @inheritDoc
     */
    public function get_last_run_at(): int {
        return $this->entity->last_run_at ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool {
        return $this->entity->exists();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
        $methodname = 'get_'.$name;
        if (method_exists($this, $methodname)) {
            return $this->{$methodname}();
        }
        return parent::__get($name);
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function has_attribute(string $name): bool {
        $methodname = 'get_'.$name;
        if (method_exists($this, $methodname)) {
            return true;
        }
        return parent::has_attribute($name);
    }

    /**
     * Retrieves notifications by their parent activity.
     *
     * @param activity $parent parent activity
     * @return collection retrieved notifications
     */
    public static function load_by_activity(activity $parent): collection {
        return notification_entity::repository()
            ->where('activity_id', $parent->get_id())
            ->get()
            ->map_to(function ($item) {
                return new self($item);
            });
    }

    /**
     * Retrieves notifications by their parent activity.
     *
     * @param activity $parent parent activity
     * @param string $class_key
     * @param boolean $strict set true to throw an exception
     * @return self|null
     */
    public static function load_by_activity_and_class_key(activity $parent, string $class_key, bool $strict = false): ?self {
        $entity = notification_entity::repository()
            ->where('activity_id', $parent->get_id())
            ->where('class_key', $class_key)
            ->one($strict);

        if (!$entity) {
            return null;
        }
        return new self($entity);
    }

    /**
     * Instantiate a class based on an existing notification setting.
     *
     * @param integer $notification_id
     * @return self
     * @throws record_not_found_exception
     */
    public static function load_by_id(int $notification_id): self {
        /** @var notification_entity $entity */
        $entity = notification_entity::repository()->find_or_fail($notification_id);
        return new self($entity);
    }

    /**
     * Create a new notification setting.
     *
     * @param activity $parent
     * @param string $class_key
     * @param boolean $active
     * @return self
     */
    public static function create(activity $parent, string $class_key, bool $active): self {
        $broker = factory::create_broker($class_key);
        $entity = new notification_entity();
        $entity->activity_id = $parent->get_id();
        $entity->class_key = $class_key;
        $entity->active = $active;
        $entity->triggers = json_encode($broker->get_default_triggers(), JSON_UNESCAPED_SLASHES);
        $entity->save();
        $inst = new self($entity);
        return $inst;
    }

    /**
     * @inheritDoc
     */
    public function activate(bool $active = true): notification_interface {
        if (!$this->entity->exists()) {
            throw new coding_exception('activate() is called after the entity is deleted');
        }
        $this->entity->active = $active;
        $this->entity->save();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set_triggers(array $values): notification_interface {
        $this->entity->triggers = json_encode($values, JSON_UNESCAPED_SLASHES);
        $this->entity->save();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): notification_interface {
        $inst = new notification_sparse($this->get_activity(), $this->entity->class_key);
        $this->entity->delete();
        return $inst;
    }

    /**
     * @inheritDoc
     */
    public function refresh(): notification_interface {
        $this->entity->refresh();
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return notification_entity::class;
    }
}
