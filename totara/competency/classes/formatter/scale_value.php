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

namespace totara_competency\formatter;

use core\orm\formatter\entity_formatter;
use totara_core\formatter\field\date_field_formatter;
use totara_core\formatter\field\string_field_formatter;

/**
 * @property scale $object
 */
class scale_value extends entity_formatter {

    protected function get_map(): array {
        return [
            'id' => null,
            'name' => string_field_formatter::class,
            'idnumber' => string_field_formatter::class,
            'description' => string_field_formatter::class,
            'scaleid' => null,
            'numericscore' => null,
            'sortorder' => null,
            'timemodified' => date_field_formatter::class,
            'usermodified' => null,
            'proficient' => null
        ];
    }

}