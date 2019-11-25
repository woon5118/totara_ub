<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package editor_weka
 */

namespace editor_weka\extension;

/**
 * An extension is  a collection of nodes of json_editor. Don't get mixed up between the extension and the node,
 * as node is just single unit, where as the extension can be either a single unit or multiple units. However, the
 * extension in this very context is to tell the weka editor which front-end component to be pulled, so that
 * at the front-end the editor can be constructed properly.
 */
abstract class extension {
    /**
     * The component where this extension is being used.
     * @var string|null
     */
    protected $component;

    /**
     * The area where this extension is being used.
     * @var string|null
     */
    protected $area;

    /**
     * The context id where this extension is being used.
     * If contextid is not being passed, then system context will be used.
     * @var int
     */
    protected $contextid;

    /**
     * extension constructor.
     * Preventing any complicated construction of the children.
     *
     * @param string|null $component    The component metadata where this extension is being used.
     * @param string|null $area         The area metadata where this extension is being used.
     * @param int|null    $contextid    The context where this extension is being used
     */
    final public function __construct(?string $component, ?string $area,
                                      ?int $contextid = null) {
        if (null == $contextid) {
            // Default to context system id.
            $contextid = \context_system::instance()->id;
        }

        $this->component = $component;
        $this->area = $area;
        $this->contextid = $contextid;
    }

    /**
     * @return string
     */
    abstract public function get_js_path(): string;

    /**
     * Let the children override this function. Basically, that an extension should be configured
     * via configuration files.
     *
     * @param array $options
     * @return void
     */
    public function set_options(array $options): void {
    }

    /**
     * Returning the array of dependencies that the extensions in the front-end need.
     * @return array
     */
    public function get_js_parameters(): array {
        return [];
    }

    /**
     * @return string
     */
    final public function get_extension_name(): string {
        $cls = get_called_class();
        $parts = explode("\\", $cls);

        $first = reset($parts);
        $last = end($parts);

        if ('editor_weka' === $first) {
            // If it is from editor_weka, then just return the extension name,
            // without any prefixing.
            return $last;
        }

        // Otherwise, prefixing the component in, as we do want prevent the chance for extensions
        // to get duplicated.
        return "{$first}_{$last}";
    }
}