<?php
/**
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @package editor_weka
 */
namespace editor_weka\config;

use editor_weka\extension\attachment;
use editor_weka\extension\extension;
use editor_weka\extension\link;
use editor_weka\extension\mention;
use editor_weka\extension\text;

/**
 * A class for holding configuration data.
 */
final class config_item {
    /**
     * Area default.
     * @var string
     */
    const AREA_DEFAULT = 'default';

    /**
     * Whether to use all the extensions or not.
     * @var string
     */
    const EXTENSION_ALL = 'all';

    /**
     * @var string
     */
    private $area;

    /**
     * @var string
     */
    private $component;

    /**
     * @var string[]
     */
    private $extensions;

    /**
     * @var bool
     */
    private $showtoolbar;

    /**
     * @var array
     */
    private $extensionsoptions;

    /**
     * item constructor.
     *
     * @param string $component
     * @param string $area
     */
    private function __construct(string $component, string $area) {
        $this->component = $component;
        $this->area = $area;

        $this->extensions = [];

        // Default to true.
        $this->showtoolbar = true;
        $this->extensionsoptions = [];
    }

    /**
     * @return bool
     */
    public function show_toolbar(): bool {
        return $this->showtoolbar;
    }

    /**
     * @param array $config
     * @return config_item
     */
    public static function from_array(array $config): config_item {
        if (!isset($config['area']) || !isset($config['component'])) {
            throw new \coding_exception("No value defined for property 'component' or 'area");
        }

        $instance = new static($config['component'], $config['area']);

        if (array_key_exists('includeextensions', $config)) {
            if (is_array($config['includeextensions'])) {
                $instance->extensions = $config['includeextensions'];
            } else {
                if (is_string($config['includeextensions']) && static::EXTENSION_ALL === $config['includeextensions']) {
                    // So this component-area want to use all the extensions. Therefore, we will load
                    // all the extensions from the system.

                    $extensions = array_merge(
                    // Default extension classes from the editor only.
                        [text::class, link::class, mention::class, attachment::class],
                        \core_component::get_namespace_classes(
                            'editor_weka\\extension',
                            extension::class
                        )
                    );

                    $instance->extensions = array_unique($extensions);
                } else {
                    debugging("Invalid value for key 'includeextensions'", DEBUG_DEVELOPER);
                }
            }
        }

        if (array_key_exists('showtoolbar', $config)) {
            $instance->showtoolbar = (bool) $config['showtoolbar'];
        }

        // The property 'extensionsoptions' should be a hashmap of the extension classname as the key and the value is
        // a hashmap of the options as key => value.
        $key = 'extensionsoptions';
        if (array_key_exists($key, $config) && is_array($config[$key])) {
            $parentcls = extension::class;

            foreach ($config[$key] as $extension => $options) {
                // Just to make sure that the map is correctly set.
                if (!class_exists($extension) || !is_subclass_of($extension, $parentcls)) {
                    debugging(
                        "The attribute's key of property '{$key}' array should be a proper classname " .
                        "that extends '{$parentcls}'",
                        DEBUG_DEVELOPER
                    );

                    continue;
                } else if (!is_array($options)) {
                    debugging(
                        "The options of extension '{$extension}' needs to be an array " .
                        "for component '{$instance->component}-{$instance->area}'",
                        DEBUG_DEVELOPER
                    );

                    continue;
                }

                $instance->extensionsoptions[$extension] = $options;
            }
        }

        return $instance;
    }

    /**
     * @return string[]
     */
    public function get_extensions(): array {
        return $this->extensions;
    }

    /**
     * Given that the $extensionname is the extension classname, then this function should be able to give the
     * extension options defined by component/area specific only.
     *
     * @param string $extensionname
     * @return array
     */
    public function get_options_for_extension(string $extensionname): array {
        if (!array_key_exists($extensionname, $this->extensionsoptions)) {
            return [];
        }

        return $this->extensionsoptions[$extensionname];
    }
}