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
 * @package totara_playlist
 */
namespace totara_playlist\webapi\resolver\mutation;

use core\webapi\execution_context;
use core\webapi\middleware\clean_content_format;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\mutation_resolver;
use totara_engage\access\access;
use totara_engage\share\manager as share_manager;
use totara_engage\share\recipient\manager as recipient_manager;
use totara_engage\webapi\middleware\require_valid_recipients;
use totara_playlist\exception\playlist_exception;
use totara_playlist\playlist;
use core\webapi\resolver\has_middleware;
use core\webapi\middleware\clean_editor_content;

/**
 * Mutation resolver for totara_playlist_update
 */
final class update implements mutation_resolver, has_middleware {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return playlist
     */
    public static function resolve(array $args, execution_context $ec): playlist {
        global $USER;
        if (!$ec->has_relevant_context()) {
            $ec->set_relevant_context(\context_user::instance($USER->id));
        }

        $playlist = playlist::from_id($args['id'], true);

        $name = $playlist->get_name(false);
        $access = $playlist->get_access();
        $summary = null;
        $summary_format = null;

        if (!empty($args['name'])) {
            if (\core_text::strlen($args['name']) > 75) {
                throw playlist_exception::create('update');
            }
            $name = $args['name'];
        }

        if (isset($args['access'])) {
            // Type totara_engage_access is a string name of the constant. Therefore, we should
            // format that constant into a value that machine can understand.
            $access = access::get_value($args['access']);
        }

        if (isset($args['summary'])) {
            $summary = $args['summary'];
        }

        if (isset($args['summary_format'])) {
            $summary_format = $args['summary_format'];
        }

        $playlist->update($name, $access, $summary, $summary_format, $USER->id);

        // Add/remove topics.
        if (!empty($args['topics'])) {
            // Remove all the current topics first, but only if it is not appearing in this list.
            $playlist->remove_topics_by_ids($args['topics']);
            $playlist->add_topics_by_ids($args['topics']);
        }

        // Shares
        if (!empty($args['shares'])) {
            $recipients = recipient_manager::create_from_array($args['shares']);
            share_manager::share($playlist, 'totara_playlist', $recipients);
        }

        return $playlist;
    }

    /**
     * @return array
     */
    public static function get_middleware(): array {
        return [
            new require_login(),
            new require_advanced_feature('engage_resources'),
            new require_valid_recipients('shares'),
            // summary field is an optional for this operation. Hence we will not require it.
            new clean_editor_content('summary', 'summary_format', false),

            // We leave the default value to null, because we want to fallback to the current playlist
            // format if the summary format is not set.
            new clean_content_format('summary_format', null, [FORMAT_JSON_EDITOR])
        ];
    }
}