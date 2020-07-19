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

namespace totara_tui\local\scss;

use totara_tui\local\locator\bundle;

/**
 * Encapsulates logic for compiling TUI SCSS
 */
class scss {
    /**
     * @var scss_options
     */
    protected $options;

    /**
     * @var array Import paths for compiler.
     */
    protected $import_paths;

    /**
     * Create a new instance
     *
     * @param scss_options $options
     */
    public function __construct(scss_options $options = null) {
        $this->options = $options ?? new scss_options();
        $this->import_paths = [\Closure::fromCallable([$this, 'resolve_import'])];
    }

    /**
     * Get the options object for this instance
     *
     * @return scss_options
     */
    public function get_options(): scss_options {
        return $this->options;
    }

    /**
     * Compile SCSS for the provided Totara component.
     *
     * @param string $component Component to build CSS for.
     * @return string
     */
    public function get_compiled_css(string $component): string {
        $import_data = $this->get_imports($component);

        $legacy_var_values = [];
        if ($import_data->cssvars_legacy_imports) {
            $css_realvars_legacy = $this->compile(
                $this->build_import_code($import_data->cssvars_legacy_imports),
                false
            );
            $legacy_var_values = $this->options->get_cssvars()->get_custom_property_values($css_realvars_legacy);
        }

        $output = $this->compile(
            $this->build_import_code($import_data->imports),
            $this->options->get_sourcemap_enabled()
        );

        if ($this->options->get_legacy()) {
            $output = $this->options->get_cssvars()->transform($output, ['override_values' => $legacy_var_values]);
        }

        return $output;
    }

    /**
     * Get list of files we read to build the CSS.
     *
     * @param string $component
     * @return string[]
     */
    public function get_loaded_files(string $component): array {
        $import_data = $this->get_imports($component);
        $imports = array_merge($import_data->imports, $import_data->cssvars_legacy_imports);
        $imports = array_reduce($imports, function ($acc, $url) {
            $url_parts = explode('!', $url);
            $url = end($url_parts);
            $file = $this->resolve_import($url);
            if ($file) {
                $acc[] = $file;
            }
            return $acc;
        }, []);
        $imports = array_values(array_unique($imports));

        $loaded_files = [];
        foreach ($imports as $import) {
            $loaded_files[] = $import;
            $this->populate_loaded_files_from_file($import, $loaded_files);
        }

        return $loaded_files;
    }

    /**
     * Populate list of files that the provided file will load (recursive)
     *
     * @param string $path Path to file on filesystem
     * @param array $loaded_files Reference to loaded files array. Found files will be added here.
     */
    private function populate_loaded_files_from_file(string $path, array &$loaded_files): void {
        // scan for @imports using regex and add them to $loaded_files
        $contents = file_get_contents($path);
        if (!preg_match_all('/@import (.*?)(?:;|$)/', $contents, $import_statements)) {
            return;
        }
        foreach ($import_statements[1] as $import_statement) {
            foreach (explode(',', $import_statement) as $import) {
                if (preg_match('/^([\'"])(.*)\1$/', trim($import), $matches)) {
                    $url_parts = explode('!', $matches[2]);
                    $url = end($url_parts);
                    $loaded_file = $this->resolve_import($url);
                    if ($loaded_file && !in_array($loaded_file, $loaded_files)) {
                        $loaded_files[] = $loaded_file;
                        $this->populate_loaded_files_from_file($loaded_file, $loaded_files);
                    }
                }
            }
        }
    }

    public function get_newest_tui_css_file(string $component): int {
        return array_reduce($this->get_loaded_files($component), function ($acc, $cur) {
            return max($acc, filemtime($cur));
        }, 0);
    }

    /**
     * Build a string containing `@import`s
     *
     * @param string[] $imports List of URLs to import.
     * @return string
     */
    private function build_import_code(array $imports): string {
        return array_reduce($imports, function ($acc, $item) {
            return "{$acc}@import '{$item}';\n";
        }, '');
    }

