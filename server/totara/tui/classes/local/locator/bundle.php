<?php

namespace totara_tui\local\locator;

use totara_tui\output\framework;

final class bundle {

    private const SUFFIX_PRODUCTION = '';
    private const SUFFIX_PRODUCTION_LEGACY = '.legacy';
    private const SUFFIX_DEVELOPMENT = '.development';
    private const SUFFIX_DEVELOPMENT_LEGACY = '.legacy.development';

    /**
     * The directory containing loadable bundles, relative to $CFG->srcroot
     */
    private const SOURCE_DIRECTORY = '/client/build';

    private static $instance;
    private $map_initialised = false;
    private $map_bundle_js = [];
    private $map_bundle_scss = [];
    private $map_scss_imports = [];
    private $map_vendors_js = null;
    private $map_bundle_dependencies = [];

    private static $map_file_to_param = [
        self::SUFFIX_PRODUCTION => 'p',
        self::SUFFIX_PRODUCTION_LEGACY => 'pl',
        self::SUFFIX_DEVELOPMENT => 'd',
        self::SUFFIX_DEVELOPMENT_LEGACY => 'dl',
    ];

    public static function get_bundle_js_file(string $bundle): ?string {
        return self::instance()->resolve_bundle_js($bundle);
    }

    public static function get_bundle_css_file(string $bundle): ?string {
        return self::instance()->resolve_bundle_scss($bundle);
    }

    public static function get_bundle_css_variables_file(string $bundle) {
        return self::instance()->resolve_style_import($bundle, '_variables.scss');
    }

    public static function get_vendors_file(): ?string {
        return self::instance()->resolve_vendor_js();
    }

    public static function get_style_scss_file($bundle, $importpath): ?string {
        return self::instance()->resolve_style_import($bundle, $importpath);
    }

    public static function get_bundle_dependencies($bundle): array {
        return self::instance()->resolve_bundle_dependencies($bundle);
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

    private static function anticipated_bundle_location(string $bundle): string {
        global $CFG;
        if ($bundle !== clean_param($bundle, PARAM_SAFEDIR)) {
            throw new \coding_exception('Invalid bundle name provided.');
        }
        return $CFG->srcroot . self::SOURCE_DIRECTORY . DIRECTORY_SEPARATOR . $bundle;
    }

    private function resolve_bundle_js(string $bundle): ?string {
        $this->ensure_map_generated();
        foreach (self::get_js_suffix_for_file() as $suffix) {
            if (isset($this->map_bundle_js[$bundle][$suffix])) {
                return $this->map_bundle_js[$bundle][$suffix];
            }
        }
        return null;
    }

    private function resolve_bundle_scss(string $bundle): ?string {
        $this->ensure_map_generated();
        foreach (self::get_css_suffix_for_file() as $suffix) {
            if (isset($this->map_bundle_scss[$bundle][$suffix])) {
                return $this->map_bundle_scss[$bundle][$suffix];
            }
        }
        return null;
    }

    private function resolve_vendor_js(): ?string {
        $this->ensure_map_generated();
        foreach (self::get_js_suffix_for_file() as $suffix) {
            if (isset($this->map_vendors_js[$suffix])) {
                return $this->map_vendors_js[$suffix];
            }
        }
        return null;
    }

    private function resolve_style_import(string $bundle, string $import) {
        $this->ensure_map_generated();
        foreach (self::get_css_suffix_for_file() as $suffix) {
            if (isset($this->map_scss_imports[$bundle][$suffix][$import])) {
                return $this->map_scss_imports[$bundle][$suffix][$import];
            }
        }
        return null;
    }

    private function resolve_bundle_dependencies(string $bundle): array {
        $this->ensure_map_generated();
        if (isset($this->map_bundle_dependencies[$bundle])) {
            return $this->map_bundle_dependencies[$bundle];
        }
        return [];
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

        if ($this->map_initialised) {
            return;
        }
        $this->map_initialised = true;

        $bundles = [];
        $suffixes = [];
        $suffixes_imports = [

        ];
        foreach (array_keys(self::$map_file_to_param) as $suffix) {
            $suffixes[$suffix] = null;
            $suffixes_imports[$suffix] = [];
        }

        $directory = $CFG->srcroot . self::SOURCE_DIRECTORY;
        if (!is_readable($directory) || !is_dir($directory)) {
            throw new \coding_exception('Unable to read bundle directory');
        }
        $directory_iterator = new \DirectoryIterator($directory);
        foreach ($directory_iterator as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isDot() || !$file->isDir()) {
                continue;
            }
            $bundles[] = $file->getFilename();
        }

        foreach ($bundles as $bundle) {
            $this->map_bundle_js[$bundle] = $suffixes;
            $this->map_bundle_scss[$bundle] = $suffixes;
            $this->map_scss_imports[$bundle] = $suffixes_imports;
            $this->map_bundle_dependencies[$bundle] = [];
            $this->map_bundle($bundle);
        }
    }

    private function map_bundle(string $bundle) {
        $imports = [];
        foreach (array_keys(self::$map_file_to_param) as $suffix) {
            $imports[$suffix] = [];
        }

        $directory_build = self::anticipated_bundle_location($bundle);
        if (!file_exists($directory_build) || !is_readable($directory_build)) {
            return $imports;
        }

        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory_build),
                \RecursiveIteratorIterator::SELF_FIRST
            ),
            '/^.+?(?<suffix>(.legacy)?\.development)?(?<extension>\.(scss|js|json))$/',
            \RecursiveRegexIterator::GET_MATCH
        );
        $length_directory_styles = strlen($directory_build) + 1;

        foreach ($iterator as $fileinfo) {
            $file_absolute = $fileinfo[0];
            $file_extension = $fileinfo['extension'];
            $file_suffix = $fileinfo['suffix'];
            $file_basename = basename($file_absolute);
            $file_relative = substr($file_absolute, $length_directory_styles, -(strlen($file_suffix . $file_extension))) . $file_extension;

            switch ($file_extension) {
                case '.scss':
                    if (strpos($file_relative, 'styles/') === 0) {
                        $file_relative = substr($file_relative, strlen('styles/'));
                        $this->map_scss_imports[$bundle][$file_suffix][$file_relative] = $file_absolute;
                    } else if ($file_relative === 'tui_bundle.scss') {
                        $this->map_bundle_scss[$bundle][$file_suffix] = $file_absolute;
                    } else {
                        debugging('Unable to map SCSS file ' . $file_absolute, DEBUG_DEVELOPER);
                    }
                    break;
                case '.js':
                    if ($bundle === framework::COMPONENT && $file_relative === 'vendors.js') {
                        $this->map_vendors_js[$file_suffix] = $file_absolute;
                        break;
                    }
                    $this->map_bundle_js[$bundle][$file_suffix] = $file_absolute;
                    break;
                case '.json':
                    if ($file_relative === 'dependencies.json') {
                        $this->map_bundle_dependencies[$bundle] = [];
                        $json = file_get_contents($file_absolute);
                        $data = @json_decode($json);
                        foreach ($data->dependencies as $dependency) {
                            $this->map_bundle_dependencies[$bundle][] = $dependency->name;
                        }
                    }
                    break;
            }
        }
    }
}