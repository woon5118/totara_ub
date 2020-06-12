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

namespace mod_perform\models\activity;

use coding_exception;
use core\orm\collection;
use core\orm\entity\model;
use mod_perform\entities\activity\notification as notification_entity;
use mod_perform\notification\factory;

/**
 * Represents a single performance notification setting.
 *
 * @property-read integer $id ID
 * @property string $name
 * @property boolean $active is active?
 * @property integer $trigger_count
 * @property integer[] $triggers
 *
 * @property-read activity $activity
 * @property-read notification_recipient[] $recipients
 */
class notification extends model {

    protected $entity_attribute_whitelist = [
        'id',
    ];

    protected $model_accessor_whitelist = [
        'active',
        'activity',
        'name',
        'recipients',
        'trigger_count',
        'triggers',
    ];

    /**
     * @var notification_entity
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    public static function get_entity_class(): string {
        return notification_entity::class;
    }

    /**
     * @return collection|notification_recipient[]
     */
    public function get_recipients(): collection {
        return $this->entity->recipients->map_to(notification_recipient::class);
    }

    /**
     * @return activity
     */
    public function get_activity(): activity {
        return new activity($this->entity->activity);
    }

    /**
     * @return boolean
     */
    public function get_active(): bool {
        return $this->entity->active;
    }

    /**
     * Return the localised name of this notification setting.
     *
     * @return string
     */
    public function get_name(): string {
        return factory::create_loader()->get_name_of($this->entity->class_key);
    }

    /**
     * Return the number of triggers set.
     *
     * @return integer
     */
    public function get_trigger_count(): int {
        $triggers = json_decode($this->entity->triggers);
        if (!is_array($triggers)) {
            return 0;
        }
        return count($triggers);
    }

    /**
     * Return the array of trigger values.
     *
     * @return integer[]
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
     * Activate this notification setting.
     *
     * @param boolean $active
     * @return self
     */
    public function activate(bool $active = true): self {
        if (!$this->entity->exists()) {
            throw new coding_exception('activate() is called after the entity is deleted');
        }
        $this->entity->active = $active;
        $this->entity->save();
        return $this;
    }

    /**
     * Create a new notification setting.
     *
     * @param activity $parent
     * @param string $class_key
     * @return self
     */
    public static function create(activity $parent, string $class_key): self {
        $broker = factory::create_broker($class_key);
        $entity = new notification_entity();
        $entity->activity_id = $parent->get_id();
        $entity->class_key = $class_key;
        $entity->triggers = json_encode($broker->get_default_triggers(), JSON_UNESCAPED_SLASHES);
        $entity->save();
        $inst = new self($entity);
        return $inst;
    }

    /**
     * Delete the current notification setting.
     */
    public function delete(): void {
        $this->entity->delete();
    }

    /**
     * Return true if the notification can provide trigger events.
     *
     * @return boolean
     */
    public function can_be_triggered(): bool {
        return factory::create_loader()->has_triggers($this->entity->class_key);
    }

    /**
     * Reload the internal bookkeeping.
     *
     * @return self
     */
    public function refresh(): self {
        $this->entity->refresh();
        $this->entity->load_relation('triggers');

        return $this;
    }
}
