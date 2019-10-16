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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\task;


use core\task\adhoc_task;
use totara_competency\achievement_configuration;
use totara_competency\competency_achievement_aggregator;
use totara_competency\competency_aggregator_user_source_list;
use totara_competency\entities\competency;

class competency_achievement_aggregation_adhoc extends adhoc_task {

    /**
     * Do competency_achievement aggregation for the user in all specified competencies
     */
    public function execute() {
        $data = $this->get_custom_data();
        if (empty($data->user_id) || empty($data->competency_ids)) {
            throw new \coding_exception('Missing user_id or competency_ids in competency_achievement_aggregation_adhoc task');
        }

        $user_id_source = new competency_aggregator_user_source_list([$data->user_id], false);
        $aggregation_time = time();

        foreach ($data->competency_ids as $competency_id) {
            $competency = new competency($competency_id);
            $configuration = new achievement_configuration($competency);
            $competency_aggregator = new competency_achievement_aggregator($configuration, $user_id_source);
            $competency_aggregator->aggregate($aggregation_time);
        }
    }
}
