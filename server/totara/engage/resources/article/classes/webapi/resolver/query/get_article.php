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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package engage_article
 */
namespace engage_article\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use engage_article\totara_engage\resource\article;
use totara_core\advanced_feature;
use totara_engage\access\access_manager;
use core_user\totara_engage\share\recipient\user as user_recipient;

final class get_article implements query_resolver {
    /**
     * @param array             $args
     * @param execution_context $ec
     *
     * @return article
     */
    public static function resolve(array $args, execution_context $ec): article {
        global $USER;
        require_login();
        advanced_feature::require('engage_resources');

        /** @var article $article */
        $article = article::from_resource_id($args['id']);

        if (!access_manager::can_access($article, $USER->id)) {
            throw new \coding_exception("User with id '{$USER->id}' does not have access to this article");
        }

        return $article;
    }
}