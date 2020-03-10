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

/**
 * Class element
 *
 * This class contains the methods related to performance activity element
 * All the activity element entity properties accessible via this class
 *
 * @property-read int $id ID
 * @property-read int $context_id
 * @property-read string $plugin_name name of the element plugin that controls this element - immutable
 * @property-read string $title a user-defined title to identify and describe this element
 * @property-read int $identifier used to match elements that share the same identifier
 * @property-read string $data specific configuration data for this type of element
 * @property-read element_plugin $element_plugin
 * @property-read \context $context
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
     * @param \context $context
     * @param string $plugin_name
     * @param string $title
     * @param int $identifier
     * @param string $data
     *
     * @return static
     */
    public static function create(
        \context $context,
        string $plugin_name,
        string $title,
        int $identifier = 0,
        string $data = null
    ): self {
        $entity = new element_entity();
        $entity->context_id = $context->id;
        $entity->plugin_name = $plugin_name;
        $entity->title = $title;
        $entity->identifier = $identifier ;
        $entity->data = $data;
        $entity->save();

        /** @var self $model */
        $model = self::load_by_entity($entity);
        return $model;
    }

    /**
     * get elelment plugin
     *
     * @return element_plugin
     */
    public function get_element_plugin(): element_plugin {
        return element_plugin::load_by_plugin($this->entity->plugin_name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has_attribute(string $name): bool {
        $attributes = ['element_plugin', 'context'];
        return in_array($name, $attributes) || parent::has_attribute($name);
    }

    /**
     * @inheritDoc
     */
    public function __get($name) {
        switch ($name) {
            case 'element_plugin':
                return element_plugin::load_by_plugin($this->entity->plugin_name);
            case 'context':
                return \context_helper::instance_by_id($this->entity->context_id);
            default:
                return parent::__get($name);
        }
    }

    /**
     * Set the context for this element
     *
     * An element is "owned" by the context it belongs to. Setting a new context effectively "moves" the element.
     *
     * @param \context $context
     */
    public function update_context(\context $context) {
        $this->entity->context_id = $context->id;
        $this->entity->save();
    }

    /**
     * Update the standard properties that define this element
     *
     * @param string $title
     * @param string $data
     */
    public function update_details(string $title, string $data = null) {
        $this->entity->title = $title;
        $this->entity->data = $data;
        $this->entity->save();
    }
}
