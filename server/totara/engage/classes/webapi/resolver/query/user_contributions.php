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
 * @author Cody Finegan <cody.finegan@totaralearning.com>
 * @package totara_engage
 */
namespace totara_engage\webapi\resolver\query;

use core\pagination\offset_cursor;
use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_core\advanced_feature;
use totara_engage\card\card_loader;
use totara_engage\query\query;

final class user_contributions implements query_resolver {
    /**
     * Area that this endpoint only supports
     */
    private static $valid_areas = [
        'otheruserlib',
    ];

    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return array
     */
    public static function resolve(array $args, execution_context $ec): array {
        global $USER;
        require_login();
        advanced_feature::require('engage_resources');

        $target_user_id = $args['user_id'] ?? null;
        if (empty($target_user_id)) {
            throw new \coding_exception('Query user_contributions must specify the "user_id" field');
        }

        // Only support the one area
        if (!in_array($args['area'], self::$valid_areas)) {
            throw new \coding_exception("Query user_contributions does not suporrt the '{$args['area']}' area.");
        }

        // If the user cannot access the target user, return nothing
        // We silently fail here as we want the parent page to handle it
        $target_user_context = \context_user::instance($target_user_id);
        if ($target_user_context->is_user_access_prevented($USER->id)) {
            return [
                'cursor' => null,
                'cards' => [],
            ];
        }

        $query = new query();
        $query->set_component($args['component'] ?? null);
        $query->set_area($args['area']);
        $query->set_filters($args['filter']);

        // User who owns the cards we want to display
        $query->set_userid($target_user_id);

        // User doing the asking (show the shared)
        $query->set_share_recipient_id($USER->id);

        if (!empty($args['cursor'])) {
            $cursor = offset_cursor::decode($args['cursor']);
            $query->set_cursor($cursor);
        }

        $loader = new card_loader($query);
        $paginator = $loader->fetch();

        return [
            'cursor' => $paginator,
            'cards' => $paginator->get_items()->all()
        ];
    }
}