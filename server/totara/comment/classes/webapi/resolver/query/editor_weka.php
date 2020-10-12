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

use core\webapi\execution_context;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use totara_comment\resolver_factory;
use context;
use totara_comment\webapi\editor_weka_helper;
use totara_comment\webapi\resolver\middleware\validate_comment_area;
use totara_core\identifier\component_area;

/**
 * Query to fetch editor configuration of other component and area, but will be masked with this very component
 * which is totara_comment
 */
final class editor_weka implements query_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     *
     * @return \weka_texteditor
     */
    public static function resolve(array $args, execution_context $ec): \weka_texteditor {
        if (!empty($args['id'])) {
            debugging(
                "The argument 'id' has been deprecated, please do not use it",
                DEBUG_DEVELOPER
            );
        }

        $component = $args['component'];
        $area = $args['area'];
        $instance_id = $args['instance_id'];
        $comment_area = strtolower($args['comment_area']);

        $resolver = resolver_factory::create_resolver($component);
        $context_id = $resolver->get_context_id($instance_id, $area);

        if (!$ec->has_relevant_context()) {
            $context = context::instance_by_id($context_id);
            $ec->set_relevant_context($context);
        }

        $identifier = new component_area($component, $area);
        return editor_weka_helper::create_mask_editor(
            $identifier,
            $comment_area,
            $context_id
        );
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new validate_comment_area('comment_area')
        ];
    }
}