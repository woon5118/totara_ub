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
use totara_competency\base_achievement_detail;
use totara_competency\entities\pathway_achievement;
use totara_competency\entities\scale_value;
use totara_competency\models\activity_log;
use totara_competency\pathway;
use totara_competency\pathway_factory;
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

        $data = [];
        foreach ($achievement->get_achieved_via() as $via) {
            /** @var pathway_achievement $via */
            $data[] = [
                // Skipping the pathway factory as we want to ignore the enabled check.
                'pathway_type' => pathway::fetch($via->pathway_id)->get_path_type(),
                'related_info' => json_decode($via->related_info),
            ];
        }
        $data[] = ['scale_value' => new scale_value($achievement->scale_value_id)];

        $scale_value = array_pop($data)['scale_value'];
        $criteria_met = [];
        foreach ($data as $pathway_achieved_by) {
            $pathway_type = $pathway_achieved_by['pathway_type'];
            $namespace = pathway_factory::get_namespace($pathway_type);
            $detail_classname = $namespace . '\\achievement_detail';
            if (!is_subclass_of($detail_classname, base_achievement_detail::class)) {
                throw new \coding_exception('Not detail class found', 'No achievement_detail class found for ' . $pathway_type);
            }
            /** @var base_achievement_detail $achievement_detail */
            $achievement_detail = new $detail_classname();
            $achievement_detail->set_related_info($pathway_achieved_by['related_info']);
            $criteria_met = array_merge($criteria_met, $achievement_detail->get_achieved_via_strings());
        }

        $a = new \stdClass();
        $a->criteria_met = implode(', ', array_unique($criteria_met));
        $a->scale_value_name = $scale_value->name;

        return get_string('activitylog_criteriamet', 'totara_competency', $a);
    }
}
