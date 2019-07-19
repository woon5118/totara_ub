<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_orm
 */

namespace totara_competency\webapi\resolver\query;

use core\webapi\execution_context;
use core\webapi\query_resolver;
use totara_competency\models\progress;

class profile_progress implements query_resolver {
    public static function resolve(array $args, execution_context $ec) {
        if (!isset($args['user_id'])) {
            throw new \coding_exception('User id is required');
        }

        $progress = progress::for($args['user_id']);

        $progress->set_filters($args['filters'] ?? [])->fetch();

        $latest_achievement = $progress->get_latest_achievement();

        return (object) [
            'user' => (object) ($progress->get_user()->add_extra_attribute('fullname')->to_array()),
            'items' => $progress->build_progress_data_per_user_group(),
            'filters' => $progress->build_filters(),
            'latest_achievement' => $latest_achievement ? $latest_achievement->competency_name : null,
        ];
    }
}