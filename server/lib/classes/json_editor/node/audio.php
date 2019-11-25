<?php
/**
 * This file is part of Totara LMS
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
use core\json_editor\node\file\base_file;
use core\json_editor\node\abstraction\block_node;
use html_writer;

/**
 * Class audio
 * @package core\json_editor\node
 */
final class audio extends base_file implements block_node {
    /**
     * @var string
     */
    protected $mime_type;

    /**
     * @param array $node
     * @return node
     */
    public static function from_node(array $node): node {
        /** @var audio $audio */
        $audio = parent::from_node($node);
        $attrs = $node['attrs'];

        if (!array_key_exists('mime_type', $attrs)) {
            throw new \coding_exception("No mime type was set");
        }

        $audio->mime_type = $attrs['mime_type'];
        return $audio;
    }

    /**
     * @param array $raw_node
     * @return bool
     */
    public static function validate_schema(array $raw_node): bool {
        $result = parent::validate_schema($raw_node);
        if (!$result) {
            return false;
        }

        $attrs = $raw_node['attrs'];
        if (!array_key_exists('mime_type', $attrs)) {
            return false;
        }

        // Validate on attribute keys.
        $input_keys = array_keys($attrs);
        return node_helper::check_keys_match($input_keys, ['filename', 'url', 'mime_type']);
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

        $attrs = $cleaned_raw_node['attrs'];

        // Cleanning mime type.
        $attrs['mime_type'] = clean_param($attrs['mime_type'], PARAM_TEXT);
        $cleaned_raw_node['attrs'] = $attrs;

        return $cleaned_raw_node;
    }

    /**
     * @param formatter $formatter
     * @return string
     */
    public function to_html(formatter $formatter): string {
        return html_writer::tag(
            'div',
            html_writer::tag(
                'audio',
                null,
                ['src' => $this->url, 'controls' => true]
            )
        );
    }

    /**
     * @param formatter $formatter
     * @return string
     */
    public function to_text(formatter $formatter): string {
        $url = $this->get_file_url();
        return "[{$this->filename}]({$url->out(false)})";
    }

    /**
     * @return string
     */
    protected static function do_get_type(): string {
        return 'audio';
    }

    /**
     * @return string
     */
    public function get_mime_type(): string {
        return $this->mime_type;
    }

    /**
     * @param \stored_file $file
     * @return array
     */
    public static function create_raw_node(\stored_file $file): array {
        $file_url = self::build_file_url_from_stored_file($file);
        return [
            'type' => static::get_type(),
            'attrs' => [
                'filename' => $file->get_filename(),
                'url' => $file_url->out(false),
                'mime_type' => $file->get_mimetype()
            ],
        ];
    }
}