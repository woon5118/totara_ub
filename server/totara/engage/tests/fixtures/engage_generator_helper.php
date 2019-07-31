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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_engage
 */
use totara_engage\generator\engage_generator;

/**
 * Generator helper for engage site.
 */
final class engage_generator_helper {
    /**
     * Please note that this function will not run any check on environment. The check should
     * be done by the upstream where this function is being called.
     *
     * @return engage_generator[]
     */
    public static function get_generators(): array {
        global $CFG;
        require_once("{$CFG->dirroot}/lib/testing/classes/util.php");

        $gen = \testing_util::get_data_generator();
        $types = \core_component::get_plugin_types();

        $generators = [];

        foreach ($types as $type => $location) {
            $plugins = \core_component::get_plugin_list($type);

            foreach ($plugins as $plugin => $plugin_location) {
                $component = "{$type}_{$plugin}";
                try {
                    $generator = $gen->get_plugin_generator($component);

                    if ($generator instanceof engage_generator) {
                        $generators[$component] = $generator;
                    }
                } catch (\coding_exception $e) {
                    continue;
                }
            }
        }

        return $generators;
    }
}
