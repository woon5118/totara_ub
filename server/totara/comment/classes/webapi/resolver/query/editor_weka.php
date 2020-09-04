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
use editor_weka\config\factory;
use totara_comment\comment;
use totara_comment\resolver_factory;

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
        global $CFG, $USER;
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(\context_user::instance($USER->id));
        }

        $component = $args['component'];
        $area = $args['area'];

        $comment_area = strtolower($args['comment_area']);
        if (!in_array($comment_area, [comment::COMMENT_AREA, comment::REPLY_AREA])) {
            throw new \coding_exception("Invalid comment area: {$comment_area}");
        }

        $factory = new factory();
        $factory->load();

        $configuration = $factory->get_configuration($component, $area);
        if (null !== $configuration) {
            // Time to mock the configuration for the comment.
            $factory->add_configuration('totara_comment', $comment_area, $configuration);
        }

        require_once("{$CFG->dirroot}/lib/editor/weka/lib.php");

        // We want an editor of totara_comment with the area of comment.
        $editor = new \weka_texteditor($factory);
        $resolver = resolver_factory::create_resolver($component);

        if (isset($args['id'])) {
            $comment = comment::from_id($args['id']);

            $context_id = $resolver->get_context_id($comment->get_instanceid(), $area);
            $editor->set_contextid($context_id);
        } else if ($ec->has_relevant_context()) {
            $context = $ec->get_relevant_context();
            $editor->set_contextid($context->id);
        }

        return $editor;
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
        ];
    }

}