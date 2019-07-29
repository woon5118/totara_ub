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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package core
 */

namespace core\tui;

/**
 * This class tracks requirements for TUI.
 */
class requirements {
    /**
     * @var string[] List of Totara components to load TUI bundles for.
     */
    private $components = [];

    /**
     * @var array Map of Totara components to their state in the sort.
     */
    private $final_component_state;

    /**
     * @var string[] Final sorted component list.
     */
    private $final_components;

    /**
     * @var object[] Cache of generated bundle objects.
     */
    private $bundles;

    /**
     * Request the TUI bundle(s) for the provided component be loaded.
     *
     * @param string $component Totara component
     */
    public function require_component(string $component) {
        if (!$component) {
            throw new \coding_exception('component is required');
        }
        if (in_array($component, $this->components)) {
            return;
        }

        // Invalidate cache.
        $this->final_components = null;
        $this->bundles = null;

        $this->components[] = $component;
    }

    /**
     * Get the full list of components to load TUI bundles for, including dependencies.
     *
     * @return string[]
     */
    public function get_final_components(): array {
        if ($this->final_components !== null) {
            return $this->final_components;
        }

        // figure out what order to load the component bundles in
        // this algorithm is a variant of the depth-first topological sort:
        // https://en.wikipedia.org/wiki/Topological_sorting

        $this->final_component_state = [];
        $this->final_components = [];

        foreach ($this->components as $bundle) {
            $this->get_final_components_visit($bundle, '(direct)');
        }

        return $this->final_components;
    }

    /**
     * Visit component for topological sort.
     *
     * @param string $node
     * @param string $reqby
     * @param object $ctx
     */
    private function get_final_components_visit(string $bundle, string $reqby) {
        if (isset($this->final_component_state[$bundle])) {
            if ($this->final_component_state[$bundle] === true) {
                // already processsed
                return;
            } else {
                // if state is false we are already processing this dependency,
                // so there must be a circular dependency in the graph
                throw new \coding_exception("Circular dependency in TUI bundle \"{$bundle}\" requested by \"${reqby}\"");
            }
        }

        // mark the bundle as processing
        $this->final_component_state[$bundle] = false;

        // process every dependency
        $deps = \core_component::get_tui_dependencies($bundle);
        if ($deps !== null) {
            foreach ($deps as $dependency) {
                $this->get_final_components_visit($dependency, $bundle);
            }
        }

        // mark the bundle as processed
        $this->final_component_state[$bundle] = true;

        // all of our dependencies, and their dependencies, and so on have been added at this point,
        // so add ourselves
        $this->final_components[] = $bundle;
    }

    /**
     * Get details of each bundle we need to load.
     *
     * @param string|null $type Filter to type of bundle, e.g. 'js' or 'css'. Null for all.
     * @return requirement[]
     */
    public function get_bundles($type = null): array {
        $bundles = $this->get_bundles_internal();
        if ($type !== null) {
            $filtered = [];
            foreach ($bundles as $bundle) {
                if ($bundle->get_type() == $type) {
                    $filtered[] = $bundle;
                }
            }
            return $filtered;
        }
        return $bundles;
    }

    /**
     * Internal implementation of get_bundles()
     *
     * @return requirement[]
     */
    private function get_bundles_internal() {
        global $CFG;

        if ($this->bundles !== null) {
            return $this->bundles;
        }

        $components = $this->get_final_components();

        $result = [];
        $suffix = \core_useragent::is_ie() ? '.legacy' : '';
        // special-case totara_core to also load vendor bundle
        if (in_array('totara_core', $components)) {
            $path = core_output_choose_build_file("/totara/core/tui/build/vendors{$suffix}.js", $CFG->dirroot);
            if ($path) {
                $result[] = new requirement_js('totara_core', 'vendors.js', $path);
            }
            $path = core_output_choose_build_file("/totara/core/tui/build/tui_bundle{$suffix}.js", $CFG->dirroot);
            if ($path) {
                $result[] = new requirement_js('totara_core', 'tui_bundle.js', $path);
            }
        }

        foreach ($components as $component) {
            if ($component === 'totara_core') {
                continue;
            }
            $dir = \core_component::get_component_directory($component);
            if ($dir) {
                // strip dirroot
                if (substr($dir, 0, strlen($CFG->dirroot)) === $CFG->dirroot) {
                    $dir = substr($dir, strlen($CFG->dirroot));
                }
                $path = core_output_choose_build_file("{$dir}/tui/build/tui_bundle{$suffix}.js", $CFG->dirroot);
                if ($path) {
                    $result[] = new requirement_js($component, 'tui_bundle.js', $path);
                }
            }
        }

        $this->bundles = $result;

        return $result;
    }
}
