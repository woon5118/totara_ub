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
 * @package ml_recommender
 */

namespace ml_recommender\loader\recommended_item;

use core\orm\pagination\offset_cursor_paginator;
use core\orm\query\builder;
use engage_article\totara_engage\resource\article;
use ml_recommender\entity\recommended_item;
use ml_recommender\entity\recommended_user_item;
use ml_recommender\local\environment;
use ml_recommender\query\recommended_item\item_query;
use ml_recommender\query\recommended_item\user_query;
use totara_engage\card\card_resolver;
use totara_engage\entity\engage_resource;

/**
 * Loader class for a recommended item
 */
final class articles_loader {
    /**
     * loader constructor.
     * Preventing this class from construction
     */
    private function __construct() {
    }

    /**
     * Get the related articles for the provided component & id
     *
     * @param item_query $query
     * @return offset_cursor_paginator
     */
    public static function get_recommended_articles(item_query $query): offset_cursor_paginator {
        $builder = static::get_base_article_query(recommended_item::TABLE);

        $builder->where('r.target_item_id', $query->get_target_item_id());
        $builder->where('r.target_component', $query->get_target_component());
        $builder->where('r.target_area', $query->get_target_area());

        $cursor = $query->get_cursor();
        $cursor->set_limit(environment::get_related_items_count());
        return new offset_cursor_paginator($builder, $cursor);
    }

    /**
     * Get the articles recommended for the provided user
     *
     * @param user_query $query
     * @return offset_cursor_paginator
     */
    public static function get_recommended_articles_for_user(user_query $query): offset_cursor_paginator {
        $builder = static::get_base_article_query(recommended_user_item::TABLE);

        $builder->where('r.user_id', $query->get_target_user_id());
        $builder->where('r.component', $query->get_target_component());
        $builder->where('r.area', $query->get_target_area());

        $cursor = $query->get_cursor();
        return new offset_cursor_paginator($builder, $cursor);
    }

    /**
     * Build the base article fetch query
     *
     * @return builder
     */
    private static function get_base_article_query(string $table): builder {
        $builder = builder::table($table, 'r');
        $builder->join([engage_resource::TABLE, 'er'], 'r.item_id', 'er.id');
        $builder->results_as_arrays();

        // We only want to return articles
        $builder->where('er.resourcetype', article::get_resource_type());
        $builder->order_by_raw('r.score DESC');

        $builder->select(
            [
                'er.id as instanceid', // card doesn't want the article id, it want's the resource id
                'er.name',
                'er.resourcetype as component',
                'er.userid',
                'er.access',
                'er.timecreated',
                'er.timemodified',
                'er.extra',
            ]
        );

        $builder->map_to(
            function(array $record) {
                return card_resolver::create_card(article::get_resource_type(), $record);
            }
        );

        return $builder;
    }
}