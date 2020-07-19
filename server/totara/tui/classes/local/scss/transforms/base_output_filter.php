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

namespace totara_tui\local\scss\transforms;

use totara_tui\local\scss\util;
use ScssPhp\ScssPhp\Block;
use ScssPhp\ScssPhp\Type;

/**
 * Base implementation for {@see output_only} and {@see definitions_only}
 *
 * Provides a protected visitor to filter to only emit code that results in either output or definitions.
 *
 * In:
 * $link-color: red; a { color: $link-color }
 *
 * Out (only_output = false):
 * $link-color: red;
 *
 * Out (only_output = true):
 * a { color: $link-color }
 */
abstract class base_output_filter extends transform {
    /**
     * @var string[] nodes where all of the children are okay to output for only_output = true
     */
    private $only_output_children_ok = [
        // assign may use non-output-only nodes, which is okay
        Type::T_ASSIGN,
    ];

    /**
     * @var string[] nodes where all of the children are okay to output for only_output = false
     */
    private $only_definitions_children_ok = [
        // mixin and function don't output anything even though they contain output only nodes
        Type::T_MIXIN,
        Type::T_FUNCTION
    ];

    /**
     * @var string[] nodes that are output-only
     */
    private $output_nodes = [
        Type::T_ASSIGN,
        Type::T_BLOCK,
        Type::T_MEDIA,
        Type::T_AT_ROOT,
        Type::T_DIRECTIVE,
    ];

    /**
     * Traversal visitor that filters tree to either only define things or only output.
     *
     * @param array $node AST node
     * @param object $context context object identifying the mode and transform name
     * @param callable $visitor Reference to self
     * @return int|null|array Replacement node or flags for traverser.
     */
    protected function output_filter_visitor(array $node, object $context, callable $visitor) {
        // rewrite imports to match
        if ($node[0] === Type::T_IMPORT) {
            $new_path = util::add_transform_to_import_path($node[1], $context->name);
            if ($new_path === null) {
                // no change needed
                return null;
            }
            $node[1] = $new_path;
            return $node;
        }

        // work out whether node is an output node or not
        $is_output_node = in_array($node[0], $this->output_nodes);
        // assign is normally output (i.e. `a: b`) but if the left side is a variable it is not (i.e. `$a: b`)
        if ($node[0] === Type::T_ASSIGN && $node[1][0] === Type::T_VARIABLE) {
            $is_output_node = false;
        }

        // remove node if it doesn't match the mode we're in
        if ($context->only_output !== $is_output_node) {
            return util::TRAVERSE_REMOVE_NODE;
        }

        // process children
        if (isset($node[1]) && $node[1] instanceof Block) {
            // some nodes' children are always okay, even if they don't match the output type,
            // so we should skip processing those
            if (!in_array($node[0], $context->only_output ? $this->only_output_children_ok : $this->only_definitions_children_ok)) {
                util::traverse($node[1], $context, $visitor);
            }
        }
    }
}
