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
 * @package totara_competency
 */

namespace totara_competency\models\activity_log;

use core\orm\entity\entity;
use totara_competency\entities\scale_value;
use totara_competency\models\activity_log;
use totara_competency\pathway;
use totara_competency\entities;

class competency_achieved_via extends activity_log {

    /**
     * Load an instance of this model using data from the entity passed in.
     *
     * @param entity $entity
     * @return activity_log
     */
    public static function load_by_entity(entity $entity): activity_log {
        if (!($entity instanceof entities\competency_achievement)) {
            throw new \coding_exception('Invalid entity', 'Entity must be instance of competency_achievement');
        }

        return (new competency_achieved_via())->set_entity($entity);
    }

    /**
     * Timestamp of the date corresponding to this data.
     *
     * @return int
     */
    public function get_date(): int {
        return $this->get_entity()->time_created;
    }

    /**
     * Gets the human-readable description for an achieved_via type instance.
     *
     * @return string
     */
    public function get_description(): string {
        /** @var entities\competency_achievement $achievement */
        $achievement = $this->get_entity();
        $scale_value = new scale_value($achievement->scale_value_id);

        if (!$scale_value->exists()) {
            return get_string('activitylog_rating_value_reset', 'totara_competency');
        }

        $criteria_met = [];
        foreach ($achievement->get_achieved_via() as $via) {
            $achievement_detail_strings = pathway::fetch($via->pathway_id)
                ->get_achievement_detail()
                ->set_related_info((array) json_decode($via->related_info))
                ->get_achieved_via_strings();

            $criteria_met = array_merge($criteria_met, $achievement_detail_strings);
        }
        $criteria_met = implode(', ', array_unique($criteria_met));

        return get_string('activitylog_criteriamet', 'totara_competency', [
            'criteria_met' => $criteria_met,
            'scale_value_name' => $scale_value->name,
        ]);
    }

}
