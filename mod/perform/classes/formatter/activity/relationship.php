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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\formatter\activity;

use core\orm\formatter\entity_model_formatter;
use mod_perform\models\activity\relationship as relationship_model;

defined('MOODLE_INTERNAL') || die();

/**
 * Maps the date_offset class into the GraphQL mod_perform_dynamic_date_offset type.
 */
class relationship extends entity_model_formatter {

    /** @var relationship_model */
    protected $object;

    /**
     * @inheritDoc
     */
    protected function get_map(): array {
        return [
            'core_relationship' => null,
            'is_subject' => null,
        ];
    }

}