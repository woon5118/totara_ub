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
 * Represents a question or other type of element which can be displayed to users within a performance activity.
 *
 * @property-read int $id ID
 * @property-read int $context_id
 * @property-read string $plugin_name name of the element plugin that controls this element
 * @property-read string $title a user-defined title to identify and describe this element
 * @property-read int $identifier used to match elements that share the same identifier
 * @property-read string $data specific configuration data for this type of element
 * @property-read \context $context
 * @property-read element_plugin $element_plugin
 *
 * @package mod_perform\models\activity
 */
class element extends model {

    protected $entity_attribute_whitelist = [
        'id',
        'context_id',
        'plugin_name',
        'title',
        'identifier',
        'data',
    ];

    protected $model_accessor_whitelist = [
        'context',
        'element_plugin',
    ];

    /**
     * @var element_entity
     */
    protected $entity;

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
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
     * Get the element plugin that this element is based on
     *
     * @return element_plugin
     */
    public function get_element_plugin(): element_plugin {
        return element_plugin::load_by_plugin($this->entity->plugin_name);
    }

    /**
     * Get the context that this element belongs to
     *
     * @return \context
     */
    public function get_context(): \context {
        return \context_helper::instance_by_id($this->entity->context_id);
    }

    /**
     * Update the context for this element
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
     * @param $data
     */
    public function update_details(string $title, string $data = null) {
        $this->entity->title = $title;
        $this->entity->data = $data;
        $this->entity->save();
    }

}
