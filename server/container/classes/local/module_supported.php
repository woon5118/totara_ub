<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package core_container
 */
namespace core_container\local;

use core_container\hook\module_supported_in_container;

/**
 * A singleton to get the module supported within a container.
 */
final class module_supported {
    /**
     * @var module_supported
     */
    private static $instance;

    /**
     * An array of mod names
     * @param string[]
     */
    private $modnames;

    /**
     * An array of mod names that are being supported within container
     * @var array
     */
    private $modsincontainer;

    /**
     * @return module_supported
     */
    public static function instance(): module_supported {
        if (null == static::$instance) {
            static::$instance = new static();

            static::$instance->modnames = [];
            static::$instance->modsincontainer = [];
        }

        return static::$instance;
    }

    /**
     * Preload the modules from table {modules} and setup its name with both plural and non-plural.
     * @return void
     */
    private function setup_modnames(): void {
        global $DB, $CFG;

        if (empty($this->modnames)) {
            $this->modnames = [];
            $allmods = $DB->get_records('modules', null, '', 'id, name, visible');

            foreach ($allmods as $mod) {
                $file = "{$CFG->dirroot}/mod/{$mod->name}/lib.php";
                if (file_exists($file)) {
                    $this->modnames[] = $mod->name;
                }
            }
        }
    }

    /**
     * @param string $containertype
     * @param bool   $plural
     *
     * @return array
     */
    public function get_for_container(string $containertype, bool $plural = false): array {
        if (!array_key_exists($containertype, $this->modsincontainer)) {
            $this->setup_modnames();

            $hook = new module_supported_in_container($containertype, $this->modnames);
            $hook->execute();

            $this->modsincontainer[$containertype] = $hook->get_mods();
        }

        $mods = $this->modsincontainer[$containertype];

        $identifier = "modulename";
        if ($plural) {
            $identifier = "modulenameplural";
        }

        $modules = [];
        foreach ($mods as $modname) {
            $modules[$modname] = get_string($identifier, "mod_{$modname}");
        }

        \core_collator::asort($modules);
        return $modules;
    }
}