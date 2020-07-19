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
use mod_perform\notification\broker;
use mod_perform\notification\factory;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\details\notification_interface;

/**
 * The internal implementation that represents a non-existent performance notification setting.
 */
final class notification_sparse implements notification_interface {
    /** @var string */
    private $class_key;

    /** @var activity */
    private $activity;

    /** @var broker */
    private $broker;

    /**
     * @param activity $activity
     * @param string $class_key
     */
    public function __construct(activity $activity, string $class_key) {
        $this->activity = $activity;
        $this->class_key = $class_key;
        $this->broker = factory::create_broker($class_key);
    }

    /**
     * @inheritDoc
     */
    public function get_activity(): activity {
        return $this->activity;
    }

    /**
     * {@inheritDoc}
     *
     * NOTE: the function always returns null.
     */
    public function get_id(): ?int {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function get_class_key(): string {
        return $this->class_key;
    }

    /**
     * {@inheritDoc}
     *
     * NOTE: the function always returns false.
     */
    public function get_active(): bool {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function get_triggers(): array {
        return $this->broker->get_default_triggers();
    }

    /**
     * {@inheritDoc}
     *
     * NOTE: the function always returns false.
     */
    public function exists(): bool {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function activate(bool $active = true): notification_interface {
        $inst = notification_real::create($this->activity, $this->class_key, $active);
        return $inst;
    }

    /**
     * @inheritDoc
     */
    public function set_triggers(array $values): notification_interface {
        throw new coding_exception('not available');
    }

    /**
     * {@inheritDoc}
     *
     * NOTE: the function does nothing.
     */
    public function delete(): notification_interface {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function refresh(): notification_interface {
        $this->activity->refresh();
        return $this;
    }
}
