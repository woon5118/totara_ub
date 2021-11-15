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
use totara_comment\comment;
use totara_comment\resolver_factory;

/**
 * Query resolving for draft item.
 */
final class draft_item implements query_resolver, has_middleware {
    /**
     * @param array $args
     * @param execution_context $ec
     * @return comment
     */
    public static function resolve(array $args, execution_context $ec): comment {
        global $USER;
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(\context_user::instance($USER->id));
        }

        $comment = comment::from_id($args['id']);
        $resolver = resolver_factory::create_resolver($comment->get_component());

        // Owner is able to update their own comment.
        $owner_id = $comment->get_userid();
        if ($owner_id == $USER->id) {
            return $comment;
        }

        if (!$resolver->is_allow_to_update($comment, $USER->id)) {
            throw new \coding_exception(
                "Cannot prepare a draft for the item where the item is not being update-able by user"
            );
        }

        return $comment;
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