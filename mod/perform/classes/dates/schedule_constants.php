<?php
/*
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\dates;

use coding_exception;

abstract class schedule_constants {
    public const BEFORE = 'BEFORE';
    public const AFTER = 'AFTER';

    public const DAY = 'DAY';
    public const WEEK = 'WEEK';
    public const MONTH = 'MONTH';

    public const ONE_PER_SUBJECT = 'ONE_PER_SUBJECT';
    public const ONE_PER_JOB = 'ONE_PER_JOB';

    /**
     * Ensure the supplied direction is valid for use in the mod_perform/dates namespace.
     *
     * @param string $direction
     */
    public static function validate_direction(string $direction): void {
        if (!in_array($direction, [self::BEFORE, self::AFTER], true)) {
            throw new coding_exception(sprintf('Invalid direction %s', $direction));
        }
    }

    /**
     * Ensure the supplied unit is valid for use in the mod_perform/dates namespace.
     *
     * @param string $unit
     */
    public static function validate_unit(string $unit): void {
        if (!in_array($unit, [self::DAY, self::WEEK, self::MONTH])) {
            throw new coding_exception(sprintf('Invalid unit %s', $unit));
        }
    }

    /**
     * Ensure the supplied subject instance generation method is valid for use in the mod_perform/dates namespace.
     *
     * @param string $method
     */
    public static function validate_method(string $method): void {
        if (!in_array($method, [self::ONE_PER_SUBJECT, self::ONE_PER_JOB])) {
            throw new coding_exception(sprintf('Invalid method %s', $method));
        }
    }
}