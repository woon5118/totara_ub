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
 * @author    Joby Harding <joby.harding@totaralms.com>
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 * @package   core
 */

namespace core\output;

use \pix_icon;

defined('MOODLE_INTERNAL') || die();

/**
 * Flexible icon class. Provides a flexible framework for outputting icons via fonts.
 *
 * @copyright 2015 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralms.com>
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 * @package   core
 */
class flex_icon implements \renderable, \templatable {
    /**
     * @var string The name of the flex icon
     */
    public $identifier;

    /**
     * @var array Custom data for icon instance
     */
    public $customdata;

    /**
     * Create a flexible icon data structure using one of identifier
     * defined in one of pix/flex_icons.php files.
     *
     * @param string $identifier icon identifier, ex: 'edit', 'mod_book|icon'
     * @param array $customdata Optional data to be passed to the rendering (template) context.
     */
    public function __construct($identifier, array $customdata = null) {
        $this->identifier = (string)$identifier;
        $this->customdata = (array)$customdata;

        if (!self::exists($this->identifier)) {
            debugging("Flex icon '{$this->identifier}' not found", DEBUG_DEVELOPER);
        }
    }

    /**
     * Retrieve the template name which should be used to render this icon.
     *
     * @return string
     */
    public function get_template() {
        global $PAGE;
        return flex_icon_helper::get_template_by_identifier($PAGE->theme->name, $this->identifier);
    }

    /**
     * Export data to be used as the context for a mustache template to render this icon.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;
        $icondata = flex_icon_helper::get_data_by_identifier($PAGE->theme->name, $this->identifier);
        $icondata['customdata'] = $this->customdata;
        return $icondata;
    }

    /**
     * Does a flex icon with this identifier exist?
     *
     * @param string $identifier Flex icon identifier.
     * @return bool
     */
    public static function exists($identifier) {
        global $PAGE, $CFG;
        // We can use any theme here because we load all flex icons
        // from all plugins and core to build the cached map.
        $theme = $CFG->theme;
        if (isset($PAGE->theme->name)) {
            $theme = $PAGE->theme->name;
        }
        $icons = flex_icon_helper::get_icons($theme);
        return isset($icons[$identifier]);
    }

    /**
     * Create a flex icon from legacy pix_icon if possible.
     *
     * @param pix_icon $icon
     * @param string|array $customclasses list of custom classes added to flex icon
     * @return flex_icon|null returns null if flex matching flex icon cannot be found
     */
    public static function create_from_pix_icon(pix_icon $icon, $customclasses = null) {
        $flexidentifier = self::get_identifier_from_pix_icon($icon);

        if (!self::exists($flexidentifier)) {
            return null;
        }

        $customdata = self::get_customdata_from_pix_icon($icon);

        if (isset($customclasses)) {
            self::add_class_to_customdata($customdata, $customclasses);

        } else {
            // Try to guess if we should apply some known classes.
            if (!empty($icon->attributes['class'])) {
                if (strpos($icon->attributes['class'], 'activityicon') !== false) {
                    self::add_class_to_customdata($customdata, 'activityicon');
                }
            }
        }

        if (isset($icon->attributes['alt'])) {
            $customdata['alt'] = $icon->attributes['alt'];
        }

        return new flex_icon($flexidentifier, $customdata);
    }

    /**
     * Add classes to custom data.
     *
     * @param array $customdata
     * @param string|array $classes the CSS class or classes to be added to $customdata['classes']
     * @return void $customdata['classes'] is modified
     */
    protected static function add_class_to_customdata(&$customdata, $classes) {
        if (is_array($classes)) {
            $classes = implode(' ', $classes);
        }
        $classes = trim($classes);
        if ($classes === '') {
            return;
        }
        if (!isset($customdata['classes']) or trim($customdata['classes']) === '') {
            $customdata['classes'] = $classes;
        } else {
            $customdata['classes'] .= ' ' . $classes;
        }
    }

    /**
     * Convert pix icon into expected flex icon identifier format.
     *
     * @param pix_icon $icon
     * @return string
     */
    protected static function get_identifier_from_pix_icon(pix_icon $icon) {
        $pixpath = $icon->pix;

        // Remove the size suffix if present - 'f/pdf-256' will become 'f/pdf'.
        if (preg_match('/^f\/.+(-\d+)$/', $pixpath) === 1) {
            $pixpath =  preg_replace('/-\d+$/', '', $pixpath);
        }

        // Cast to string before normalisation because it might be null.
        $component = \core_component::normalize_componentname((string)$icon->component);

        return "{$component}|{$pixpath}";
    }

    /**
     * Return custom data required to display pix icon.
     *
     * Works around the fact that image-based pix-icons under certain directories
     * were expected to have specific over-sized dimensions.
     *
     * This is not intended to deal with 12x12 or 16x16 sizes,
     * those are handled using normal font sizes of texts where icons are used.
     *
     * @param pix_icon $icon
     * @return array
     */
    protected static function get_customdata_from_pix_icon(pix_icon $icon) {
        $customdata = array();
        $pixpath = $icon->pix;

        if (strpos($pixpath, '/') === 0) {
            return $customdata;
        }

        if (preg_match('#f/.+-(\d+)$#', $pixpath, $matches)) {
            $iconsize = $matches[1];
            if ($iconsize > 32) {
                $customdata['classes'] = 'ft-size-700';
            } else if ($iconsize > 24) {
                $customdata['classes'] = 'ft-size-600';
            } else if ($iconsize == 24) {
                $customdata['classes'] = 'ft-size-400';
            }
        }

        return $customdata;
    }
}
