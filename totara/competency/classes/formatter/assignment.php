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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_userstatus
 */

namespace totara_competency\formatter;

use totara_competency\models\assignment as assignment_model;
use totara_core\formatter\field\date_field_formatter;
use totara_core\formatter\field\string_field_formatter;
use totara_core\formatter\formatter;

/**
 * @property assignment_model $object
 */
class assignment extends formatter {

    protected function get_map(): array {
        return [
            'id' => null,
            'type' => null,
            'competency_id' => null,
            'user_group_type' => null,
            'user_group_id' => null,
            'optional' => null,
            'status' => null,
            'created_by' => null,
            'created_at' => date_field_formatter::class,
            'updated_at' => date_field_formatter::class,
            'archived_at' => date_field_formatter::class,
            'user_group' => null,
            'competency' => null,
            'status_name' => null,
            'type_name' => null,
            'progress_name' => string_field_formatter::class,
            'reason_assigned' => string_field_formatter::class,
        ];
    }

    protected function get_field(string $field) {
        return $this->object->get_field($field);
    }

    protected function has_field(string $field): bool {
        return $this->object->has_field($field);
    }

}