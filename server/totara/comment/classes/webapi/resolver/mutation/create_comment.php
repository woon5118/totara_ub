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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package totara_comment
 */
namespace totara_comment\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use totara_comment\comment;
use totara_comment\comment_helper;
use core\webapi\resolver\has_middleware;
use core\webapi\middleware\clean_editor_content;

final class create_comment implements mutation_resolver, has_middleware {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return comment
     */
    public static function resolve(array $args, execution_context $ec): comment {
        global $USER;
        require_login();

        $component = $args['component'];
        $area = $args['area'];
        $content = $args['content'];
        $instanceid = $args['instanceid'];

        $format = FORMAT_JSON_EDITOR;
        if (isset($args['format'])) {
            $format = $args['format'];
        }

        $draft_id = null;
        if (isset($args['draft_id'])) {
            $draft_id = (int) $args['draft_id'];
        }

        return comment_helper::create_comment(
            $component,
            $area,
            $instanceid,
            $content,
            $format,
            $draft_id,
            $USER->id
        );
    }

    /**
     * @return array
     */
    public static function get_middleware(): array {
        return [
            new clean_editor_content('content', 'format')
        ];
    }
}