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
 * Node for video
 */
final class video extends base_file implements block_node {
    /**
     * @var string
     */
    private $mime_type;

    /**
     * @param array $node
     * @return video
     */
    public static function from_node(array $node): node {
        /** @var video $video */
        $video = parent::from_node($node);
        $attrs = $node['attrs'];

        if (!array_key_exists('mime_type', $attrs)) {
            throw new \coding_exception("Unable to find mime_type");
        }

        $video->mime_type = $attrs['mime_type'];
        return $video;
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

        $attrs =  $raw_node['attrs'];
        if (!array_key_exists('mime_type', $attrs)) {
            return false;
        }

        $input_keys = array_keys($attrs);
        return node_helper::check_keys_match($input_keys, ['mime_type', 'url', 'filename']);
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

        $mime_type = $cleaned_raw_node['attrs']['mime_type'];
        $cleaned_raw_node['attrs']['mime_type'] = clean_param($mime_type, PARAM_TEXT);

        return $cleaned_raw_node;
    }

    /**
     * @param formatter $formatter
     * @return string
     */
    public function to_html(formatter $formatter): string {
        // Just return a dummy url for now. VideoJS will in later.
        return html_writer::tag(
            'a',
            $this->filename,
            ['href' => $this->get_file_url()->out(false)]
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
        return 'video';
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
        $file_url = static::build_file_url_from_stored_file($file);

        return [
            'type' => static::get_type(),
            'attrs' => [
                'url' => $file_url->out(false),
                'filename' => $file->get_filename(),
                'mime_type' => $file->get_mimetype()
            ],
        ];
    }
}