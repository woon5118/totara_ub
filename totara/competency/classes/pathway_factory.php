<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;


/**
 * Pathway factory class to obtain an instance of the specific pathway type
 */
class pathway_factory {

    /**
     * Create a new instance of the given type.
     *
     * Checks that the type exists and is enabled.
     *
     * @param string $type
     * @return pathway of the requested type
     */
    public static function create(string $type) {
        static::require_enabled($type);

        $classname = static::get_classname($type);

        return new $classname();
    }

    /**
     * Instantiate an instance of the specified pathway type
     *
     * @param string $type Pathway type to instantiate
     * @param int $id Optional pathway instance id
     * @return pathway of the requested type
     */
    public static function fetch(string $type, int $id = null) {
        static::require_enabled($type);

        $classname = static::get_classname($type);

        return $classname::fetch($id);
    }

    /**
     * Returns the namespace that corresponds to this class.
     *
     * No checks are made to ensure the namespace exists or if the type is enabled.
     *
     * @param string $type
     * @return string The namespace of this type
     */
    public static function get_namespace(string $type): string {
        return "\\pathway_{$type}";
    }

    /**
     * @param string $type
     * @return string Namespaced name of the class corresponding to this type.
     * @throws \coding_exception if the type does not have a valid corresponding class.
     */
    public static function get_classname(string $type): string {
        $classname = static::get_namespace($type) . "\\{$type}";
        if (!class_exists($classname) || !is_subclass_of($classname, 'totara_competency\pathway')) {
            throw new \coding_exception(
                "Pathway type '{$type}' not found.",
                "Pathway type '{$type}' does not have a valid corresponding class"
            );
        }

        return $classname;
    }

    /**
     * @param string $type
     * @return void
     * @throws \coding_exception if the type is not enabled
     */
    private static function require_enabled($type) {
        $enabledtypes = plugintypes::get_enabled_plugins('pathway', 'totara_competency');
        if (!in_array($type, $enabledtypes)) {
            throw new \coding_exception(
                "Pathway type '{$type}' not found.",
                "Pathway type '{$type}' is not enabled"
            );
        }
    }

    /**
     * Retrieve the pathway data from the database and return it in an associative array
     *
     * @param string $type Pathway type to instantiate
     * @param ?int $id Pathway instance id
     * @return \stdClass | null
     */
    public static function dump_pathway_configuration(string $type, ?int $id = null) {
        $classname = static::get_classname($type);
        return $classname::dump_pathway_configuration($id);
    }
}