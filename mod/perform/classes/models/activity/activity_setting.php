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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use moodle_exception;
use core\orm\entity\model;
use mod_perform\entities\activity\activity_setting as activity_setting_entity;

/**
 * Represents a single setting for a parent activity.
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $value
 * @property-read activity $activity
 */
class activity_setting extends model {
    /**
     * @var activity_setting_entity
     */
    protected $entity;

    // List of known "out of the box" setting names.
    public const CLOSE_ON_COMPLETION = 'close_on_completion';
    public const MULTISECTION = 'multisection';

    protected $entity_attribute_whitelist = [
        'id',
        'name',
        'value'
    ];

    protected $model_accessor_whitelist = [
        'activity'
    ];

    /**
     * {@inheritdoc}
     */
    protected static function get_entity_class(): string {
        return activity_setting_entity::class;
    }

    /**
     * Creates a setting record for the given parent activity.
     *
     * @param activity $parent parent activity.
     * @param string $name setting name.
     * @param mixed $value setting value.
     *
     * @return activity_setting the newly created setting model.
     */
    public static function create(
        activity $parent,
        string $name,
        $value
    ): activity_setting {
        if (!$parent->can_manage()) {
            throw new moodle_exception('nopermissions', '', '', 'create setting');
        }

        $allowed = [
            self::CLOSE_ON_COMPLETION,
            self::MULTISECTION
        ];
        if (!in_array($name, $allowed)) {
            throw new moodle_exception("invalid activity setting name: $name");
        }

        if (is_bool($value)) {
            $value = (int)$value;
        }

        $entity = new activity_setting_entity();
        $entity->activity_id = $parent->id;
        $entity->name = $name;
        $entity->value = (string)$value;

        $entity->save();

        return new activity_setting($entity);
    }

    /**
     * Get the parent activity model.
     *
     * @return activity the parent activity.
     */
    public function get_activity(): activity {
        return activity::load_by_entity($this->entity->activity);
    }

    /**
     * Sets the setting value.
     *
     * @param mixed $value new value.
     *
     * @return activity_setting the setting model.
     */
    public function update($value): activity_setting {
        if (!$this->activity->can_manage()) {
            throw new moodle_exception('nopermissions', '', '', 'update setting');
        }

        if (is_bool($value)) {
            $value = (int)$value;
        }

        $this->entity->value = (string)$value;
        $this->entity->save();

        return $this;
    }

    /**
     * Deletes the activity settings. Note: after this, the model is invalid.
     */
    public function delete(): void {
        if (!$this->activity->can_manage()) {
            throw new moodle_exception('nopermissions', '', '', 'delete setting');
        }
        $this->entity->delete();
    }
}
