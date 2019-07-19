<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @package tassign_competency
 */

namespace totara_assignment\entities;


use ReflectionClass;
use tassign_competency\entities\helpers\hierarchy_crumbtrail_helper;
use core\orm\entity\entity;

/**
 * Class hierarchy item
 *
 * @property int $typeid
 * @property int $parentid
 * @property-read array $crumbtrail Totara sync flag
 * @property-read hierarchy_framework $framework Hierarchy item framework
 * @property-read string $display_name
 * @property-read hierarchy_item $parent
 * @property-read collection $children
 * @property-read hierarchy_type $type
 *
 * @package tassign_competency\entities
 */
abstract class hierarchy_item extends entity {

    /**
     * Always append default name
     *
     * @var array
     */
    protected $extra_attributes = [
        'display_name'
    ];

    /**
     * Hierarchy item framework
     *
     * @var hierarchy_framework
     */
    protected $framework = null;

    /**
     * Hierarchy item crumbtrail cached
     *
     * @var array
     */
    protected $crumbtrail_cached = null;

    /**
     * @var hierarchy_type
     */
    protected $type = null;

    /**
     * If this is called this item will have a crumbtrail attribute loaded when to_array() is called
     *
     * @return $this
     */
    public function with_crumbtrail(): hierarchy_item {
        return $this->add_extra_attribute('crumbtrail');
    }

    /**
     * @return array
     */
    public function get_crumbtrail_attribute(): array {
        if (!$this->crumbtrail_cached) {
            $this->crumbtrail_cached = (new hierarchy_crumbtrail_helper($this))->generate();
        }
        return $this->crumbtrail_cached;
    }

    /**
     * Get unified display name that can be referred to safely, just an alias in this case
     *
     * @return string
     */
    protected function get_display_name_attribute() {
        return $this->fullname;
    }

    /**
     * Get hierarchy item type, by default matches class name, can be overridden on a class level
     *
     * @return string
     */
    public function get_type() {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * Get related framework
     *
     * @return hierarchy_framework|null
     */
    protected function get_framework_attribute(): ?hierarchy_framework {

        if (!$this->framework && $fw = self::get_framework_class()) {
            $this->framework = new $fw($this->frameworkid);
        }

        return $this->framework;
    }

    /**
     * Get framework entity class name
     *
     * @return string|null
     */
    public static function get_framework_class(): ?string {
        $fw = static::class . '_framework';

        return is_a($fw, hierarchy_framework::class, true) ? $fw : null;
    }

    /**
     * Get associated type entity
     *
     * @return hierarchy_type|null
     */
    public function get_type_attribute(): ?hierarchy_type {
        if ($this->typeid && !$this->type && $type = self::get_type_class()) {
            $this->type = new $type($this->typeid);
        }

        return $this->type;
    }

    /**
     * Get framework entity class name
     *
     * @return string|null
     */
    public static function get_type_class(): ?string {
        $fw = static::class . '_type';

        return is_a($fw, hierarchy_type::class, true) ? $fw : null;
    }

    /**
     * Returns all children of the current item
     *
     * @return collection|hierarchy_item[]
     */
    public function get_children_attribute(): collection {
        return static::repository()
            ->where('parentid', $this->id)
            ->get();
    }

    /**
     * Returns the parent item if there's any
     *
     * @return hierarchy_item|null
     */
    public function get_parent_attribute(): ?hierarchy_item {
        return $this->parentid ? static::repository()->find($this->parentid) : null;
    }

}
