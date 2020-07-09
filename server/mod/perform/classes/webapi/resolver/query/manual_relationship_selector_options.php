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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use mod_perform\webapi\middleware\require_activity;
use mod_perform\webapi\middleware\require_manage_capability;
use totara_core\entities\relationship;

/**
 * Get the options for manual relationships to be set at an activity level by an admin.
 *
 * @package mod_perform\webapi\resolver\query
 */
class manual_relationship_selector_options implements query_resolver, has_middleware {

    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        // The require_activity middleware loads the activity and passes it along via the args
        $activity = $args['activity'];

        // TODO: Return actual data in TL-26142 / TL-26143
        return self::get_dummy_data($activity);
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            require_activity::by_activity_id('activity_id', true),
            require_manage_capability::class,
        ];
    }

    /**
     * TODO: REMOVE IN TL-26142 / TL-26143
     */
    public static function get_dummy_data(\mod_perform\models\activity\activity $activity): array {
        $core_relationships = \totara_core\entities\relationship::repository()
            ->with('resolvers')
            ->where('type', relationship::TYPE_STANDARD)
            ->order_by('id')
            ->limit(3)
            ->get()
            ->map_to(\totara_core\relationship\relationship::class);
        $subject_relationship = $core_relationships->first();

        $manual_relationships = \totara_core\entities\relationship::repository()
            ->with('resolvers')
            ->where('type', relationship::TYPE_MANUAL)
            ->order_by('id')
            ->get()
            ->map_to(\totara_core\relationship\relationship::class);

        $relationships_to_return = [];
        foreach ($manual_relationships as $i => $manual_relationship) {
            $relationships_to_return[] = (object) [
                'activity' => $activity,
                'manual_relationship' => $manual_relationship,
                'selector_relationship' => $subject_relationship,
            ];
        }

        $data = [
            'manual_relationships' => $relationships_to_return,
        ];

        if ($activity->is_draft()) {
            $data['selector_options'] = $core_relationships;
        }

        return $data;
    }

}
