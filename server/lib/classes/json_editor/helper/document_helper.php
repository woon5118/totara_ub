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
namespace core\json_editor\helper;

use core\json_editor\node\node;
use core\json_editor\schema;
use core\json_editor\node\abstraction\block_node;

/**
 * A helper class for document, which it can run the validation, sanitizing on the json document.
 */
final class document_helper {
    /**
     * document_helper constructor.
     * Preventing this class from construction.
     */
    private function __construct() {
    }

    /**
     * Returning empty array, means that it is not able to parse the content into an array that the
     * machine can understand.
     *
     * @param \stdClass|array|string|\JsonSerializable $json
     * @return array
     */
    public static function parse_document($json): array {
        if (is_array($json)) {
            return $json;
        }

        $document = [];

        if (is_string($json)) {
            $document = json_decode($json, true);

            if (JSON_ERROR_NONE !== json_last_error() || !is_array($document)) {
                // Return the raw content, when there is an error.
                $msg = json_last_error_msg();
                debugging("There was an error on parsing json content: {$msg}", DEBUG_DEVELOPER);

                return [];
            }
        } else if (is_object($json)) {
            // Converting the whole data object holder intto an array, even with the nested object.
            $content = json_encode($json);
            $document = json_decode($content, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                $msg = json_last_error_msg();
                debugging(
                    "There was an error when converting an object into array via json encoding/decoding: {$msg}",
                    DEBUG_DEVELOPER
                );
            }

            if (!is_array($document)) {
                debugging("Cannot format the json content", DEBUG_DEVELOPER);
                return [];
            }
        }

        // Empty document will return, just that there is no debugging on invalid type of json content
        // if the json content is an actual number or some sorts.
        return $document;
    }

    /**
     * Running check against the document for every single node.
     *
     * @param string $json_document
     * @return bool
     */
    public static function is_valid_json_document(string $json_document): bool {
        if (empty($json_document)) {
            // Document is empty, which it should not go here.
            return false;
        }

        $document = self::parse_document($json_document);
        if (empty($document)) {
            debugging("Cannot decode the json document as it is invalid json", DEBUG_DEVELOPER);
            return false;
        }

        return self::is_valid_document($document);
    }

    /**
     * @param array $document
     * @return bool
     */
    public static function is_valid_document(array $document): bool {
        if (!array_key_exists('type', $document) || 'doc' !== $document['type']) {
            // Invalid document schema at the very top level.
            return false;
        } else if (!array_key_exists('content', $document)) {
            return false;
        }

        if (!is_array($document['content'])) {
            // Invalid content.
            return false;
        }

        // Now the fun begins, where we have to loop thru every single node and validate its schema.
        return self::do_validate_raw_nodes($document['content']);
    }

    /**
     * Take a json document in, then parse it to a proper json array data/document. Clean it, then return
     * the json document - as a string.
     *
     * If the json document is invalid, empty string will be return.
     *
     * Note that this function is for the INPUT - when the system is receiving any data from the user - who
     * is interacting with the system.
     *
     * If you want to use cleaning for output, then please use {@see document_helper::sanitize_json_document()}
     *
     * @param string $json_document
     * @return string
     */
    public static function clean_json_document(string $json_document): string {
        if (empty($json_document)) {
            // Nothing to do pretty much.
            return '';
        }

        // Make sure we clean it first/again - would not HURD :P
        clean_param($json_document, PARAM_RAW);
        $document = self::parse_document($json_document);
        $cleaned_document = static::clean_json($document);

        if (empty($cleaned_document)) {
            return '';
        }

        return self::json_encode_document($cleaned_document);
    }

    /**
     * @param array $document
     * @return string
     */
    protected static function json_encode_document(array $document): string {
        $json_result = json_encode(
            $document,
            JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \coding_exception("Cannot encoded the json data");
        }

        return $json_result;
    }

