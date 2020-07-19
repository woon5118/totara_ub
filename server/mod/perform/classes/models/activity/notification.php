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
use core\orm\query\exceptions\record_not_found_exception;
use mod_perform\notification\factory;
use mod_perform\models\activity\details\notification_interface as notification_interface;
use mod_perform\models\activity\details\notification_real;
use mod_perform\models\activity\details\notification_sparse;

/**
 * A proxy class that represents a single performance notification setting.
 *
 * @property-read integer $id ID
 * @property string $name
 * @property string $class_key
 * @property boolean $active is active?
 * @property integer $trigger_type
 *
 * @property-read activity $activity
 * @property-read collection|notification_recipient[] $recipients
 * @property-read integer[] $triggers
 */
final class notification implements notification_interface {
    /** @var notification_interface */
    protected $current;

    /**
     * Private constructor.
     *
     * @param notification_interface $input
     */
    private function __construct(notification_interface $input) {
        if (!($input instanceof notification_real) && !($input instanceof notification_sparse)) {
            throw new coding_exception('invalid instance passed');
        }
        $this->current = $input;
    }

    /**
     * Create a new notification setting in the database.
     *
     * @param activity $parent
     * @param string $class_key
     * @param boolean $active
     * @return self
     */
    public static function create(activity $parent, string $class_key, bool $active = false): self {
        return new self(notification_real::create($parent, $class_key, $active));
    }

    /**
     * Load all notifications by the activity.
     *
     * @param activity $activity
     * @return collection|notification[]
     */
    public static function load_all_by_activity(activity $activity): collection {
        $loader = factory::create_loader();
        $classes = $loader->get_classes();
        $models = notification_real::load_by_activity($activity);
        $results = new collection();
        foreach ($classes as $class_key => $unused) {
            $model = $models->find('class_key', $class_key)
                   ?? new notification_sparse($activity, $class_key);
            $results->append(new self($model));
        }
        return $results;
    }

    /**
     * Load an instance by the activity and the class key.
     *
     * @param activity $activity
     * @param string $class_key
     * @return self
     */
    public static function load_by_activity_and_class_key(activity $activity, string $class_key): self {
        factory::create_loader()->ensure_class_key_exists($class_key);
        $model = notification_real::load_by_activity_and_class_key($activity, $class_key, false)
            ?? new notification_sparse($activity, $class_key);
        return new self($model);
    }

    /**
     * Load an instance by the notification id.
     *
     * @param integer $id
     * @return self
     * @throws record_not_found_exception
     */
    public static function load_by_id(int $id): self {
        return new self(notification_real::load_by_id($id, true));
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
        $methodname = 'get_'.$name;
        if (!method_exists($this, $methodname)) {
            throw new coding_exception('unknown property: '.$name);
        }
        return $this->{$methodname}();
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function has_attribute(string $name): bool {
        $methodname = 'get_'.$name;
        return method_exists($this, $methodname);
    }

    /**
     * Get all recipients.
     *
     * @param boolean $active_only get only active recipients
     * @return collection|notification_recipient[]
     */
    public function get_recipients(bool $active_only = false): collection {
        return notification_recipient::load_by_notification($this, $active_only);
    }

    /**
     * @inheritDoc
     */
    public function get_activity(): activity {
        return $this->current->get_activity();
    }

    /**
     * @inheritDoc
     */
    public function get_id(): ?int {
        return $this->current->get_id();
    }

    /**
     * @inheritDoc
     */
    public function get_class_key(): string {
        return $this->current->get_class_key();
    }

    /**
     * @inheritDoc
     */
    public function get_active(): bool {
        return $this->current->get_active();
    }

    /**
     * @inheritDoc
     */
    public function get_triggers(): array {
        $trigger = factory::create_trigger($this);
        return $trigger->translate_outgoing($this->current->get_triggers());
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool {
        return !empty($this->get_id());
    }

    /**
     * @inheritDoc
     */
    public function activate(bool $active = true): notification_interface {
        $this->current = $this->current->activate($active);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set_triggers(array $triggers): notification_interface {
        $trigger = factory::create_trigger($this);
        $triggers = $trigger->translate_incoming($triggers);
        $this->current = $this->current->set_triggers($triggers);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(): notification_interface {
        $this->current = $this->current->delete();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function refresh(): notification_interface {
        $this->current = $this->current->refresh();
        return $this;
    }

    /**
     * Return the localised name of this notification setting.
     *
     * @return string
     */
    public function get_name(): string {
        return factory::create_loader()->get_name_of($this->get_class_key());
    }

    /**
     * Return the trigger type.
     *
     * @return integer one of trigger_type constants
     */
    public function get_trigger_type(): int {
        $loader = factory::create_loader();
        return $loader->get_trigger_type_of($this->current->get_class_key());
    }

    /**
     * Return true if the notification can provide trigger events.
     *
     * @return boolean
     */
    public function can_be_triggered(): bool {
        return factory::create_loader()->support_triggers($this->class_key);
    }
}
