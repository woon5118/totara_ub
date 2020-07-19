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

namespace totara_tui\output;

use totara_tui\local\locator\bundle;
use totara_tui\local\requirement;

/**
 * This class tracks requirements for TUI.
 */
final class framework implements \core\output\framework_manager {

    public const COMPONENT = 'tui';

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
     * @var requirement[] Cache of generated bundle objects.
     */
    private $bundles = null;

    private $css_urls = [];

    public static function new_instance(): \core\output\framework_manager {
        return new self();
    }

    /**
     * @param \moodle_page $page
     * @return framework
     */
    public static function get(\moodle_page $page): framework {
        /** @var framework $framework */
        $framework = $page->requires->framework(__CLASS__);
        return $framework;
    }

    /**
     * framework constructor.
     */
    public function __construct() {
    }

    /**
     * @throws \coding_exception
     */
    public function initialise(): void {
    }

    public function hook_get_head_code(\moodle_page $page, \core_renderer $renderer) {
        // Always require the theme bundle
        $this->require_vue('theme_' . $page->theme->name);

        // Include the CSS for loaded TUI components.
        $css_urls = array();
        $requirement_url_options = ['theme' => $page->theme->name];
        foreach ($this->get_bundles(requirement::TYPE_CSS) as $bundle) {
            $css_urls[] = $bundle->get_url($requirement_url_options);
        }
        $this->css_urls = $css_urls;
    }

    public function inject_css_urls(array &$urls) {
        // Add TUI SCSS bundles.
        // Find last tui_scss url. It should be the theme.
        $count = count($urls);
        for ($theme_index = $count - 1; $theme_index >= 0; $theme_index--) {
            if (strpos($urls[$theme_index], 'tui_scss')) {
                break;
            }
        }
        // Add the bundles before the theme CSS so theme CSS can override the CSS in them.
        array_splice($urls, $theme_index === -1 ? $count : $theme_index, 0, $this->css_urls);
    }

    public function inject_js_urls(array &$urls, bool $initialiseamd) {
        if (!$initialiseamd) {
            return;
        }
        foreach ($this->get_bundles('js') as $bundle) {
            $urls[] = $bundle->get_url();
        }
    }

    /**
     * @param string $name
     * @param \moodle_page|null $page
     * @return component
     */
    public static function vue($name, \moodle_page $page = null) {
        global $PAGE;
        if ($page === null) {
            $page = $PAGE;
        }
        $component = new component($name);
        $component->register($page);
        return $component;
    }

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
     * Request the TUI bundle(s) for the provided component be loaded.
     *
     * This must be called before $OUTPUT->header(), as by then the <head> will have
     * already been sent, meaning we wouldn't be able to add the CSS bundle.
     *
     * @param string $name Vue component name, e.g. 'mod_example/pages/Example'.
     */
    public function require_vue(string $name) {
        $pos = strpos($name, '/');
        if ($pos !== false) {
            $component = substr($name, 0, $pos);
        } else {
            $component = $name;
        }
        $this->require_component($component);
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
        $dependencies = bundle::get_bundle_dependencies($bundle);
        foreach ($dependencies as $dependency) {
            $this->get_final_components_visit($dependency, $bundle);
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
        if ($this->bundles !== null) {
            return $this->bundles;
        }

        $components = $this->get_final_components();
        // We only want the components.
        $components = array_values($components);

        $requirements = [];

        $requires_tui = false;
        foreach ($components as $component) {
            if ($component === self::COMPONENT) {
                $requires_tui = true;
                continue;
            }
            $requirements[] = new requirement\js($component);
            $requirements[] = new requirement\scss($component);
        }

        $this->bundles = array_filter($requirements, function(requirement $requirement) {
            return $requirement->required();
        });

        if (!empty($this->bundles) || $requires_tui) {
            array_unshift(
                $this->bundles,
                new requirement\vendors_js(),
                new requirement\js(self::COMPONENT),
                new requirement\scss(self::COMPONENT)
            );
        }

        return $this->bundles;
    }
}
