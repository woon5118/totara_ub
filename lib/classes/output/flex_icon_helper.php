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
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 * @package   core
 */

namespace core\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Flex Icon helper class.
 *
 * This class is expected to be used from the flex_icon
 * and internal stuff only, this is not part of public API!
 *
 * @copyright 2015 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralms.com>>
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 * @package   core
 */
class flex_icon_helper {
    const MISSING_ICON = 'flex-icon-missing';

    /**
     * Resolve translation, deprecated, defaults and map
     * to create a list of all icons.
     *
     * @param array $iconsdata
     * @return array all flex icon definitions
     */
    protected static function resolve_translations(array $iconsdata) {
        // Apply defaults to map.
        if ($iconsdata['defaults']) {
            $template = 'core/flex_icon';
            if (!empty($iconsdata['defaults']['template'])) {
                $template = $iconsdata['defaults']['template'];
            }
            $data = array();
            if (!empty($iconsdata['defaults']['data'])) {
                $data = $iconsdata['defaults']['data'];
            }
            foreach ($iconsdata['map'] as $k => $item) {
                if (empty($item['template'])) {
                    $item['template'] = $template;
                }
                if (!isset($item['data'])) {
                    $item['data'] = array();
                }
                if ($data) {
                    $item['data'] = array_merge($data, $item['data']);
                }
                $iconsdata['map'][$k] = $item;
            }
        }

        // Verify the translations first.
        foreach ($iconsdata['translations'] as $identifierfrom => $identifierto) {
            if (isset($iconsdata['map'][$identifierfrom])) {
                // Translation cannot override map, do not show debug warning because
                // theme might force these for some reason.
                unset($iconsdata['translations'][$identifierfrom]);
                continue;
            }
            if (!isset($iconsdata['map'][$identifierto])) {
                debugging("Flex icon translation $identifierfrom points to non-existent $identifierto map entry", DEBUG_DEVELOPER);
                unset($iconsdata['translations'][$identifierfrom]);
                continue;
            }
        }

        // Map the translations and remember what we did.
        foreach ($iconsdata['translations'] as $identifierfrom => $identifierto) {
            $iconsdata['map'][$identifierfrom] = $iconsdata['map'][$identifierto];
            $iconsdata['map'][$identifierfrom]['translatesto'] = $identifierto;
        }

        // Add deprecated stuff.
        foreach ($iconsdata['deprecated'] as $identifierfrom => $identifierto) {
            if (isset($iconsdata['map'][$identifierfrom])) {
                // Valid map already exists.
                continue;
            }
            if (!isset($iconsdata['map'][$identifierto])) {
                // Nothing to map to.
                continue;
            }
            if (!empty($iconsdata['map'][$identifierto]['translatesto'])) {
                // Always link the original.
                $identifierto = $iconsdata['map'][$identifierto]['translatesto'];
            }
            $iconsdata['map'][$identifierfrom] = $iconsdata['map'][$identifierto];
            $iconsdata['map'][$identifierfrom]['translatesto'] = $identifierto;
            $iconsdata['map'][$identifierfrom]['deprecated'] = true;
        }

        return $iconsdata['map'];
    }

    public static function get_ajax_data($themename) {
        $icons = self::get_icons($themename);

        $templates = array();
        $ti = 0;

        $datas = array();
        $di = 0;

        foreach ($icons as $identifier => $desc) {
            if (!empty($desc['translatesto'])) {
                continue;
            }
            $icon = array();

            $template = $desc['template'];
            if (!isset($templates[$template])) {
                $templates[$template] = $ti;
                $ti++;
            }
            $icon[0] = $templates[$template];

            $datas[$di] = $desc['data'];
            $icon[1] = $di;
            $di++;

            $icons[$identifier] = $icon;
        }

        foreach ($icons as $identifier => $desc) {
            if (empty($desc['translatesto'])) {
                continue;
            }
            $icons[$identifier] = $icons[$desc['translatesto']];
        }

        return array(
            'templates' => array_flip($templates),
            'datas' => $datas,
            'icons' => $icons,
        );
    }

    /**
     * Get the list of icon definitions.
     *
     * Recurse through parent theme hierarchy and core icon data
     * to resolve data and template for every icon. This method
     * should only be called when building the cache file for
     * performance reasons.
     *
     * @param string $themename
     * @return array
     */
    public static function get_icons($themename) {
        global $CFG;

        $themename = clean_param($themename, PARAM_THEME);
        if (!$themename) {
            // We do not want any failures in here, always return something valid.
            $themename = $CFG->theme;
        }

        $cache = \cache::make('totara_core', 'flex_icons');
        $cached = $cache->get($themename);
        if ($cached) {
            return $cached;
        }

        $flexiconsfile = '/pix/flex_icons.php';
        $iconsdata = array(
            'translations' => array(),
            'deprecated' => array(),
            'defaults' => array(),
            'map' => array(),
        );

        // Load all plugins in the standard order.
        $plugintypes = \core_component::get_plugin_types();
        foreach ($plugintypes as $type => $unused) {
            $plugs = \core_component::get_plugin_list($type);
            foreach ($plugs as $name => $location) {
                $iconsdata = self::merge_flex_icons_file($location . $flexiconsfile, $iconsdata);
            }
        }

        // Load core translation and map.
        $iconsdata = self::merge_flex_icons_file($CFG->dirroot. $flexiconsfile, $iconsdata);

        // Then parent theme and at the very end load the current theme.
        $theme = \theme_config::load($themename);
        $candidatedirs = $theme->get_flex_icon_candidate_dirs();
        foreach ($candidatedirs as $candidatedir) {
            $iconsdata = self::merge_flex_icons_file($candidatedir . $flexiconsfile, $iconsdata);
        }

        $iconsmap = self::resolve_translations($iconsdata);
        $cache->set($themename, $iconsmap);
        return $iconsmap;
    }

    /**
     * Merge individual flex_icon.php files.
     *
     * @param string $file
     * @param array $iconsdata
     * @return array the new icons data
     */
    protected static function merge_flex_icons_file($file, $iconsdata) {
        if (!file_exists($file)) {
            return $iconsdata;
        }

        $translations = array();
        $deprecated = array();
        $defaults = array();
        $map = array();
        require($file);

        if ($deprecated) {
            $iconsdata['deprecated'] = array_merge($iconsdata['deprecated'], $deprecated);
        }
        if ($translations) {
            $iconsdata['translations'] = array_merge($iconsdata['translations'], $translations);
        }
        if ($defaults) {
            $iconsdata['defaults'] = array_merge($iconsdata['defaults'], $defaults);
        }
        if ($map) {
            $iconsdata['map'] = array_merge($iconsdata['map'], $map);
        }
        return $iconsdata;
    }

    /**
     * Return the template name for rendering a given flex icon.
     *
     * @param string $themename Name of the theme to get icon data from.
     * @param string $identifier Resolved identifier for the icon to be rendered.
     * @return string
     */
    public static function get_template_by_identifier($themename, $identifier) {
        $iconsmap = self::get_icons($themename);
        if (!isset($iconsmap[$identifier])) {
            $identifier = self::MISSING_ICON;
        }
        return $iconsmap[$identifier]['template'];
    }

    /**
     * Retrieve data associated with given Flex Icon.
     *
     * @param string $themename
     * @param string $identifier Flex Icon identifier.
     * @return array
     */
    public static function get_data_by_identifier($themename, $identifier) {
        $iconsmap = self::get_icons($themename);
        if (!isset($iconsmap[$identifier])) {
            $identifier = self::MISSING_ICON;
        }
        return $iconsmap[$identifier]['data'];
    }
}
