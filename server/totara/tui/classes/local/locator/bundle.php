<?php

namespace totara_tui\local\locator;

use totara_tui\output\framework;

final class bundle {

    private const SUFFIX_PRODUCTION = '';
    private const SUFFIX_PRODUCTION_LEGACY = '.legacy';
    private const SUFFIX_DEVELOPMENT = '.development';
    private const SUFFIX_DEVELOPMENT_LEGACY = '.legacy.development';

    const EXT_JS = '.js';
    const EXT_SCSS = '.scss';

    private static $instance;
    private $bundle_map = null;
    private $styles_map = null;
    private $import_map = null;

    private static $map_file_to_param = [
        self::SUFFIX_PRODUCTION => 'p',
        self::SUFFIX_PRODUCTION_LEGACY => 'pl',
        self::SUFFIX_DEVELOPMENT => 'd',
        self::SUFFIX_DEVELOPMENT_LEGACY => 'dl',
    ];

    private static $extensions = [
        self::EXT_JS,
        self::EXT_SCSS,
    ];

    public static function get_js_component_bundle(string $component): ?string {
        if (debugging() && $component !== clean_param($component, PARAM_COMPONENT)) {
            throw new \coding_exception('Invalid component provided.', $component);
        }
        $instance = self::instance();
        $bundle = $instance->find_component_bundle($component);
        if ($bundle === null || !isset($bundle[self::EXT_JS])) {
            return null;
        }
        return $bundle[self::EXT_JS];
    }

    public static function get_css_component_bundle(string $component): ?string {
        if (debugging() && $component !== clean_param($component, PARAM_SAFEDIR)) {
            throw new \coding_exception('Invalid component provided.');
        }
        $instance = self::instance();
        $bundle = $instance->find_component_bundle($component);
        if ($bundle === null || !isset($bundle[self::EXT_SCSS])) {
            return null;
        }
        return $bundle[self::EXT_SCSS];
    }

    public static function get_variables_for_component(string $component) {
        if (debugging() && $component !== clean_param($component, PARAM_SAFEDIR)) {
            throw new \coding_exception('Invalid component provided.');
        }
        $instance = self::instance();
        $instance->ensure_map_generated();
        if (!isset($instance->styles_map[$component])) {
            return null;
        }
        $key = '_variables.scss';
        foreach (self::get_css_suffix_for_file() as $suffix) {
            if (isset($instance->styles_map[$component][$suffix][$key])) {
                return $instance->styles_map[$component][$suffix][$key];
            }
        }
        return null;
    }

    public static function get_vendor_bundle(): ?string {
        $path = self::component_build_directory_client(framework::COMPONENT) . 'vendors';
        foreach (self::get_js_suffix_for_file() as $suffix) {
            $file = $path . $suffix . self::EXT_JS;
            if (file_exists($file)) {
                return $file;
            }
        }
        return null;
    }

    public static function get_style_file_in_component($component, $path) {
        $instance = self::instance();
        $instance->ensure_map_generated();
        foreach (self::get_js_suffix_for_file() as $suffix) {
            if (isset($instance->styles_map[$component][$suffix][$path])) {
                return $instance->styles_map[$component][$suffix][$path];
            }
        }
        return null;
    }

    public static function reset() {
        self::$instance = null;
    }

