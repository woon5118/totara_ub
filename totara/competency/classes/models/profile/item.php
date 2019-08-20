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

use coding_exception;
use core\orm\collection;
use core\orm\query\builder;
use tassign_competency\entities\assignment;
use totara_assignment\entities\user;
use totara_competency\data_providers\progress;
use totara_competency\entities\scale as scale_entity;
use totara_competency\entities\scale_value;
use totara_competency\models\basic_model;

/**
 * Class profile_progress
 *
 * This is a profile progress item model scaffolding, it has the following properties available:
 *
 *  - Key -> md5 of some attributes it's grouped by
 *  - Assignments -> [Assignment] - a collection of related assignment models
 *  - Overall progress -> int - Overall progress value per this group
 *
 *
 * @property-read string $key A key uniquely identifying this progress item
 * @property-read collection $assignments Collection of assignments for this user group
 * @property-read collection $filters Collection of filters
 * @property-read string $latest_achievement Latest achieved competency name (if any)
 * @package totara_competency\models
 */
class item extends basic_model {

    public static function build_from_assignments(collection $assignments) {
        $progress = new collection();

        foreach ($assignments->all() as $assignment) {
            if (!$progress->item($key = $this->build_key($assignment->type, $assignment->user_group_type, $assignment->user_group_id))) {
                $progress->set((object) [
                    'key' => $key,
                    'name' => $assignment->get_progress_name(),
                    'user' => $this->user,
                    'assignments' => new collection([$assignment]),
                    'overall_progress' => 0,
                    'graph' => []
                ], $key);
            } else {
                $progress->item($key)->assignments->append($assignment);
            }
        }

        foreach ($progress as $item) {
            // Let's iterate over progress items and calculate individual progress percentage
            $competent_count = array_reduce($item->assignments->all(), function($count, $assignment) {
                if (!$assignment->achievement) {
                    return $count;
                } else {
                    return $count + intval($assignment->achievement->proficient);
                }
            }, 0);

            $item->overall_progress = round($competent_count / count($item->assignments) * 100);

            // Now let's insert my value percentage and minimum value percentage.
            $item->assignments->transform(function(assignment $assignment) {
                return $this->calculate_proficiency_chart_data($assignment);
            });

        }
    }

    protected static function build_key($type, $user_group_type, $user_group_id) {
        return md5("$type/$user_group_type/$user_group_id");
    }

}