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

namespace mod_perform\models\activity;

use core\orm\entity\model;
use mod_perform\entities\activity\element as element_entity;
use mod_perform\models\activity\element_plugin as element_plugin_model;

/**
 * Class element
 *
 * This class contains the methods related to performance activity element
 * All the activity element entity properties accessible via this class
 *
 * @package mod_perform\models\activity
 */
class element extends model {

    /**
     * @var element_entity
     */
    protected $entity;

    /**
     * @inheritDoc
     */
    public static function get_entity_class(): string {
        return element_entity::class;
    }

    /**
     * @param string $plugin_name
     * @param string $title
     * @param string $identifier
     * @param array  $data
     *
     * @return static
     */
    public static function create(string $plugin_name, string $title, string $identifier='', array $data=[]): self {
        $entity = new element_entity();
        $entity->plugin_name = $plugin_name;
        $entity->title = $title;
        $entity->identifier = $identifier ;
        $entity->data = json_encode($data ?? null);
        $entity->save();

        /** @var self $model */
        $model = self::load_by_entity($entity);
        return $model;
    }

    /**
     * get json decode data
     *
     * @return mixed
     */
    public function get_data() {
        return json_decode($this->entity->data);
    }

    /**
     * set json encode data
     *
     * @param \stdClass $data
     */
    public function set_data(\stdClass $data): void {
        $this->entity->data = json_encode($data);
    }

    /**
     * get elelment plugin
     *
     * @return element_plugin
     */
    public function get_element_plugin(): element_plugin_model {
        return element_plugin_model::load_by_plugin($this->entity->plugin_name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has_attribute(string $name): bool {
        $attributes = ['element_plugin'];
        return in_array($name, $attributes) || parent::has_attribute($name);
    }

    /**
     * @inheritDoc
     */
    public function __get($name) {
        switch ($name) {
            case 'element_plugin':
                return $this->get_element_plugin();
            case 'data':
                return $this->get_data();
            default:
                return parent::__get($name);
        }
    }

    /**
     * @inheritDoc
     */
    public function to_array(): array {
        $result = parent::to_array();
        $result['element_plugin'] = static::get_element_plugin();
        $result['data'] = $this->get_data();
        return $result;
    }
}
