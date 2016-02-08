<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @copyright 2015 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralms.com>>
 * @package   core
 */

namespace core;

defined('MOODLE_INTERNAL') || die();

/**
 * Flex Icon helper class
 *
 * @copyright 2015 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralms.com>>
 * @package   core
 */
class flex_icon_helper {

    /**
     * @var string
     */
    const FLEX_ICON_MAP_FILENAME = 'icons.json';


    /**
     * @var string
     */
    const FLEX_ICON_CACHE_FILENAME = 'flex_icons.php';


    /**
     * @var string
     */
    const FLEX_ICON_CACHE_FUNCTIONNAME = 'icons_cache';

    /**
     * Return an array of paths to potential icons.json files for a given theme.
     *
     * The returned array is in order of precedence highest first to lowest last.
     *
     * @param \theme_config[] $themeconfigs Theme configuration files.
     * @return string[]
     */
    public static function get_file_candidate_paths(array $themeconfigs) {

        $candidates = array_map(function($configitem) {
            return $configitem->dir . '/' . self::FLEX_ICON_MAP_FILENAME;
        }, $themeconfigs);

        $candidates[] = self::get_core_map_path();

        return $candidates;

    }

    /**
     * Return path to the core icons.json file.
     *
     * @return string
     */
    public static function get_core_map_path() {
        global $CFG;
        return $CFG->libdir . '/db/' . self::FLEX_ICON_MAP_FILENAME;
    }

    /**
     * Return path to cache file for a given theme.
     *
     * @param string $themename
     * @return string
     */
    public static function get_cache_file_path($themename) {
        global $CFG;
        $revision = theme_get_revision();
        $cachedir = "{$CFG->localcachedir}/theme/{$revision}/{$themename}";
        return $cachedir . '/' . self::FLEX_ICON_CACHE_FILENAME;
    }

    /**
     * Helper to read icons file and parse JSON into PHP data structure.
     *
     * At the time of development the built-in PHP function
     * json_decode() fails silently if JavaScript is not well
     * formed. We need to throw an error here instead so the
     * developer knows they must take action to correct it.
     *
     * @throws \coding_exception If the JSON file cannot be read or parsed
     *
     * @param string $filepath Path to the JSON file.
     * @return array
     */
    public static function parse_json_file($filepath) {

        // Check the file is readable.
        if (!is_readable($filepath)) {
            throw new \coding_exception("Unable to find JSON file.", $filepath);
        }
        // On failure file_get_contents raises an error
        // with level E_WARNING which cannot be caught
        // and is therefore difficult to test until PHP7
        // when errors are converted to Exceptions.
        $jsonstring = @file_get_contents($filepath);

        // Throw an Exception so we can test for failure.
        if ($jsonstring === false) {
            throw new \coding_exception("Unable to read JSON file.", $filepath);
        }

        $jsondata = json_decode($jsonstring, true);

        // The content could not be parsed.
        if ($jsondata === null) {
            throw new \coding_exception("Unable to parse JSON in file.", $filepath);
        }

        return $jsondata;

    }

    /**
     * Generate the fully qualified cache function name for a given theme.
     *
     * @param string $themename
     * @return string
     */
    public static function get_fully_qualified_cache_functionname($themename) {
        $functionname = self::FLEX_ICON_CACHE_FUNCTIONNAME;
        return "theme_{$themename}\\{$functionname}";
    }

    /**
     * Resolve translation section and add map entries.
     *
     * Returns a new array in which the translation section
     * has been removed and entries for all translations are
     * added to the map section.
     *
     * Note: This method should only be called on a given
     * $iconsdata array which has already been merged with
     * all possible parents as missing translations will
     * cause an error to be thrown.
     *
     * @throws \coding_exception if map data is not passed correctly when called recursively.
     *
     * @param array $iconsdata
     * @return array
     */
    public static function flatten_translations(array $iconsdata) {
        // No translations to resolve.
        if (!array_key_exists('translation', $iconsdata)) {
            return $iconsdata;
        }

        foreach ($iconsdata['translation'] as $identifierfrom => $identifierto) {
            $resolvedidentifier = self::resolve_identifier_using_translationarray($identifierfrom, $iconsdata['translation']);

            if (!isset($iconsdata['map'][$resolvedidentifier])) {
                $message = "No map data for resolved identifier '{$resolvedidentifier}'";
                throw new \coding_exception($message);
            }

            $iconsdata['map'][$identifierfrom] = $iconsdata['map'][$resolvedidentifier];
            $iconsdata['map'][$identifierfrom]['translatesto'] = $identifierto;
        }
        unset($iconsdata['translation']);
        return $iconsdata;
    }