    private static function instance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
        // Static methods only.
    }

    private static function component_build_directory_server(string $component, string $directory_component = null) {
        if ($directory_component === null) {
            $directory_component = \core_component::get_component_directory($component);
        }
        return $directory_component . '/tui/build/';
    }

    private static function component_build_directory_client(string $component) {
        global $CFG;
        return $CFG->srcroot . '/client/build/' . $component . '/';
    }

    private function find_component_bundle(string $component): ?array {
        $this->ensure_map_generated();
        foreach (self::get_js_suffix_for_file() as $suffix) {
            if (isset($this->bundle_map[$component][$suffix])) {
                return $this->bundle_map[$component][$suffix];
            }
        }
        return null;
    }

    public static function get_js_suffix_for_url() {
        $suffixes = self::get_js_suffix_for_file();
        $suffix = reset($suffixes);
        return self::$map_file_to_param[$suffix];
    }

    private static function get_js_suffix_for_file() {
        $suffixes = [];
        $development = self::is_javascript_development();
        $legacy = \core_useragent::is_ie();

        if ($legacy) {
            if ($development) {
                $suffixes[] = self::SUFFIX_DEVELOPMENT_LEGACY;
            }
            $suffixes[] = self::SUFFIX_PRODUCTION_LEGACY;
            return $suffixes;
        }

        if ($development) {
            $suffixes[] = self::SUFFIX_DEVELOPMENT;
        }
        $suffixes[] = self::SUFFIX_PRODUCTION;

        return $suffixes;
    }

    public static function get_css_suffix_for_url() {
        $suffixes = self::get_css_suffix_for_file();
        $suffix = reset($suffixes);
        return self::$map_file_to_param[$suffix];
    }

    private static function get_css_suffix_for_file($preferred_only = false) {
        $suffixes = [];
        $development = self::is_css_development();
        $legacy = \core_useragent::is_ie();

        if ($legacy) {
            if ($development) {
                $suffixes[] = self::SUFFIX_DEVELOPMENT_LEGACY;
            }
            $suffixes[] = self::SUFFIX_PRODUCTION_LEGACY;
            return $suffixes;
        }

        if ($development) {
            $suffixes[] = self::SUFFIX_DEVELOPMENT;
        }
        $suffixes[] = self::SUFFIX_PRODUCTION;

        if ($preferred_only) {
            return reset($suffixes);
        }

        return $suffixes;
    }

    /**
     * Determine the correct JS Revision to use for this load.
     *
     * @return int the revision number to use.
     */
    public static function get_js_rev() {
        global $CFG;
        if (empty(get_config('totara_tui', 'cache_js') )) {
            return -1;
        }
        if (!empty($CFG->jsrev)) {
            return $CFG->jsrev;
        }
        return 1;
    }

    public static function get_css_rev() {
        if (empty(get_config('totara_tui', 'cache_scss'))) {
            return -1;
        }
        return theme_get_revision();
    }

    private static function is_javascript_development() {
        return (bool)get_config('totara_tui', 'development_mode');
    }

    private static function is_css_development() {
        return (bool)get_config('totara_tui', 'development_mode');
    }

    private function ensure_map_generated() {
        global $CFG;

        if ($this->bundle_map !== null) {
            return;
        }

        $this->bundle_map = [];
        $this->import_map = [];
        $this->styles_map = [];

        self::load_for_component('core', "$CFG->dirroot/lib/classes");

        foreach (\core_component::get_core_subsystems() as $subsystem => $fulldir) {
            if (!$fulldir) {
                continue;
            }
            self::load_for_component('core_'.$subsystem, $fulldir);
        }

        foreach (\core_component::get_plugin_types() as $plugintype => $plugintype_dir) {
            foreach (\core_component::get_plugin_list($plugintype) as $pluginname => $fulldir) {
                self::load_for_component($plugintype.'_'.$pluginname, $fulldir);
            }
        }
    }

    private function load_for_component(string $component, string $directory) {
        // Load client resources first. They will be overridden by server resources.
        $client_build_directory = self::component_build_directory_client($component);
        if (file_exists($client_build_directory)) {
            $this->load_bundles_for_component($component, $client_build_directory);
            $this->load_styles_for_component($component, $client_build_directory);
        }
    }

    private function load_bundles_for_component(string $component, string $directory_build) {
        $expected_files = [
            'tui_bundle',
        ];
        $filesuffixes = array_keys(self::$map_file_to_param);
        foreach ($expected_files as $expected_file) {
            foreach ($filesuffixes as $suffix) {
                foreach (self::$extensions as $extension) {
                    $candidate_file =  $directory_build . $expected_file . $suffix . $extension;
                    if (file_exists($candidate_file)) {
                        $this->bundle_map[$component][$suffix][$extension] = $candidate_file;
                    }
                }
            }
        }
    }

    private function load_styles_for_component(string $component, string $directory_build) {
        $directory_styles = $directory_build . 'styles/';
        if (!file_exists($directory_styles)) {
            return;
        }
        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory_styles),
                \RecursiveIteratorIterator::SELF_FIRST
            ),
            '/^.+\.scss$/',
            \RecursiveRegexIterator::GET_MATCH
        );
        $length_directory_styles = strlen($directory_styles);
        $filesuffixes = array_keys(self::$map_file_to_param);

        foreach ($iterator as $files_absolute) {
            foreach ($files_absolute as $file_absolute) {
                $file_relative = substr($file_absolute, $length_directory_styles);
                $file = basename($file_relative);
                $filename = substr($file, 0, -5);

                $suffixpos = strpos($filename, '.');
                if (!$suffixpos) {
                    $suffix = '';
                } else {
                    $suffix = substr($filename, $suffixpos);
                }
                $file_relative = substr($file_relative, 0, -(strlen($suffix . '.scss'))) . '.scss';
                if (!in_array($suffix, $filesuffixes)) {
                    debugging('Unexpected suffix encountered in styles directory: ' .$suffix, DEBUG_DEVELOPER);
                    continue;
                }
                $this->styles_map[$component][$suffix][$file_relative] = $file_absolute;
            }
        }
    }
}