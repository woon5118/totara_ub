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
 * @package core
 */
namespace core\json_editor\node;

use core\json_editor\formatter\formatter;
use core\json_editor\helper\node_helper;
use core\json_editor\schema;

/**
 * Base list class for both ordered and bullet list.
 */
abstract class base_list extends node {
    /**
     * Array of children.
     * @var array
     */
    protected $children;

    /**
     * @param array $node
     * @return node
     */
    public static function from_node(array $node): node {
        /** @var base_list $list_node */
        $list_node = parent::from_node($node);
        $list_node->children = [];

        if (!array_key_exists('content', $node) || !is_array($node['content'])) {
            debugging("No property 'content' found for the node", DEBUG_DEVELOPER);
            return $list_node;
        }

        $list_node->children = $node['content'];
        return $list_node;
    }

    /**
     * @param formatter $formatter
     * @return string
     */
    public function to_html(formatter $formatter): string {
        $content = '';

        foreach ($this->children as $child) {
            $content .= $formatter->print_node($child, formatter::HTML);
        }

        return $content;
    }

    /**
     * @param array $raw_node
     * @return bool
     */
    public static function validate_schema(array $raw_node): bool {
        if (!array_key_exists('content', $raw_node)) {
            return false;
        }

        // Let the children to do the actual implementation of checking.
        return node_helper::check_keys_match_against_data($raw_node, ['type', 'content']);
    }

    /**
     * @param array $raw_node
     * @return array|null
     */
    public static function clean_raw_node(array $raw_node): ?array {
        $cleaned_raw_node = parent::clean_raw_node($raw_node);
        if (null === $cleaned_raw_node) {
            return null;
        }

        if (!array_key_exists('content', $cleaned_raw_node)) {
            throw new \coding_exception("Invalid node structure", static::get_type());
        }

        if (!is_array($cleaned_raw_node['content'])) {
            // Empty list, skip the rest
            $cleaned_raw_node['content'] = [];
            return $cleaned_raw_node;
        }

        // Reset the keys on the array content - just to make sure that the keys of content are
        // just numeric and nothing else.
        $cleaned_raw_node['content'] = array_values($cleaned_raw_node['content']);

        $contents = $cleaned_raw_node['content'];
        $schema = schema::instance();

        foreach ($contents as $i => $content_node) {
            if (!array_key_exists('type', $content_node)) {
                throw new \coding_exception("Invalid node structure", static::get_type());
            }

            $node_class = $schema->get_node_classname($content_node['type']);
            if (null === $node_class) {
                debugging("Cannot find node class for type '{$content_node['type']}'", DEBUG_DEVELOPER);
                continue;
            }

            $cleaned_content_node = call_user_func([$node_class, 'clean_raw_node'], $content_node);
            if (null === $cleaned_content_node) {
                return null;
            }

            $cleaned_raw_node['content'][$i] = $cleaned_content_node;
        }

        return $cleaned_raw_node;
    }

    /**
     * @deprecated since Totara 13.8
     * @param array $raw_node
     * @return array
     */
    public static function sanitize_raw_node(array $raw_node): array {
        $sanitized_node = parent::sanitize_raw_node($raw_node);
        $content_nodes = $sanitized_node['content'] ?? [];

        $sanitized_node['content'] = node_helper::sanitize_raw_nodes($content_nodes);
        return $sanitized_node;
    }
}