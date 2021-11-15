<?php
/**
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\formatter\activity;

use core\orm\formatter\entity_model_formatter;
use core\webapi\formatter\field\string_field_formatter;

defined('MOODLE_INTERNAL') || die();

/**
 * Maps the track model class into the GraphQL mod_perform_track type.
 */
class track extends entity_model_formatter {

    /**
     * @var \mod_perform\models\activity\track
     */
    protected $object;

    /**
     * {@inheritdoc}
     */
    protected function get_map(): array {
        return [
            'id' => null,
            'description' => string_field_formatter::class,
            'status' => null,
            'subject_instance_generation' => null,
            'schedule_is_open' => null,
            'schedule_is_fixed' => null,
            'schedule_fixed_from' => function () {
                return $this->object->get_schedule_fixed_from_setting();
            },
            'schedule_fixed_to' => function () {
                return $this->object->get_schedule_fixed_to_setting();
            },
            'schedule_dynamic_from' => null,
            'schedule_dynamic_to' => null,
            'schedule_dynamic_source' => null,
            'schedule_use_anniversary' => null,
            'due_date_is_enabled' => null,
            'due_date_is_fixed' => null,
            'due_date_fixed' => function () {
                return $this->object->get_due_date_fixed_setting();
            },
            'due_date_offset' => null,
            'repeating_is_enabled' => null,
            'repeating_type' => null,
            'repeating_offset' => null,
            'repeating_is_limited' => null,
            'repeating_limit' => null,
            'created_at' => null,
            'updated_at' => null,
            'assignments' => null,
            'can_assign_positions' => null,
            'can_assign_organisations' => null
        ];
    }

}
