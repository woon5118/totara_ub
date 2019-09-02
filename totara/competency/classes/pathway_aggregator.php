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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency;


use core\orm\collection;
use totara_competency\entities\pathway_achievement;

class pathway_aggregator {

    private $pathway;

    public function __construct(pathway $pathway) {
        $this->pathway = $pathway;
    }

    public function aggregate($user_ids, $aggregation_time = null) {
        if (empty($user_ids)) {
            return;
        }

        if (is_null($aggregation_time)) {
            $aggregation_time = time();
        }

        /** @var pathway_achievement[]|collection $achievements */
        $achievements = pathway_achievement::repository()
            ->where('pathway_id', '=', $this->pathway->get_id())
            ->where('user_id', '=', $user_ids)
            ->where('status', '=', pathway_achievement::STATUS_CURRENT)
            ->get();

        $needs_new_achievement = array_fill_keys($user_ids, null);

        foreach ($achievements as $achievement) {
            $aggregated_achievement_detail = $this->pathway->aggregate_current_value($achievement->user_id);

            if ($aggregated_achievement_detail->get_scale_value_id() == $achievement->scale_value_id) {
                // No new achievement required.
                $achievement->last_aggregated = $aggregation_time;
                $achievement->save();
                unset($needs_new_achievement[$achievement->user_id]);
                continue;
            } else {
                // The value has changed.
                $achievement->archive();
                $needs_new_achievement[$achievement->user_id] = $aggregated_achievement_detail;
            }
        }

        foreach ($needs_new_achievement as $user_id => $achievement_detail) {
            if (is_null($achievement_detail)) {
                $achievement_detail = $this->pathway->aggregate_current_value($user_id);
            }

            $new_achievement = new pathway_achievement();
            $new_achievement->pathway_id = $this->pathway->get_id();
            $new_achievement->user_id = $user_id;
            $new_achievement->scale_value_id = $achievement_detail->get_scale_value_id();
            $new_achievement->status = pathway_achievement::STATUS_CURRENT;
            $new_achievement->last_aggregated = $aggregation_time;
            $new_achievement->date_achieved = $aggregation_time;
            $new_achievement->related_info = json_encode($achievement_detail->get_related_info());
            $new_achievement->save();
        }
    }
}