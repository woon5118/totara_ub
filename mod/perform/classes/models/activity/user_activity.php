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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use context_module;
use core\entities\user;
use core\orm\entity\model;
use mod_perform\entities\activity\subject_instance;

/**
 * Class user_activity
 *
 * This class represents a specific activity about a specific person (subject_instance)
 *
 * @method static load_by_entity(subject_instance $entity)
 * @method static load_by_id(int $id)
 *
 * @package mod_perform\models\activity
 */
class user_activity extends model {

    protected $accessible_attributes = [];

    /** @var subject_instance */
    protected $entity;

    public function __construct(subject_instance $subject_instance) {
        parent::__construct($subject_instance);
    }

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return subject_instance::class;
    }

    public function get_subject_instance_id(): int {
        return $this->entity->id;
    }

    /**
     * @return activity The abstract perform activity that this user activity is an instance of
     */
    public function get_activity(): activity {
        $activity_entity = $this->entity->activity();

        return activity::load_by_entity($activity_entity);
    }

    /**
     * Get the context object for the overarching abstract perform activity (perform in the database).
     *
     * @return context_module
     */
    public function get_context(): context_module {
        return $this->get_activity()->get_context();
    }

    /**
     * @return user The user that this activity is about
     */
    public function get_subject(): user {
        return $this->entity->subject_user;
    }

    /**
     * @return string The status of the user activity (subject instance),
     * in the format of a constant, not human readable string
     */
    public function get_status(): string {
        // TODO get from the subject instance($this->>entity) once implemented
        return 'IN_PROGRESS';
    }
}