    /**
     * @see document_helper::clean_json_document() for more details
     * Take in an array of document, then clean it and return the document.
     *
     * @param array $document
     * @return array
     */
    public static function clean_json(array $document): array {
        if (!array_key_exists('content', $document)) {
            $document['content'] = [];
        }

        if (!self::is_valid_document($document)) {
            debugging("JSON document is invalid", DEBUG_DEVELOPER);
            return [];
        }

        $contents = $document['content'];
        $cleaned_contents = self::do_clean_raw_nodes($contents);

        if (null === $cleaned_contents) {
            return [];
        }

        $document['content'] = $cleaned_contents;
        $document['type'] = clean_param($document['type'], PARAM_TEXT);

        return $document;
    }

    /**
     * This function will invoke {@see node::validate_schema()}
     *
     * Note that this function will only expecting {@see block_node} only. If any node(s) within the list
     * of raw nodes are not a block node will result in false on validation.
     *
     * @param array $raw_nodes
     * @return bool
     */
    protected static function do_validate_raw_nodes(array $raw_nodes): bool {
        $schema = schema::instance();

        foreach ($raw_nodes as $raw_node) {
            if (!isset($raw_node['type'])) {
                return false;
            }

            $type = $raw_node['type'];
            $node_class = $schema->get_node_classname($type);

            if (null === $node_class) {
                // Skip invalid node for now.
                debugging("Cannot find node class for type '{$type}'", DEBUG_DEVELOPER);
                continue;
            }

            if (!is_subclass_of($node_class, block_node::class)) {
                // Expecting block node only.
                return false;
            }

            // Validate the schema.
            $result = call_user_func([$node_class, 'validate_schema'], $raw_node);
            if (!$result) {
                return false;
            }
        }

        // Schema looks good.
        return true;
    }

    /**
     * This function will try to invoke {@see node::clean_raw_node}
     *
     * @param array $raw_nodes
     * @return array|null
     */
    protected static function do_clean_raw_nodes(array $raw_nodes): ?array {
        // If it is an array of array, this should be safe enough. However if it is an array of objects,
        // then it does not guaratee the isolation on mutate the object.
        $cloned_raw_nodes = $raw_nodes;
        $schema = schema::instance();

        foreach ($raw_nodes as $key => $raw_node) {
            if (!isset($raw_node['type'])) {
                throw new \coding_exception("No type was found", DEBUG_DEVELOPER);
            }

            $type = $raw_node['type'];
            $node_class = $schema->get_node_classname($type);

            if (null === $node_class) {
                debugging("Cannot find any node class for type '{$type}'", DEBUG_DEVELOPER);
                continue;
            }

            $cleaned_raw_node = call_user_func([$node_class, 'clean_raw_node'], $raw_node);
            if (null === $cleaned_raw_node) {
                return null;
            }

            // Make sure that the cleaned raw node will not destroy our key `type` as it is the very special one.
            if (!isset($cleaned_raw_node['type'])) {
                throw new \coding_exception(
                    "Do NOT delete the key 'type' when cleaning the raw node data",
                    $node_class
                );
            }

            $cloned_raw_nodes[$key] = $cleaned_raw_node;
        }

        return $cloned_raw_nodes;
    }

    /**
     * Take in a string, and parse it to a proper json document, then sanitize it - which is making almost
     * every node to run pass thru {@see s()} and {@see clean_string()}.
     *
     * Note that this is for the OUTPUTTING to the raw json document to user facing interface.
     * If you are looking for the way in - INPUTTING, please use {@see document_helper::clean_json_document()} instead
     *
     * @param string $json_document
     * @return string
     */
    public static function sanitize_json_document(string $json_document): string {
        if (empty($json_document)) {
            return '';
        }

        $document = self::parse_document($json_document);
        $sanitized_document = self::sanitize_json($document);

        return self::json_encode_document($sanitized_document);
    }

    /**
     * @see document_helper::sanitize_json_document() for more detail.
     *
     * @param array $document
     * @return array
     */
    public static function sanitize_json(array $document): array {
        if (!isset($document['type'])) {
            throw new \coding_exception("Invalid json document");
        }

        if (!array_key_exists('content', $document)) {
            $document['content'] = [];
        }

        $block_nodes = $document['content'];
        $document['content'] = node_helper::sanitize_raw_nodes($block_nodes);

        return $document;
    }
}