    /**
     * Resolve an identifier if it has been translated in a given array.
     *
     * Due to the declarative nature of the cached icons file
     * it's possible for an identifier to be mapped to another
     * which in turn is mapped to a different one. This type
     * of usage is not encouraged.
     *
     * @throws \coding_exception if a circular reference is found in icon translation.
     *
     * @param string $identifier Icon identifier
     * @param array $translationarray Array mapping identifiers to their
     *                                'canonical' identifier for rendering e.g.
     *                                't/import' => 'download
     *                                ...means we should output the 'download'
     *                                icon when 't/import' is used.
     * @return string
     */
    public static function resolve_identifier_using_translationarray($identifier, $translationarray) {
        // A class level cache for this isn't necessary
        // as we only need to store data during recursion.
        static $parents = array();

        if (in_array($identifier, $parents)) {
            throw new \coding_exception("Circular reference '{$identifier}' in icon translation");
        }

        if (array_key_exists($identifier, $translationarray)) {
            $parents[] = $identifier;
            return self::resolve_identifier_using_translationarray($translationarray[$identifier], $translationarray);
        }

        // Reset static variable for next time.
        $parents = array();

        return $identifier;
    }

    /**
     * Resolves a given identifier using icon translation array (if it exists).
     *
     * @param string $themename
     * @param string $identifier
     * @return string
     */
    public static function resolve_identifier($themename, $identifier) {

        $iconscache = self::get_cache($themename);
        $translationarray = isset($iconscache['translation']) ? $iconscache['translation'] : array();

        return self::resolve_identifier_using_translationarray($identifier, $translationarray);

    }

    /**
     * Build flex icon cache file content for given theme.
     *
     * Recurse through parent theme hierarchy and core icon data
     * to resolve data and template for every icon. This method
     * should only be called when building the cache file for
     * performance reasons.
     *
     * @param string $themename
     * @return array
     */
    public static function build_cache_file_data($themename) {

        $candidateiconsfiles = \theme_config::flex_icon_get_file_candidate_paths($themename);

        // Only keep filepaths which have a corresponding file.
        $iconsfilepaths = array_filter($candidateiconsfiles, 'file_exists');
        $iconsfilepaths = array_reverse($iconsfilepaths);

        // Iterate through icons.json files and reduce into a single structure
        // by merging icons data. Filepaths are in order of least to highest precedence.
        $cachedata = array_reduce($iconsfilepaths, function($carry, $filepath) {
            $overrides = self::parse_json_file($filepath);
            return self::merge_icons_data($carry, $overrides);
        }, array());

        $cachedata = self::flatten_translations($cachedata);

        return $cachedata;

    }

    /**
     * Return the icon cache for a given theme.
     *
     * Retrieve the flex icon data from the cache file in current theme cache.
     * Builds the file if it doesn't exist.
     *
     * @param string $themename
     * @return array
     */
    public static function get_cache($themename) {

        global $CFG;

        static $caches = array();

        if (array_key_exists($themename, $caches)) {
            return $caches[$themename];
        }

        // We store the cache inside a function so that we
        // can take advantage of namespacing to prevent any
        // collisions. A class for this purpose seems overkill.
        $functionname = \core\flex_icon_helper::get_fully_qualified_cache_functionname($themename);
        $cachefilepath = \core\flex_icon_helper::get_cache_file_path($themename);

        if (file_exists($cachefilepath)) {
            require_once($cachefilepath);
            return $caches[$themename] = call_user_func($functionname);
        }

        // Cache file doesn't yet exist.
        make_localcache_directory('theme', false);
        @mkdir(dirname($cachefilepath), $CFG->directorypermissions, true);

        // Prevent client disconnect from causing incomplete cache file.
        ignore_user_abort(true);

        $cachedata = self::build_cache_file_data($themename);
        $cachecontent = self::get_cache_file_content_from_data($themename, $cachedata);

        file_put_contents($cachefilepath, $cachecontent);

        return self::get_cache($themename);

    }

