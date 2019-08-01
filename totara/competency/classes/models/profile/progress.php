<?php
/*
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com
 * @package totara_competency
 */

namespace totara_competency\models\profile;

use core\orm\collection;
use totara_assignment\entities\user;
use totara_competency\data_providers\progress as progress_provider;
use totara_competency\models\basic_model;

/**
 * Class profile_progress
 *
 * This is a generic profile progress model scaffolding, it has the following properties available:
 *
 *  - User -> user entity
 *  - Items -> [Progress item] - a collection of objects containing individual progress items per assignments grouped
 *                               by almost user group name (it's slightly more conditional)
 *  - Filters -> [Filter] - a collection of filter items
 *  - Latest achievement -> Competency achievement entity - Latest competency achieved by user (if any)
 *
 *
 * @property-read user $user User the progress is for
 * @property-read collection $items Collection of progress items
 * @property-read collection $filters Collection of filters
 * @property-read string $latest_achievement Latest achieved competency name (if any)
 * @package totara_competency\models
 */
class progress extends basic_model {

    public static function for(int $user_id, $filters = []) {
        $profile_progress = new static();

        $progress = progress_provider::for($user_id)
            ->set_filters($filters)
            ->fetch();

        $profile_progress->set_attribute('user', $progress->get_user()->to_the_origins())
            ->set_attribute('items', $progress->build_progress_data_per_user_group())
            ->set_attribute('filters', $progress->build_filters())
            ->set_attribute('latest_achievement', $progress->get_latest_achievement() ? $progress->get_latest_achievement()->competency_name : null);

        return $profile_progress;
    }
}