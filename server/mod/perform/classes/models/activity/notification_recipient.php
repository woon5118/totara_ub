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
use core\orm\query\builder;
use mod_perform\entities\activity\notification_recipient as notification_recipient_entity;
use stdClass;
use totara_core\relationship\relationship;

/**
 * Represents a notification setting recipient.
 *
 * @property integer $id
 * @property integer $relationship_id
 * @property integer $notification_id
 * @property integer|null $recipient_id
 * @property relationship $relationship
 * @property string $name
 * @property boolean $active is active?
 */
class notification_recipient {
    /** @var integer */
    private $relationship_id;

    /** @var integer */
    private $notification_id;

    /** @var integer|null */
    private $recipient_id;

    /** @var boolean */
    private $active;

    /**
     * @param notification_recipient_entity|stdClass $object
     */
    private function __construct($object) {
        if ($object instanceof notification_recipient_entity) {
            $this->relationship_id = $object->core_relationship_id;
            $this->notification_id = $object->notification_id;
            $this->recipient_id = $object->id;
            $this->active = $object->active;
        } else {
            $this->relationship_id = $object->relationship_id;
            $this->notification_id = $object->notification_id;
            $this->recipient_id = $object->recipient_id;
            $this->active = !empty($object->active);
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) {
        $methodname = 'get_'.$name;
        if (!method_exists($this, $methodname)) {
            throw new coding_exception('unknown property: '.$name);
        }
        return $this->{$methodname}();
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function has_attribute(string $name): bool {
        $methodname = 'get_'.$name;
        return method_exists($this, $methodname);
    }

    /**
     * @return integer
     */
    public function get_id(): int {
        return $this->relationship_id;
    }

    /**
     * @return relationship
     */
    public function get_relationship(): relationship {
        return relationship::load_by_id($this->relationship_id);
    }

    /**
     * @return integer
     */
    public function get_relationship_id(): int {
        return $this->relationship_id;
    }

    /**
     * @return integer
     */
    public function get_notification_id(): int {
        return $this->notification_id;
    }

    /**
     * @return integer|null
     */
    public function get_recipient_id(): ?int {
        return $this->recipient_id;
    }

    /**
     * @return boolean
     */
    public function get_active(): bool {
        return $this->active;
    }

    /**
     * Return the localised string of this recipient.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->get_relationship()->get_name();
    }

    /**
     * Create a new notification recipient.
     *
     * @param notification $parent
     * @param relationship $relationship
     * @param boolean $active
     * @return self
     */
    public static function create(notification $parent, relationship $relationship, bool $active = false): self {
        if (!$parent->exists()) {
            throw new coding_exception('parent record does not exist');
        }
        $entity = new notification_recipient_entity();
        $entity->notification_id = $parent->get_id();
        $entity->core_relationship_id = $relationship->get_id();
        $entity->active = $active;
        $entity->save();
        $inst = new self($entity);
        return $inst;
    }

    /**
     * @param boolean $active
     * @return self
     */
    public function activate(bool $active): self {
        if (!$this->recipient_id) {
            throw new coding_exception('not available; call create() instead');
        }
        $entity = notification_recipient_entity::repository()->find_or_fail($this->recipient_id);
        $entity->active = $active;
        $entity->save();
        return $this;
    }

    /**
     * Load all notification recipients.
     *
     * @param notification $parent
     * @param boolean $active_only get only active recipients
     * @return collection
     */
    public static function load_by_notification(notification $parent, bool $active_only = false): collection {
        $notify_id = $parent->id;
        $params = ['activity_id' => $parent->activity->id];
        $notification_id_part = ' AND 1 != 1';
        if ($notify_id !== null) {
            $notification_id_part = ' AND nr.notification_id = :notification_id';
            $params['notification_id'] = $notify_id;
        }
        $active_only_part = $active_only ? 'AND nr.active <> 0' : '';

        $sql = "
            SELECT 
                r.id as relationship_id,
                nr.id as recipient_id,
                nr.active as active,
                r.sort_order
            FROM {perform_section} s 
            JOIN {perform_section_relationship} sr ON s.id = sr.section_id
            JOIN {totara_core_relationship} r ON sr.core_relationship_id = r.id 
            LEFT JOIN {perform_notification_recipient} nr ON sr.core_relationship_id = nr.core_relationship_id
                {$notification_id_part}
            WHERE s.activity_id = :activity_id
                {$active_only_part}
            GROUP BY
                r.id,
                nr.id,
                nr.active,
                r.sort_order
            ORDER BY r.sort_order
        ";

        $records = builder::get_db()->get_records_sql($sql, $params);

        return collection::new($records)
            ->map_to(function ($source) use ($notify_id) {
                $source->notification_id = $notify_id;
                return new self($source);
            });
    }
}
