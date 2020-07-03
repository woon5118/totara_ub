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

use coding_exception;
use core\orm\collection;
use core\orm\entity\model;
use core\orm\query\builder;
use totara_core\entities\relationship as relationship_entity;
use totara_core\entities\relationship_resolver as relationship_resolver_entity;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\notification_message as notification_message_entity;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use totara_core\relationship\relationship;

/**
 * Represents a notification setting recipient.
 *
 * @property integer $id
 * @property integer $sent_at
 * @property integer $notification_id
 * @property integer $core_relationship_id
 * @property notification $notification
 * @property relationship $relationship
 */
class notification_message extends model {
    protected $entity_attribute_whitelist = [
        'id',
        'sent_at',
        'notification_id',
        'core_relationship_id',
    ];

    protected $model_accessor_whitelist = [
        'notification',
        'relationship',
    ];

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return notification_message_entity::class;
    }

    /**
     * @return notification
     */
    public function get_notification(): notification {
        return notification::load_by_id($this->notification_id);
    }

    /**
     * @return relationship
     */
    public function get_relationship(): relationship {
        return relationship::load_by_id($this->core_relationship_id);
    }

    /**
     * Create a new notification recipient.
     *
     * @param notification_recipient $recipient
     * @param integer|null $timestamp
     * @return self
     */
    public static function create(notification_recipient $recipient, int $timestamp = null): self {
        $entity = new notification_message_entity();
        $entity->notification_id = $recipient->notification_id;
        $entity->core_relationship_id = $recipient->relationship_id;
        $entity->sent_at = $timestamp ?? time();
        $entity->save();
        $inst = new self($entity);
        return $inst;
    }

    /**
     * Load all notification recipients.
     *
     * @param notification $parent
     * @return collection
     */
    public static function load_all_latest_by_notification(notification $parent): collection {
        // select cr.id as crid, coalesce(m.sent_at, 0) as sent_at
        // from {perform_section_relationship} sr
        // join {totara_core_relationship} cr on cr.id=sr.core_relationship_id
        // left join (
        // select max(nm.sent_at) as sent_at,nm.core_relationship_id
        // from {perform_notification_message} nm
        // group by nm.core_relationship_id
        // ) m on m.core_relationship_id=cr.id

        throw new \Exception('not yet implemented');
    }
}