    /**
     * Get files (URLs) to import (in the correct order) to build the CSS.
     *
     * Standard imports on $result->imports.
     * If legacy mode enabled and not compiling theme,
     * $result->cssvars_legacy_imports will contain imports needed to get theme CSS vars.
     *
     * @param string $component
     * @return object with 'imports' and 'cssvars_legacy_imports' properties
     */
    private function get_imports(string $component): object {
        /*
         * In order for the theme inheritance to work correctly with both variable types, we
         * need to separate them so they can be compiled in the correct order.
         *
         * The order is (in pseudocode):
         *
         *   if not compiling theme:
         *     add component scss vars
         *   for each theme:
         *     add theme scss vars
         *   if compiling theme:
         *     for each theme
         *       add theme css vars and css content
         *   else:
         *     add component css vars and css content
         *
         * This is because SCSS variables are evaluated as they are seen, and CSS variables
         * are evaluated lazily, and we want CSS variables defined in components to slot in
         * in the right place to be overridable by themes.
         *
         * For example, the following SCSS will make `button` green:
         * $bg: green; button { background: $bg; } $bg: blue;
         * Using CSS variables it would be blue:
         * :root { --bg: green: } button { background: var(--bg); } :root { --bg: blue; }
         *
         * For this reason we have to evaluate SCSS variables first, so that variables have
         * their final values by the time we evaluate component styles.
         *
         * We can't simply put CSS vars in the header too, because then CSS vars defined in
         * components will not be overridable by later themes.
         *
         * In legacy mode, when compiling a non-theme component we must also load CSS var
         * values from the themes to provide to the CSS var polyfill.
         *
         * When serving the resulting CSS, the order is components, then theme,
         * to allow theme vars to override component vars.
         */

        $component_files = $this->get_component_tui_scss_files($component);
        $themes_files = $this->get_themes_tui_scss();

        $imports = [];

        // SCSS vars
        $def_file_sets = array_merge([$component_files], $themes_files);

        foreach ($def_file_sets as $def_files) {
            foreach ($def_files['variables'] as $def_file) {
                $imports[] = "definitions_only!internal_absolute:" . $def_file;
            }
        }

        // CSS vars from theme for CSS vars polyfill
        $cssvars_legacy_imports = [];
        if ($this->options->get_legacy()) {
            $cssvars_legacy_imports = $imports;
            foreach ($themes_files as $theme_files) {
                foreach ($theme_files['variables'] as $theme_file) {
                    $cssvars_legacy_imports[] = "output_only!internal_absolute:" . $theme_file;
                }
            }
        }

        // CSS vars and component content
        $source_file_sets = [$component_files];
        foreach ($source_file_sets as $source_files) {
            foreach ($source_files['variables'] as $source_file) {
                $imports[] = "output_only!internal_absolute:" . $source_file;
            }
            foreach ($source_files['scss'] as $source_file) {
                $imports[] = "internal_absolute:" . $source_file;
            }
        }

        return (object)[
            'imports' => $imports,
            'cssvars_legacy_imports' => $cssvars_legacy_imports
        ];
    }

    /**
     * Compile the provided SCSS
     *
     * @param string $scss
     * @param bool $sourcemap
     * @return string
     */
    private function compile(string $scss, bool $sourcemap): string {
        $compiler = $this->options->get_compiler();
        $compiler->set_import_paths($this->import_paths);
        $compiler->set_sourcemap_enabled($sourcemap);
        return $compiler->compile($scss);
    }

    /**
     * Resolve an import URL to a file on the filesystem
     *
     * @param string $url
     * @return string|null
     */
    private function resolve_import(string $url): ?string {

        // handle "@import 'internal_absolute:/path/to/file.scss';"
        if (substr($url, 0, 18) == 'internal_absolute:') {
            return substr($url, 18);
        }

        // handle "@import 'theme_foo/name';"
        // and map to theme/foo/build/styles/_name.scss

        // ignore external requests
        if (preg_match('/^https?:\/\//', $url)) {
            return null;
        }

        $bundle = false;
        $parts = explode('/', $url, 2);
        if (count($parts) === 1) {
            $bundle = true;
            $parts[] = '';
        } if (count($parts) != 2) {
            return null;
        }

        list($component, $path) = $parts;
        unset($parts); // Don't be tempted to abuse me.

        if (substr($path, 0, 1) === '@') {
            debugging('What is this?', DEBUG_DEVELOPER);
            $path = substr($path, 1);
        }

        if ($component !== clean_param($component, PARAM_COMPONENT)) {
            throw new \coding_exception('Import does not reference a valid param', $component);
        }
        if ($path !== clean_param($path, PARAM_SAFEPATH)) {
            throw new \coding_exception('Import does not reference a valid path', $path);
        }

        if ($bundle) {
            return bundle::get_css_component_bundle($component);
        }

        $bundle = bundle::get_style_file_in_component($component, $path . '.scss');
        if ($bundle) {
            return $bundle;
        }
        $altpath = preg_replace('/[^\/]+$/', '_\0', $path);
        return bundle::get_style_file_in_component($component, $altpath . '.scss');
    }

    /**
     * Get an array of the TUI SCSS files provided by each theme.
     *
     * @return array[]
     */
    private function get_themes_tui_scss(): array {
        return array_map(function ($theme) {
            return $this->get_component_tui_scss_files("theme_{$theme}");
        }, $this->options->get_themes());
    }

    /**
     * Get the TUI SCSS files provided by a component.
     *
     * @param string $component
     * @return array With keys 'scss' and 'variables' containing paths to files for each category if they exist.
     */
    protected function get_component_tui_scss_files(string $component): array {
        $cssfiles = [
            'scss' => [],
            'variables' => [],
        ];
        $bundle = bundle::get_css_component_bundle($component);
        if ($bundle) {
            $cssfiles['scss']['bundle'] = $bundle;
        }
        $bundle = bundle::get_variables_for_component($component);
        if ($bundle) {
            $cssfiles['variables']['_variables'] = $bundle;
        }
        return $cssfiles;
    }
}
