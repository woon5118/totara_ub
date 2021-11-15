<?php
/*
 * This file is part of Totara LMS
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
 * @package core
 */

namespace core\json_editor\node;

use core\json_editor\formatter\formatter;

/**
 * Base class for all the node.
 * The data structure of a node can be something like below
 *
 * node => [
 *      'type' => 'string',
 *      'content' => [node, node],
 *      'attrs' => [
 *          ...mixed
 *      ],
 *      'marks' => [
 *          'type' => 'string,
 *          'attrs' => [
 *              ...mixed
 *          ]
 *      ]
 * ]
 *
 * To provide the json data sample for your own node, please put it in the fixtures directory of
 * your component - where you are introducing the new node. Please note that it has to be under
 * subdirectory json_editor/node with the same filename as your own node.
 *
 * For example, if you introduced new node 'special_node' type located in your component with the directory path as
 * '/root-project/your/component/classes/json_editor/node/special_node.php'. Then the sample json data for your node
 * can be located in '/root-project/your/component/tests/fixtures/json_editor/node/special_node.php'.
 *
 * By default, every node will provide a rendered content in HTML and also in text. Whether to use the front-end
 * components to display the node differently is up to the formatter.
 */
abstract class node {
    /**
     * node constructor.
     *
     * Forcing all the children to have a simple constructor. To construct your children class, better
     * to extend or call to function {@see node::from_node()}
     */
    final protected function __construct() {
    }

    /**
     * @param array $node
     * @return node
     */
    public static function from_node(array $node): node {
        $expected = static::get_type();
        if (!array_key_exists('type', $node) || $expected !== $node['type']) {
            throw new \coding_exception("Cannot create a node that is not being matched by type with each other");
        }

        return new static();
    }

    /**
     * This will concat the component/plugin name with the defined type from children
     * @return string
     */
    final public static function get_type(): string {
        $cls = static::class;
        $parts = explode("\\", $cls);

        $first = reset($parts);
        $cleaned = clean_param($first, PARAM_COMPONENT);

        if (null == $cleaned || $cleaned !== $first) {
            throw new \coding_exception("Invalid component name ({$first}) found for node '{$cls}'");
        }

        [$plugintype, $pluginname] = \core_component::normalize_component($first);
        $type = static::do_get_type();

        if ('core' === $plugintype && null === $pluginname) {
            // For core, the type is alright to not be prefixed.
            return $type;
        }

        // However, for the non-core plugin, the type of node will be prefixed with the component name.
        return "{$plugintype}_{$pluginname}_{$type}";
    }

    /**
     * Format to html text.
     *
     * It is the responsibility of this method to return safe HTML. clean_text
     * will not be run on it as that would remove TUI component data attributes.
     *
     * @param formatter $formatter
     * @return string
     */
    abstract public function to_html(formatter $formatter): string;

    /**
     * Format to a plain text.
     *
     * @param formatter $formatter
     * @return string
     */
    abstract public function to_text(formatter $formatter): string;

    /**
     * Metadata about this node, which it will be used for the schema. This function should only return the type
     * name of the node with out prefixing. The prefix will be added via {@see node::get_type()}.
     *
     * @return string
     */
    abstract protected static function do_get_type(): string;

    /**
     * This is where the schema validation happening. The validation should be about
     * whether the node structure is correctly implemented or not.
     *
     * It is also worth to check if the $raw_node has additional properties that the node implementation
     * does not expect it to be in the data.
     *
     * Prior to this function called, the attribute `type` is mandatory for the node itself in order to let
     * the system find the right node data handler for it, hence the implementation of the validation does NOT
     * need to re-validate on `type` attribute.
     *
     * Note: DO NOT DEPEND THIS FUNCTION ON DATABASE OR ANYTHING THAT DEPENDING ON THE STATE OF SYSTEM
     * AFTER INSTALLED - IT MUST BE PURELY PROGRAMMATICALLY AND INDENPENDENT.
     *
     * @param array $raw_node
     * @return bool
     */
    abstract public static function validate_schema(array $raw_node): bool;

    /**
     * Cleaning your raw node data, this is where all the data fix is happening, strip out nasty html tags - would be.
     *
     * Note: DO NOT DEPEND THIS FUNCTION ON DATABASE OR ANYTHING THAT DEPENDING ON THE STATE OF SYSTEM
     * AFTER INSTALLED - IT MUST BE PURELY PROGRAMMATICALLY AND INDENPENDENT.
     *
     * At the point where this function is called, the data should had been gone thru the schema validation.
     * Otherwise the error will be yield, and this function will be much fragile.
     *
     * Either returned a cleaned raw node - or null, if there is any invalid data.
     *
     * @param array $raw_node
     * @return array|null
     */
    public static function clean_raw_node(array $raw_node): ?array {
        // All the node MUST have a property `type` at least.
        if (!isset($raw_node['type'])) {
            throw new \coding_exception("Invalid node structure", static::get_type());
        }

        $type = $raw_node['type'];
        $cleaned_type = clean_param($type, PARAM_ALPHAEXT);

        if ($type !== $cleaned_type) {
            // Invalid type.
            return null;
        }

        return $raw_node;
    }

    /**
     * @deprecated since Totara 13.8
     * @param array $raw_node
     * @return array
     */
    public static function sanitize_raw_node(array $raw_node): array {
        if (!isset($raw_node['type'])) {
            throw new \coding_exception("Invalid node structure", static::get_type());
        }

        $raw_node['type'] = clean_string($raw_node['type']);
        return $raw_node;
    }
}