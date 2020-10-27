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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\data_providers\activity;

use core\collection;
use core\orm\entity\repository;
use core\orm\query\builder;
use core\pagination\base_paginator;
use core\pagination\cursor;
use mod_perform\data_providers\cursor_paginator_trait;
use mod_perform\data_providers\provider;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\models\activity\activity as activity_model;
use totara_core\access;

/**
 * Class activity.
 *
 * @package mod_perform\data_providers\activity
 *
 * @method collection|activity_model[] get
 */
class activity extends provider {

    use cursor_paginator_trait;

    /**
     * @inheritDoc
     */
    protected function build_query(): repository {
        return activity_entity::repository()
            ->as('a')
            ->filter_by_visible()
            ->join(['course_modules', 'cm'], function (builder $builder) {
                $builder->where_field('course', 'a.course')
                    ->where_field('instance', 'a.id');
            })
            ->join(['context', 'ctx'], function (builder $builder) {
                $builder->where_field('instanceid', 'cm.id')
                    ->where('contextlevel', CONTEXT_MODULE);
            })
            ->where(function (builder $builder) {
                global $USER;

                // Restrict the returned activities to what the current user is allowed to view.

                [$sql, $params] = access::get_has_capability_sql('mod/perform:manage_activity', 'ctx.id', $USER->id);
                $builder->or_where_raw($sql, $params);

                [$sql, $params] = access::get_has_capability_sql('mod/perform:view_participation_reporting', 'ctx.id', $USER->id);
                $builder->or_where_raw($sql, $params);
            })
            ->with('type')
            // The following relations are all needed for reducing the amount of queries
            // triggered by the activity status conditions
            ->with('tracks')
            ->with([
                'sections_ordered' => function (repository $repository) {
                    $repository->with('section_relationships.core_relationship')
                        ->with('section_elements.element');
                }
            ])
            ->order_by('id');
    }

    /**
     * @return collection|activity_model[]
     */
    protected function process_fetched_items(): collection {
        return $this->items
            ->map_to(activity_model::class);
    }

    /**
     * @param repository $repository
     * @param int $activity_id
     */
    protected function filter_query_by_id(repository $repository, int $activity_id): void {
        $repository->where('id', $activity_id);
    }

    /**
     * Paginate list of activities.
     *
     * @param string|null $cursor
     * @param int $limit Number of items to fetch.
     *
     * @return array
     */
    public function get_activities_page(?string $cursor = null, int $limit = base_paginator::DEFAULT_ITEMS_PER_PAGE): array {
        $page_cursor = $cursor !== null
            ? cursor::decode($cursor)
            : cursor::create()->set_limit($limit);

        $paginated_results = $this->get_next($page_cursor, true)->get();
        $paginated_results['items'] = array_map(function ($activity_entity) {
            return new activity_model($activity_entity);
        }, $paginated_results['items']);

        return $paginated_results;
    }
}