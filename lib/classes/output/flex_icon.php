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
 * @package   core
 */

namespace core\output;

use core\flex_icon_helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Class flex_icon
 *
 * Flexible icon class. Provides a flexible framework for outputting icons.
 *
 * @copyright 2015 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralms.com>
 * @package core
 */
class flex_icon implements \renderable, \templatable {

    /**
     * Regular expression to match legacy font icon identifiers.
     */
    const REGEX_LEGACY_ICON_IDENTIFIER = '/^[a-zA-Z]+(_[a-zA-Z]+)*-([a-zA-Z0-9_]+\/)*[a-zA-Z0-9_\-]+$/';

    /**
     * @var string
     */
    public $identifier = '';

    /**
     * @var string
     */
    public $theme = '';

    /**
     * @var array
     */
    protected $cache = array();

    /**
     * @var array
     */
    public $additionalclasses = array();

    /**
     * @var array
     */
    public $customdata = array();

    /**
     * flex_icon constructor.
     *
     * Create a flexible icon data structure. $identifier is
     * (in cases apart from legacy icons inherited from pix_icon())
     * a unique human-readable name which identifies the icon in the
     * global set e.g. 'cog'.
     *
     * @param string $identifier Unique identifier for the icon to be rendered.
     * @param array $customdata Optional data to be passed to the rendering (template) context.
     */
    public function __construct($identifier, $customdata = array()) {

        global $PAGE;

        $this->identifier = $identifier;
        $this->customdata = $customdata;
        $this->set_theme($PAGE->theme->name);

    }

    /**
     * Sets the theme name.
     *
     * @param string $themename
     * @return flex_icon
     */
    public function set_theme($themename) {

        $this->theme = $themename;

        return $this;

    }

    /**
     * Retrieve the template name which should be used to render $this.
     *
     * @return string
     */
    public function get_template() {

        $resolved = \core\flex_icon_helper::resolve_identifier($this->theme, $this->identifier);

        return \core\flex_icon_helper::get_template_path_by_identifier($this->theme, $resolved);

    }

    /**
     * Compile data from icons files and custom data passed in to constructor.
     *
     * @return array
     */
    protected function get_data() {

        $icondata = \core\flex_icon_helper::get_data_by_identifier($this->theme, $this->identifier);
        $icondata['customdata'] = $this->customdata;
        return $icondata;

    }

    /**
     * Implements export_for_template().
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {

        global $CFG;

        $resolved = \core\flex_icon_helper::resolve_identifier($this->theme, $this->identifier);

        if ((bool)$CFG->debugdeveloper === true) {
            $hasmap = (new flex_icon_helper())->identifier_has_map_data($this->theme, $this->identifier);

            if ($hasmap === false) {
                \debugging("Flex icon identifier '{$this->identifier}' not found in map.");
            }
        }

        return array_merge($this->get_data(), array('iconidentifier' => $resolved));

    }

    /**
     * Convert pix icon data into flex icon identifier for legacy icons.
     *
     * Note: New icons should not need to use this method - it provides a
     * 'shim' for resolving legacy pix_icon() items into flex_icon() ones.
     * As per pix_icon() if component is not provided then we default to
     * moodle core.
     *
     * @param string $pixpath e.g. t/arrow_left
     * @param string $component e.g. 'block_badges'
     * @return string
     */
    public static function legacy_identifier_from_pix_data($pixpath, $component = null) {

        // Sometimes an empty value is passed in these situations default to moodle.
        if (empty($component)) {
            $component = 'moodle';
        }

        $pixpath = self::normalize_pixpath($pixpath);
        $component = \core_component::normalize_componentname($component);

        return "{$component}-{$pixpath}";

    }

    /**
     * Check whether the given identifier is formatted as a legacy identifier.
     *
     * Legacy identifiers are in the format <component_name>-<pix/path>
     * e.g. 'totara_core-t/upload'
     *
     * @param string $identifier
     * @return bool
     */
    public static function is_legacy_identifier($identifier) {

        return (bool)preg_match(self::REGEX_LEGACY_ICON_IDENTIFIER, $identifier);

    }

    /**
     * Return customdata required to display an icon for given legacy identifier.
     *
     * Works around the fact that image-based pix-icons under certain directories
     * were expected to have specific dimensions.
     *
     * @throws \coding_exception If the legacy id is invalid.
     * @param string $legacyidentifier
     * @return array
     */
    public static function get_customdata_by_legacy_identifier($legacyidentifier) {

        if (self::is_legacy_identifier($legacyidentifier) === false) {
            throw new \coding_exception("'{$legacyidentifier}' is not a legacy identifier");
        }

        $component = explode('-', $legacyidentifier);
        $component = array_shift($component);
        $pixpath = str_replace("{$component}-", '', $legacyidentifier);
        $pathfragments = explode('/', $pixpath);

        // No directory prefix.
        if (count($pathfragments) < 2) {
            return array();
        }

        $customdata = array();

        if ($pathfragments[0] === 'i') {
            $customdata['classes'] = 'ft-size-200';
        }

        if ($pathfragments[0] === 'f') {

            $sizeclass = self::get_file_icon_size_class($legacyidentifier);
            if ($sizeclass !== '') {
                $customdata['classes'] = $sizeclass;
            }
        }

        return $customdata;

    }

    /**
     * Remove any redundant 'variant' identifiers.
     *
     * Normalise pixpath so that any legacy variants
     * which are not mapped in the new flex_icon set
     * are normalized to a single icon e.g. file
     * icons have a size suffix:
     *
     * 'f/pdf-256' will become 'f/pdf'
     *
     * @param string $pixpath
     * @return string
     */
    public static function normalize_pixpath($pixpath) {

        // File icon size suffixes.
        if (preg_match('/^f\/.+(-\d+)$/', $pixpath) === 1) {
            return preg_replace('/-\d+$/', '', $pixpath);
        }

        return $pixpath;

    }

    /**
     * Return the appropriate size class for this file icon legacy identifier.
     *
     * @param string $legacyidentifier
     * @return string
     */
    public static function get_file_icon_size_class($legacyidentifier) {

        $iconsize = explode('-', $legacyidentifier);
        $iconsize = array_pop($iconsize);

        // There are no size classes greater than 38.
        if (in_array($iconsize, array('48', '64', '72', '80', '96', '128', '256'))) {
            return 'ft-size-700';
        }

        if ($iconsize === '24') {
            return 'ft-size-400';
        }

        if ($iconsize === '32') {
            return 'ft-size-600';
        }

        return '';

    }

}
