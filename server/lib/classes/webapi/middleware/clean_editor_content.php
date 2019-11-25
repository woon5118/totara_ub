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
namespace core\webapi\middleware;

use Closure;
use core\webapi\middleware;
use core\webapi\resolver\payload;
use core\webapi\resolver\result;
use core\json_editor\helper\document_helper;

/**
 * Middle-ware for cleaning up the json editor.
 */
final class clean_editor_content implements middleware {
    /**
     * The variables key to get content.
     * @var string
     */
    private $content_key;

    /**
     * The variable keys to get content format. If it is not provided, then
     * the default format value will be using FORMAT_PLAIN which will do nothing.
     *
     * @var string|null
     */
    private $content_format_key;

    /**
     * Passing down the keys to get the content value and its format value from the payload
     * when the middleware is running thru.
     *
     * clean_json_editor constructor.
     * @param string        $content_key
     * @param string|null   $content_format_key
     */
    public function __construct(string $content_key, ?string $content_format_key = null) {
        $this->content_key = $content_key;
        $this->content_format_key = $content_format_key;
    }

    /**
     * @param payload $payload
     * @param Closure $next
     *
     * @return result
     */
    public function handle(payload $payload, Closure $next): result {
        if (!$payload->has_variable($this->content_key)) {
            throw new \coding_exception(
                "Cannot find the content variable at key '{$this->content_key}'"
            );
        }

        $format = FORMAT_PLAIN;
        if (null !== $this->content_format_key && $payload->has_variable($this->content_format_key)) {
            $format = $payload->get_variable($this->content_format_key);
        }

        if (FORMAT_JSON_EDITOR == $format) {
            // it is a json editor content - start cleaning them.
            $raw_content = $payload->get_variable($this->content_key);

            if (!empty($raw_content)) {
                $cleaned_content = document_helper::clean_json_document($raw_content);
                $payload->set_variable($this->content_key, $cleaned_content);
            }
        }

        return $next->__invoke($payload);
    }
}