    /**
     * Convert generated cache data into a string ready to write to a file.
     *
     * We wrap the cache data in a function rather than storing in a global variable so
     * that we can take advantage of namespacing to prevent collisions.
     *
     * @param string $themename
     * @param array $cachedata Array of cache data as returned by build_cache_file_data().
     * @return string
     */
    public static function get_cache_file_content_from_data($themename, $cachedata) {

        $namespace = "theme_{$themename}";
        $functionname = self::FLEX_ICON_CACHE_FUNCTIONNAME;

        $content  = "<?php\n\n";
        $content .= "namespace {$namespace};\n\n";
        $content .= "function {$functionname}() {\n";
        $content .= '   return ';
        $content .= var_export($cachedata, true);
        $content .= ";\n}";

        return $content;

    }

    /**
     * Merges two arrays (parsed icons.json) respecting overrides.
     *
     * Merges two PHP data structures based on parsed icons.json files
     * ensuring that values present in both are correctly overridden.
     *
     * @param array $currentdata
     * @param array $overridedata
     * @return array
     */
    public static function merge_icons_data($currentdata, $overridedata) {

        $merged = $currentdata;
        $overriddenkeys = array_keys($overridedata);

        foreach ($overriddenkeys as $key) {
            $current = array_key_exists($key, $currentdata) ? $currentdata[$key] : array();
            $merged[$key] = array_merge($current, $overridedata[$key]);
        }

        return $merged;

    }


    /**
     * Return the template name for rendering a given icon.
     *
     * Resolves a given identifier e.g. 'cog' to a template name as
     * expected by core_renderer\render_from_template(). As it is
     * quite possible multiple instances of the same icon will be rendered
     * on a given page we store results in a static cache.
     *
     * @throws \coding_exception If the icon template is not found.
     * @param string $themename Name of the theme to get icon data from.
     * @param string $identifier Resolved identifier for the icon to be rendered.
     * @return string
     */
    public static function get_template_path_by_identifier($themename, $identifier) {

        $iconscache = self::get_cache($themename);

        if (isset($iconscache['map'][$identifier]['template'])) {
            return $iconscache['map'][$identifier]['template'];
        }

        if (isset($iconscache['defaults']['template'])) {
            return $iconscache['defaults']['template'];
        }

        throw new \coding_exception("Icon template for '{$identifier}' not found.");

    }


    /**
     * Retrieve data associated with given Flex Icon.
     *
     * @param string $themename
     * @param string $identifier Flex Icon identifier.
     * @return array
     */
    public static function get_data_by_identifier($themename, $identifier) {

        $cache = self::get_cache($themename);

        $defaults = array();
        $mapdata = array();

        if (isset($cache['defaults']['data'])) {
            $defaults = $cache['defaults']['data'];
        }

        if (isset($cache['map'][$identifier]['data'])) {
            $mapdata = $cache['map'][$identifier]['data'];
        }

        return array_merge($defaults, $mapdata);

    }

    /**
     * Does the identifier have a cache file map entry?
     *
     * This method is primarily intended for use in the
     * output method pix_icon() to discover whether or not
     * to render as a flex_icon.
     *
     * @param string $themename
     * @param string $identifier Flex icon identifier.
     * @return bool
     */
    public static function identifier_has_map_data($themename, $identifier) {

        static $cache = array();

        $cachekey = "{$themename}__{$identifier}";

        if (array_key_exists($cachekey, $cache)) {
            return $cache[$cachekey];
        }

        $themecache = self::get_cache($themename);

        return $cache[$cachekey] = isset($themecache['map'][$identifier]);

    }

    /**
     * Whether to replace a given pix icon with a flexible icon.
     *
     * Checks if there is an available replacement for the
     * given identifier. We expect an identifier formatted as
     * returned by core\output\flex_icon::legacy_identifier_from_pix_data().
     *
     * @param string $themename
     * @param string $identifier Flex icon identifier.
     * @return bool
     */
    public static function flex_icon_should_replace_pix_icon($themename, $identifier) {

        static $cache = array();

        $cachekey = "{$themename}__{$identifier}";

        if (array_key_exists($cachekey, $cache)) {
            return $cache[$cachekey];
        }

        // Malformed identifier.
        if (\core\output\flex_icon::is_legacy_identifier($identifier) === false) {
            return $cache[$cachekey] = false;
        }

        // Fall back to pix_icon if there is no data.
        if (self::identifier_has_map_data($themename, $identifier) === false) {
            return $cache[$cachekey] = false;
        }

        return $cache[$cachekey] = true;

    }

}
