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
 * @author Carl Anderson <carl.anderson@totaralearning.com>
 * @package totara_reportbuilder
 */

namespace totara_reportbuilder\local\graph\settings;

/**
 * Class to translate user settings defined in JSON to a format compatible with a charting library
 *
 * @package totara_reportbuilder\local\graph\settings
 */
abstract class base {

    /**
     * Return chart settings based on user-provided settings object
     * @param $settings
     * @return array
     */
    abstract public static function create(array $settings): array;

    /**
     * Matches the user settings with a translation object to produce a list of settings
     * @param $key string object key to check
     * @param $input array user settings
     * @param $settings array settings translation
     * @return array translated list of settings
     */
    protected static function match(string $key, array $input, array $settings): array {
        if (!isset($input[$key])) {
            return [];
        }

        $setting = $settings[$key];
        $value = $input[$key];

        //If the setting for this key isn't an array, then we should just take whatever the value is
        if (!is_array($setting)) {
            return [$setting => $value];
        }

        // If we've hit an option where the input value isn't an array, but the setting is, that means we've hit a
        // shortcut option, and need to take the default
        if (!is_array($value)) {
            if (isset($setting['_default'])) {
                $val = $setting['_default'];

                // If the default is several values, then loop
                if (is_array($val)) {
                    $rtn = [];
                    foreach ($val as $key) {
                        $rtn[$key] = $value;
                    }

                    return $rtn;
                }

                return [$val => $value];
            } else {
                $values = [];
                foreach ($setting as $v) {
                    $values[$v] = $value;
                }
                return $values;
            }
        }

        // If both options are arrays, we need to go deeper
        $keys = array_keys($setting);
        $output = [];
        foreach ($keys as $option) {
            if (isset($value[$option])) {
                //Flatten this, and add it to this object
                $child = self::match($option, $value, $setting);
                $output = array_merge($output, $child);
            }
        }
        return $output;
    }
}
