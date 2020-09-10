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
use invalid_parameter_exception;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\entities\activity\notification_recipient as notification_recipient_entity;
use mod_perform\notification\factory;
use mod_perform\notification\recipient;
use stdClass;
use totara_core\entities\relationship as relationship_entity;
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

    /** @var integer|null */
    private $notification_id;

    /** @var integer|null */
    private $recipient_id;

    /** @var boolean */
    private $active;

    /**
     * @param notification_recipient_entity|stdClass $object
     */
    public function __construct($object) {
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
        $loader = factory::create_loader();
        if (!recipient::is_available($loader->get_possible_recipients_of($parent->class_key), $relationship)) {
            throw new invalid_parameter_exception("{$relationship->idnumber} is unavailable");
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
     * @return collection<integer, notification_recipient>
     */
    public static function load_by_notification(notification $parent, bool $active_only = false): collection {
        $loader = factory::create_loader();
        if ($loader->are_all_possible_recipients($parent->class_key)) {
            $builder = builder::table(relationship_entity::TABLE, 'r');
        } else {
            $builder = builder::table(section_entity::TABLE, 's')
                ->join([section_relationship_entity::TABLE, 'sr'], 's.id', 'sr.section_id')
                ->join([relationship_entity::TABLE, 'r'], 'r.id', 'sr.core_relationship_id')
                ->where('s.activity_id', $parent->activity->id);
        }
        $builder
            ->select(['r.id as relationship_id', 'r.sort_order'])
            ->group_by(['r.id', 'r.sort_order'])
            ->order_by('r.sort_order')
            ->map_to(function ($source) use ($parent) {
                $source->notification_id = $parent->id;
                if (empty($source->recipient_id)) {
                    $source->recipient_id = null;
                }
                return new self($source);
            });
        recipient::where_available($loader->get_possible_recipients_of($parent->class_key), $builder, 'r');
        $parent->recipients_builder($builder, $active_only);
        return $builder->get();
    }
}
