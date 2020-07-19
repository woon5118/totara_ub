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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\orm\entity\entity;

/**
 * Section element response entity
 *
 * Properties:
 * @property-read int $id ID
 * @property int section_element_id $context_id the section element this is a answer to
 * @property int participant_instance_id $plugin_name the participant instance for the person making this answer
 * @property string $response_data JSON encoded question response data
 *
 * @method static element_response_repository repository()
 *
 * @package mod_perform\entities
 */
class element_response extends entity {
    public const TABLE = 'perform_element_response';
}