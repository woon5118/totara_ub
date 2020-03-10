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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\entities\activity;

use core\orm\entity\entity;

/**
 * Section element entity
 *
 * @property-read int $id ID
 * @property int $context_id the context which owns this element, a performance activity or category/tenant
 * @property string $plugin_name name of the element plugin that controls this element
 * @property string $title a user-defined title to identify and describe this element
 * @property int $identifier used to match elements that share the same identifier
 * @property string $data data specific to this type of entity
 *
 * @method static element_repository repository()
 *
 * @package mod_perform\entities
 */
class element extends entity {
    public const TABLE = 'perform_element';
}