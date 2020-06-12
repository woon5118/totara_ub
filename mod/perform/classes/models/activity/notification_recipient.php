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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use core\orm\entity\model;
use mod_perform\entities\activity\notification_recipient as notification_recipient_entity;

/**
 * Represents a notification setting recipient.
 *
 * @property-read integer $id ID
 * @property string $name
 * @property boolean $active is active?
 */
class notification_recipient extends model {

    protected $entity_attribute_whitelist = [
        'id',
    ];

    protected $model_accessor_whitelist = [
        'active',
    ];

    /**
     * @var notification_recipient_entity
     */
    protected $entity;

    /**
     * {@inheritdoc}
     */
    public static function get_entity_class(): string {
        return notification_recipient_entity::class;
    }

    /**
     * @return boolean
     */
    public function get_active(): bool {
        return $this->entity->active;
    }

    /**
     * Return the localised string of this recipient.
     *
     * @return string
     */
    public function get_name(): string {
        throw new \Exception('not yet implemented');
    }

    /**
     * Create a new notification recipient.
     *
     * @param notification $parent
     * @param section_relationship $relationship
     * @return self
     */
    public static function create(notification $parent, section_relationship $relationship): self {
        throw new \Exception('not yet implemented');
    }
}
