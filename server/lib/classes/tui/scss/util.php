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

namespace core\tui\scss;

use ScssPhp\ScssPhp\Block;
use ScssPhp\ScssPhp\Type;

class util {
    /**
     * @var int Special return value to tell traverse() to remove the current node from the tree.
     */
    public const TRAVERSE_REMOVE_NODE = -1;

    /**
     * Add transform to the path part of an import.
     *
     * @param array $raw_path Path part of import (i.e. $node[1] where $node[0] === 'import')
     * @param string $transform Name of transform to add.
     * @return array Modified path, or null if it's not a path we care about.
     */
    public static function add_transform_to_import_path(array $raw_path, string $transform): ?array {
        // @import "x";
        //         ^^^
        if ($raw_path[0] === Type::T_STRING) {
            $path = self::get_string_content($raw_path);
            // bail out if import is not a plain string
            if ($path === null) {
                return null;
            }

            // don't modify url imports
            if (substr($path, 0, 4) === 'http') {
                return null;
            }

            // skip adding transform if it is already present
            $transforms = explode('!', $path);
            array_pop($transforms); // remove file path
            if (in_array($transform, $transforms)) {
                return null;
            }

            $path = $transform . '!' . $path;
            return [Type::T_STRING, '"', [$path]];
        }

        // @import "x", "y";
        //         ^^^^^^^^
        if ($raw_path[0] === Type::T_LIST) {
            foreach ($raw_path[2] as $path) {
                if ($path[0] !== Type::T_STRING) {
                    return null;
                }
            }

            // @import "x", "y";
            //         ^^^  ^^^
            foreach ($raw_path[2] as &$path) {
                $changed_path = self::add_transform_to_import_path($path, $transform);
                if ($changed_path !== null) {
                    $path = $changed_path;
                }
            }

            return $raw_path;
        }

        return null;
    }

    /**
     * Get the content of a string node.
     *
     * @param array $node String node ($node[0] === Type::T_STRING)
     * @return string|null Content, or null if it was not a plain string.
     */
    public static function get_string_content(array $node): ?string {
        $parts = [];

        foreach ($node[2] as $part) {
            if (is_array($part) || $part instanceof \ArrayAccess) {
                return null;
            } else {
                $parts[] = $part;
            }
        }

        return implode('', $parts);
    }

    /**
     * Walk down the tree, calling $visitor for every node and its children (depth-first).
     *
     * @param \ScssPhp\ScssPhp\Block $tree
     * @param mixed $context
     * @param callable $visitor Function that gets passed the node and itself.
     */
    public static function traverse(Block $tree, $context, callable $visitor) {
        $filter = false;

        for ($i = 0; $i < count($tree->children); $i++) {
            $child = $tree->children[$i];

            // there's no easy way for a closure to reference itself in PHP, so
            // pass the visitor to itself from here.
            $result = $visitor($child, $context, $visitor);

            if ($result === self::TRAVERSE_REMOVE_NODE) {
                $tree->children[$i] = null;
                $filter = true;
            } else if ($result !== null && !is_int($result)) {
                $tree->children[$i] = $result;
            }
        }

        if ($filter) {
            $tree->children = array_values(array_filter($tree->children));
        }
    }
}
