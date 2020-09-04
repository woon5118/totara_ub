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
 * @package engage_article
 */
namespace engage_article\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\mutation_resolver;
use core\webapi\resolver\has_middleware;
use engage_article\totara_engage\resource\article;
use totara_engage\access\access;
use totara_engage\timeview\time_view;
use totara_engage\share\recipient\manager as recipient_manager;
use totara_engage\share\manager as share_manager;

final class update implements mutation_resolver, has_middleware {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return article
     */
    public static function resolve(array $args, execution_context $ec) {
        global $USER;
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(\context_user::instance($USER->id));
        }

        $id = $args['resourceid'];
        unset($args['resourceid']);

        if (isset($args['access']) && !is_numeric($args['access']) && is_string($args['access'])) {
            // Format the string access into a proper value that machine can understand.
            $access = access::get_value($args['access']);
            $args['access'] = $access;
        }

        if (!isset($args['format'])) {
            $args['format'] = FORMAT_JSON_EDITOR;
        }

        if (isset($args['timeview'])) {
            $timeview = time_view::get_value($args['timeview']);
            $args['timeview'] = $timeview;
        }

        /** @var article $article */
        $article = article::from_resource_id($id);
        $article->update($args, $USER->id);

        // Add/remove topics.
        if (!empty($args['topics'])) {
            // Remove all the current topics first, but only if it is not appearing in this list.
            $article->remove_topics_by_ids($args['topics']);
            $article->add_topics_by_ids($args['topics']);
        }

        // Shares
        if (!empty($args['shares'])) {
            $recipients = recipient_manager::create_from_array($args['shares']);
            share_manager::share($article, article::get_resource_type(), $recipients);
        }

        return $article;
    }

    /**
     * @inheritDoc
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('engage_resources'),
        ];
    }

}