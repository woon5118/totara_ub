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

namespace mod_perform\notification;

use coding_exception;

/**
 * Provides the master clock.
 */
final class clock {
    /** @var integer */
    private $bias = 0;

    /**
     * Constructor.
     */
    public function __construct() {
        if ((defined('PHPUNIT_TEST') && PHPUNIT_TEST) || defined('BEHAT_UTIL') || defined('BEHAT_TEST') || defined('BEHAT_SITE_RUNNING')) {
            $this->bias = get_config('mod_perform', 'notification_time_travel') ?: 0;
        }
    }

    /**
     * Get the current time stamp.
     *
     * @return integer
     */
    public function get_time(): int {
        return time() + $this->bias;
    }
}
