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
use totara_competency\entities;

class configuration_change extends activity_log {

    /**
     * Load an instance of this model using data from the entity passed in.
     *
     * @param entity $entity
     * @return activity_log
     */
    public static function load_by_entity(entity $entity): activity_log {
        if (!($entity instanceof entities\configuration_change)) {
            throw new \coding_exception('Invalid entity', 'Entity must be instance of configuration_change');
        }

        return (new configuration_change())->set_entity($entity);
    }

    /**
     * Timestamp of the date corresponding to this data.
     *
     * @return int
     */
    public function get_date(): int {
        return $this->get_entity()->time_changed;
    }

    /**
     * Gets the human-readable description for an configuration change type instance.
     *
     * @return string
     */
    public function get_description(): string {
        /** @var entities\configuration_change $entity */
        $entity = $this->get_entity();

        switch ($entity->change_type) {
            case entities\configuration_change::CHANGED_COMPETENCY_AGGREGATION:
                return get_string('activity_log_competency_aggregation_changed', 'totara_competency');
            case entities\configuration_change::CHANGED_AGGREGATION:
                return get_string('activity_log_aggregation_changed', 'totara_competency');
            case entities\configuration_change::CHANGED_CRITERIA:
                return get_string('activity_log_criteria_change', 'totara_competency');
            case entities\configuration_change::CHANGED_MIN_PROFICIENCY:
                $data = $entity->get_decoded_related_info();
                $a = new \stdClass();
                $a->scale_value_name = (new scale_value($data['new_min_proficiency_id']))->name;
                return get_string('activity_log_minprof_changed', 'totara_competency', $a);
            default:
                throw new \coding_exception('Invalid type', 'Invalid change type: ' . $entity->change_type);
        }
    }
}