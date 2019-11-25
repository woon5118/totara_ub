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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package core
 */
namespace core\json_editor\node;

use core\json_editor\formatter\formatter;
use core\json_editor\helper\node_helper;
use html_writer;
use core\json_editor\node\abstraction\block_node;

final class link_media extends base_link implements block_node {
    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $image;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var array
     */
    private $resolution;

    /**
     * @param array $node
     * @return node
     */
    public static function from_node(array $node): node {
        /** @var link_media $block */
        $block = parent::from_node($node);

        $block->title = null;
        $block->image = null;
        $block->description = null;
        $block->resolution = [];

        $attrs = $node['attrs'];

        if (array_key_exists('title', $attrs)) {
            $block->title = $attrs['title'];
        }

        if (array_key_exists('description', $attrs)) {
            $block->description = $attrs['description'];
        }

        if (array_key_exists('resolution', $attrs)) {
            $block->resolution = (array) $attrs['resolution'];
        }

        if (array_key_exists('image', $attrs)) {
            $block->image = clean_param($attrs['image'], PARAM_URL);
        }

        return $block;
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
        if (array_key_exists('resolution', $attrs)) {
            // Resolution attribute is pretty much optional, but if it is provided, then we will
            // run thru the schema check.

            if (null !== $attrs['resolution'] && !is_array($attrs['resolution'])) {
                return false;
            }

            // Check for resolution.
            $resolution = $attrs['resolution'];
            if (!empty($resolution)) {
                if (!array_key_exists('width', $resolution) || !array_key_exists('height', $resolution)) {
                    return false;
                }

                $resolution_keys = array_keys($resolution);
                if (!node_helper::check_keys_match($resolution_keys, ['width', 'height'])) {
                    return false;
                }
            }
        }

        $input_keys = array_keys($attrs);
        return node_helper::check_keys_match($input_keys, ['url'], ['title', 'description', 'resolution', 'image']);
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

        if (isset($attrs['title'])) {
            $attrs['title'] = clean_param($attrs['title'], PARAM_TEXT);
        }

        if (isset($attrs['description'])) {
            $attrs['description'] = clean_param($attrs['description'], PARAM_TEXT);
        }

        if (isset($attrs['image'])) {
            $attrs['image'] = clean_param($attrs['image'], PARAM_URL);
        }

        if (array_key_exists('resolution', $attrs)) {
            if (!is_array($attrs['resolution'])) {
                $attrs['resolution'] = [];
            } else {
                $attrs['resolution']['width'] = clean_param($attrs['resolution']['width'], PARAM_INT);
                $attrs['resolution']['height'] = clean_param($attrs['resolution']['height'], PARAM_INT);
            }
        }

        $cleaned_raw_node['attrs'] = $attrs;
        return $cleaned_raw_node;
    }

    /**
     * @param formatter $formatter
     * @return string
     */
    public function to_html(formatter $formatter): string {
        $info = $this->get_info();

        switch ($info['type']) {
            case 'iframe':
                $title = $info['url'];
                if (null !== $this->title && '' !== $this->title) {
                    $title = clean_text($this->title);
                }

                // Don't think iframe is supported nicely, so we will just use the url for now.
                // If it is iframe type then we should use the original URL in-order to let the filter
                // run normally with the embedded media player.
                return html_writer::tag(
                    'a',
                    $title,
                    ['href' => $this->url]
                );

            case 'image':
                return html_writer::empty_tag('img', ['src' => $info['url']]);

            case 'audio':
                return html_writer::tag(
                    'div',
                    html_writer::tag('audio', null, ['src' => $this->url, 'controls' => true])
                );

            default:
                $title = $this->url;

                if (null !== $this->title && '' !== $this->title) {
                    $title = $this->title;
                }

                return html_writer::tag(
                    'div',
                    html_writer::tag('a', $title, ['href' => $this->url])
                );
        }
    }

    /**
     * @param formatter $formatter
     * @return string
     */
    public function to_text(formatter $formatter): string {
        return "{$this->title} ({$this->url})";
    }

    /**
     * @return string
     */
    protected static function do_get_type(): string {
        return 'link_media';
    }

    /**
     * Get information needed to display.
     *
     * @return array
     */
    public function get_info(): array {
        $url = $this->url;

        if (preg_match('/^https?:\/\/(?:www\.)?youtube.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $match) ||
            preg_match('/^https?:\/\/(?:www\.)?youtu.be\/([a-zA-Z0-9_-]+)/', $url, $match)
        ) {
            return [
                'type' => 'iframe',
                'url' => 'https://www.youtube.com/embed/' . $match[1] . '?rel=0',
                'image' => $this->image,
            ];
        }

        if (preg_match('/^https?:\/\/(?:www\.)?vimeo.com\/([0-9]+)/', $url, $match)) {
            return [
                'type' => 'iframe',
                'url' => 'https://player.vimeo.com/video/' . $match[1] . '?portrait=0',
                'image' => null, // Vimeo images come back mixed up currently
            ];
        }

        if (preg_match('/\.(png|jpe?g|gif|webp|avif)$/', $url)) {
            return [
                'type' => 'image',
                'url' => $url,
                'image' => $url,
            ];
        }

        if (preg_match('/\.(aac|flac|m4a|mp3|ogg|opus|wav|wma)$/', $url)) {
            return [
                'type' => 'audio',
                'url' => $url,
                'image' => null,
            ];
        }

        return [
            'type' => 'link',
            'url' => $url,
            'image' => $this->image,
        ];
    }

    /**
     * @return array
     */
    public function get_resolution(): array {
        return $this->resolution;
    }

    /**
     * @return string|null
     */
    public function get_title(): ?string {
        return $this->title;
    }
}