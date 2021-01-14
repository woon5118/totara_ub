<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package compatibility
 */

/**
 * This class adds compatibility functions for older PHP versions and can be removed once PHP 7.4 support is dropped.
 *
 * For more information see:
 *  - https://www.php.net/releases/8.0/en.php
 *  - https://php.watch/versions/8.0
 */

if (!function_exists('str_contains')) {
    /**
     * Checks if $needle is found in $haystack and returns a boolean value
     * (true/false) whether or not the $needle was found.
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     * @since Totara 13.4
     */
    function str_contains(string $haystack, string $needle): bool {
        return ($needle === '' || strpos($haystack, $needle) !== false);
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * The function returns true if the passed $haystack starts from the
     * $needle string or false otherwise.
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     * @since Totara 13.4
     */
    function str_starts_with(string $haystack, string $needle): bool {
        return ($needle === '' || substr($haystack, 0, strlen($needle) === $needle));
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * The function returns true if the passed $haystack ends with the
     * $needle string or false otherwise.
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     * @since Totara 13.4
     */
    function str_ends_with(string $haystack, string $needle): bool {
        return ($needle === '' || substr($haystack, -strlen($needle) === $needle));
    }
}
