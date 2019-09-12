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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @package hierarchy_competency
 */

namespace aggregation_highest;


use totara_competency\entities\pathway_achievement;
use totara_competency\entities\scale_value;
use totara_competency\overall_aggregation;

class highest extends overall_aggregation {

    private $scale_values_cache = [];

    protected function do_aggregation(int $user_id) {
        /** @var pathway_achievement|null $highest_achievement */
        $highest_achievement = null;
        $achieved_via = [];

        foreach ($this->pathways as $pathway) {
            $achievement = pathway_achievement::get_current($pathway, $user_id);
            $value_achieved = $this->get_scale_value($achievement->scale_value_id);

            if (!is_null($value_achieved)) {
                if (is_null($highest_achievement)) {
                    $highest_achievement = $achievement;
                    $achieved_via = [$achievement];
                } else {
                    $highest_value = $this->get_scale_value($highest_achievement->scale_value_id);

                    if ($value_achieved->sortorder > $highest_value->sortorder) {
                        $highest_achievement = $achievement;
                        $achieved_via = [$achievement];
                    } else if ($value_achieved->sortorder == $highest_value->sortorder) {
                        $achieved_via[] = $achievement;
                    }
                }
            }
        }

        if (isset($highest_achievement)) {
            $this->set_user_achievement($user_id, $highest_achievement->scale_value_id, $achieved_via);
        }
    }

    private function get_scale_value(?int $id): ?scale_value {
        if (is_null($id)) {
            return null;
        }

        if (!isset($this->scale_values_cache[$id])) {
            $this->scale_values_cache[$id] = new scale_value($id);
        }

        return $this->scale_values_cache[$id];
    }
}
