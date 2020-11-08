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
 * @author Kunle Odusan <kunle.odusan@totaralearning.com>
 */

namespace mod_perform\entity\activity;

use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;

/**
 * Section element entity
 *
 * Properties:
 * @property-read int $id ID
 * @property int $context_id the context which owns this element, a performance activity or category/tenant
 * @property string $plugin_name name of the element plugin that controls this element
 * @property string $title a user-defined title to identify and describe this element
 * @property int $identifier_id used to match elements that share the same identifier
 * @property string $data configuration data specific to this type of element
 * @property bool $is_required used to check response required or optional
 * @property-read element_identifier $element_identifier
 *
 * @method static element_repository repository()
 *
 * @package mod_perform\entity
 */
class element extends entity {
    public const TABLE = 'perform_element';

    /**
     * Cast is_required to bool type.
     *
     * @return bool
     */
    protected function get_is_required_attribute(): ?bool {
        $value = $this->get_attributes_raw()['is_required'];
        if (is_null($value)) {
            return null;
        } else {
            return (bool) $this->get_attributes_raw()['is_required'];
        }
    }

    /**
     * Get the element_identifier
     *
     * @return belongs_to
     */
    public function element_identifier(): belongs_to {
        return $this->belongs_to(element_identifier::class, 'identifier_id');
    }

    /**
     * An element belongs to a section.
     *
     * @return belongs_to
     */
    public function section_element(): belongs_to {
        return $this->belongs_to(section_element::class, 'id', 'element_id');
    }
}