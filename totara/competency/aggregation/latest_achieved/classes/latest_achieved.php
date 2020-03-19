<?php
/*
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package aggregation_latest_achieved
 */

namespace aggregation_latest_achieved;


use totara_competency\entities\pathway_achievement;
use totara_competency\overall_aggregation;

class latest_achieved extends overall_aggregation {

    /**
     * Aggregate the user and return the latest value achieved by the user
     * If the user achieved a value through multiple paths during the same aggregation cycle (cron run)
     * the pathways' sortorder is used to determine which value to use
     *
     * @param int $user_id
     * @return void
     */
    protected function do_aggregation(int $user_id): void {
        /** @var pathway_achievement|null $highest_achievement */
        $latest_achievement = null;
        $latest_value = null;
        $latest_pw_sortorder = null;
        $achieved_via = [];

        // We load all current achievement for all the pathways in one go
        // to reduce the number of queries
        $current_achievements = $this->get_current_pathway_achievements_for_user($this->pathways, $user_id);
        foreach ($this->pathways as $pathway) {
            $achievement = $this->get_or_create_current_pathway_achievement($current_achievements, $pathway, $user_id);

            $value_achieved = $achievement->scale_value;

            if (!is_null($achievement->last_aggregated)) {
                if (is_null($latest_achievement)) {
                    $latest_achievement = $achievement;
                    $latest_value = $value_achieved;
                    $latest_pw_sortorder = $pathway->get_sortorder();
                    $achieved_via = [$achievement];
                } else {
                    if ($achievement->date_achieved > $latest_achievement->date_achieved) {
                        $latest_achievement = $achievement;
                        $latest_value = $value_achieved;
                        $latest_pw_sortorder = $pathway->get_sortorder();
                        $achieved_via = [$achievement];
                    } else if ($achievement->date_achieved == $latest_achievement->date_achieved) {
                        if ($pathway->get_sortorder() < $latest_pw_sortorder) {
                            $latest_achievement = $achievement;
                            $latest_value = $value_achieved;
                            $latest_pw_sortorder = $pathway->get_sortorder();
                            $achieved_via = [$achievement];
                        } else if ($pathway->get_sortorder() == $latest_pw_sortorder) {
                            $achieved_via[] = $achievement;
                        }
                    }
                }
            }
        }

        if (isset($latest_achievement)) {
            $this->set_user_achievement($user_id, $achieved_via, $latest_value);
        }
    }

}
