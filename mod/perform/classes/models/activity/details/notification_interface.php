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

use mod_perform\models\activity\activity;

/**
 * notification interface
 */
interface notification_interface {
    /**
     * Return the parent activity.
     *
     * @return activity
     */
    public function get_activity(): activity;

    /**
     * Return the underlying notification record id or null.
     *
     * @return integer|null
     */
    public function get_id(): ?int;

    /**
     * Returns the registered class key.
     *
     * @return string
     */
    public function get_class_key(): string;

    /**
     * Return the active state.
     *
     * @return boolean
     */
    public function get_active(): bool;

    /**
     * Return the number of triggers set.
     *
     * @return integer
     */
    public function get_trigger_count(): int;

    /**
     * Return the array of trigger values.
     *
     * @return integer[] always empty
     */
    public function get_triggers(): array;

    /**
     * Return true if the underlying record exists in the database.
     *
     * @return boolean
     */
    public function exists(): bool;

    /**
     * Activate this notification setting.
     *
     * @param boolean $active
     * @return notification
     */
    public function activate(bool $active = true): notification_interface;

    /**
     * Delete the current notification setting.
     *
     * @return notification
     */
    public function delete(): notification_interface;

    /**
     * Reload the internal bookkeeping.
     *
     * @return notification
     */
    public function refresh(): notification_interface;
}
