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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity;

use coding_exception;
use context_module;
use core\collection;
use core\entities\user;
use core\orm\entity\model;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;

/**
 * Class subject_instance
 *
 * This class represents a specific activity about a specific person (subject_instance)
 *
 * @property-read int $id
 * @property-read user $subject_user The user that this activity is about
 * @property-read activity activity The top level perform activity this is an instance of
 * @property-read string status The string representation of the status
 *
 * @package mod_perform\models\activity
 */
class subject_instance extends model {

    protected $accessible_attributes = [
        'id',
        'subject_user',
    ];

    protected $model_accessor_whitelist = [
        'activity',
        'status',
        'participant_instances',
    ];

    /** @var subject_instance_entity */
    protected $entity;

    public function __construct(subject_instance_entity $subject_instance) {
        parent::__construct($subject_instance);
    }

    /**
     * @inheritDoc
     */
    protected static function get_entity_class(): string {
        return subject_instance_entity::class;
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
     * @return string The status of the user activity (subject instance),
     * in the format of a constant, not human readable string
     */
    public function get_status(): string {
        // TODO get from the subject instance($this->>entity) once implemented
        return 'IN_PROGRESS';
    }

    /**
     * @param int $participant_id
     * @return participant_section
     */
    public function get_participant_section_for_participant(int $participant_id): participant_section {
        /** @var participant_instance_entity $participant_instance_entity */
        $participant_instance_entity = participant_instance_entity::repository()
            ->with('participant_sections')
            ->where('subject_instance_id', $this->id)
            ->where('participant_id', $participant_id)
            ->order_by('id')
            ->first();

        /** @var participant_section_entity $first_participant_section */
        $first_participant_section = $participant_instance_entity->participant_sections->first();

        if (!$first_participant_section) {
            throw new coding_exception('No participant section found for this subject instance and given participant');
        }

        return participant_section::load_by_entity($first_participant_section);
    }

    /**
     * @return participant_instance[]|collection
     */
    public function get_participant_instances(): collection {
        return $this->entity->participant_instances->map_to(participant_instance::class);
    }
}
