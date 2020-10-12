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
 * @package totara_comment
 */
namespace totara_comment\webapi\resolver\query;

use core\webapi\middleware\require_login;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use totara_comment\resolver_factory;
use totara_comment\webapi\editor_weka_helper;
use totara_comment\webapi\resolver\middleware\validate_comment_area;
use totara_core\identifier\component_area;
use weka_texteditor;
use totara_comment\comment;
use context;

class editor_weka_from_id implements query_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return weka_texteditor
     */
    public static function resolve(array $args, execution_context $ec): weka_texteditor {
        $id = $args['id'];
        $comment_area = strtolower($args['comment_area']);

        $comment = comment::from_id($id);

        $component = $comment->get_component();
        $area = $comment->get_area();

        $resolver = resolver_factory::create_resolver($component);
        $context_id = $resolver->get_context_id($comment->get_instanceid(), $area);

        if (!$ec->has_relevant_context()) {
            $context = context::instance_by_id($context_id);
            $ec->set_relevant_context($context);
        }

        return editor_weka_helper::create_mask_editor(
            new component_area($component, $area),
            $comment_area,
            $context_id
        );
    }

    /**
     * @return array
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new validate_comment_area('comment_area')
        ];
    }
}