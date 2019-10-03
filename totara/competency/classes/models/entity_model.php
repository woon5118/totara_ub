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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\models;

use coding_exception;
use core\orm\entity\entity;
use JsonSerializable;

abstract class entity_model implements JsonSerializable {

    /**
     * Entity this model is based upon
     *
     * @var entity
     */
    protected $entity;

    public function __construct(entity $entity) {
        if (!$entity->exists()) {
            throw new coding_exception('Can load only existing entities');
        }

        $this->entity = $entity;
    }

    /**
     * Get associated entity ID
     *
     * @return int
     */
    public function get_id(): int {
        return $this->entity->id;
    }

    /**
     * Convert the model to array
     *
     * @return array
     */
    public function to_array(): array {
        return $this->entity->to_array();
    }

    /**
     * Magic attribute getter
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name) {
        return $this->entity->get_attribute($name);
    }

    /**
     * Required to use it in GraphQL implementation as it checks first whether the field is set
     * @param $name
     *
     * @return bool
     */
    public function __isset($name): bool {
        return $this->has_attribute($name);
    }

    /**
     * A helper to check whether an attribute with a given name can be accessed
     *
     * @param string $name
     * @return bool
     */
    public function has_attribute(string $name): bool {
        return $this->entity->has_attribute($name) || $this->entity->relation_exists($name);
    }

    /**
     * Define what should be serialized to JSON
     *
     * @return array|mixed
     */
    public function jsonSerialize() {
        return $this->to_array();
    }
}