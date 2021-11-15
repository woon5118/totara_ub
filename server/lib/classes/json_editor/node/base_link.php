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

use core\json_editor\helper\node_helper;

/**
 * Base node for link node.
 */
abstract class base_link extends node {
    /**
     * @var string|null
     */
    protected $url;

    /**
     * Constructing the link node.
     *
     * @param array $node
     * @return node
     */
    public static function from_node(array $node): node {
        /** @var base_link $link_node */
        $link_node = parent::from_node($node);
        $link_node->url = null;

        $attrs = $node['attrs'];

        if (!empty($attrs['url'])) {
            $link_node->url = clean_param($attrs['url'], PARAM_URL);
        }

        return $link_node;
    }

    /**
     * @return string|null
     */
    public function get_url(): ?string {
        return $this->url;
    }

    /**
     * @param array $raw_node
     * @return bool
     */
    public static function validate_schema(array $raw_node): bool {
        if (!array_key_exists('attrs', $raw_node)) {
            return false;
        }

        $attrs = $raw_node['attrs'];
        if (!array_key_exists('url', $attrs) || empty($attrs['url'])) {
            return false;
        }

        // Note that we do not check for `attrs` keys at the parent, as the children can have anything with it.
        // But we know for sure that the node schema will be the same across every where else for links related node.
        return node_helper::check_keys_match_against_data($raw_node, ['type', 'attrs']);
    }

    /**
     * @param array $raw_node
     * @return array|null
     */
    public static function clean_raw_node(array $raw_node): ?array {
        $cleaned_raw_node = parent::clean_raw_node($raw_node);

        if (empty($cleaned_raw_node['attrs'])) {
            throw new \coding_exception("Invalid node structure", static::get_type());
        }

        $attrs = $cleaned_raw_node['attrs'];

        $url = $attrs['url'];
        $cleaned_url = clean_param($url, PARAM_URL);

        if (empty($cleaned_url)) {
            return null;
        }

        $cleaned_raw_node['attrs']['url'] = $cleaned_url;
        return $cleaned_raw_node;
    }
}