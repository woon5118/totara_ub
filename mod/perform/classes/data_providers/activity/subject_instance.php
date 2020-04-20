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

namespace mod_perform\data_providers\activity;

use coding_exception;
use core\orm\collection;
use core\orm\entity\repository;
use core\orm\query\builder;
use mod_perform\entities\activity\filters\subject_instance_id;
use mod_perform\entities\activity\participant_instance;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\entities\activity\filters\subject_instances_about;
use totara_core\relationship\relationship;

/**
 * Class subject_instance
 *
 * @package mod_perform\data_providers\activity
 */
class subject_instance {

    /** @var int */
    protected $participant_id;

    /** @var collection */
    protected $items;

    /** @var array */
    private $filters = [];

    /** @var collection */
    protected $participant_instance_entities;

    /**
     * subject_instance constructor.
     *
     * @param int $participant_id The id of the user we would like to get activities that they are participating in.
     */
    public function __construct(int $participant_id) {
        $this->participant_id = $participant_id;
    }

    /**
     * Set filter for who the activities are about (who is the subject).
     *
     * @param array $about
     * @return $this
     * @see subject_instances_about::VALUE_ABOUT_SELF
     * @see subject_instances_about::VALUE_ABOUT_OTHERS
     */
    public function set_about_filter(array $about): self {
        $this->filters[] = (new subject_instances_about($this->participant_id, 'si'))->set_value($about);

        return $this;
    }

    public function set_subject_instance_id_filter(int ...$subject_instance_ids): self {
        $this->filters[] = (new subject_instance_id('si'))->set_value($subject_instance_ids);

        return $this;
    }

    /**
     * Fetch subject instances that from the database.
     *
     * @return $this
     */
    public function fetch(): self {
        $this->fetch_subject_instances();

        return $this;
    }

    /**
     * Fetch user activities that can be managed by the logged in user.
     *
     * @return $this
     */
    protected function fetch_subject_instances(): self {
        $repo = subject_instance_entity::repository()
            ->as('si')
            ->with('subject_user')
            ->with('track.activity')
            ->with([
                // Only get the participant_instance for the given participant.
                'participant_instances' => function (repository $repository) {
                    $repository->where('participant_id', $this->participant_id)
                        // Required for "your progress" and "relationship to subject".
                        ->with('activity_relationship.relationship.resolvers');
                }
            ])
            ->where_exists($this->get_target_participant_exists())
            // Newest subject instances at the top of the list
            ->order_by('si.created_at', 'desc')
            // Order by id as well is so that tests wont fail if two rows are inserted within the same second
            ->order_by('si.id', 'desc');

        $repo->set_filters($this->filters);

        $subject_instance_entities = $repo->get();

        $this->items = $subject_instance_entities->map(
            function (subject_instance_entity $subject_instance_entity) {
                $relationship_to_subject = $this->determine_relationship_to_subject($subject_instance_entity);

                return new subject_instance_model($subject_instance_entity, $relationship_to_subject);
            }
        );

        return $this;
    }

    /**
     * @param subject_instance_entity $subject_instance
     * @return string
     * @throws coding_exception
     */
    protected function determine_relationship_to_subject(subject_instance_entity $subject_instance): string {
        // Short circuit if the user (participant_id) is the subject, this is about them (self).
        // A common case where this could happen is when viewing the "Your activities" list.
        if ((int) $subject_instance->subject_user_id === (int) $this->participant_id) {
            return get_string('relation_to_subject_self', 'mod_perform');
        }

        // It's possible for one user two have two separate participant instance records for the same
        // subject instance. For example the case where one user is both the subject's manager and appraiser.
        /** @var participant_instance[] $participant_instance */
        $participant_instances = $subject_instance->participant_instances->filter('participant_id', $this->participant_id);

        // Conversely it should not be possible for there to be no participant instance records for the
        // user ($participant_id).
        if ($participant_instances->count() === 0) {
            throw new coding_exception(sprintf(
                'No participant_instance records found for subject_instance with id %d', $subject_instance->id
            ));
        }

        $relationship_names = $participant_instances->map(
            function (participant_instance $instance_for_subject) {
                $relationship_entity = $instance_for_subject->activity_relationship->relationship;

                if ($relationship_entity === null) {
                    throw new coding_exception(sprintf(
                        'perform_relationship not found for participant_instance with id %d', $instance_for_subject->id
                    ));
                }

                return (new relationship($relationship_entity))->get_name();
            }
        );

        // TODO: Return all relationships, probably with the participant_instance.id),
        // TODO: so that a particular participant instance can be selected to respond as in the ui.
        // Last() seems to more often line up with the participant instance the user will responds to as by default.
        return $relationship_names->last();
    }

    /**
     * get items for the model
     *
     * @return collection|subject_instance_model[]
     */
    public function get(): collection {
        return $this->items;
    }

    private function get_target_participant_exists(): builder {
        return participant_instance::repository()
            ->as('target_participant')
            ->where_raw('target_participant.subject_instance_id = si.id')
            ->where('participant_id', $this->participant_id)
            ->get_builder();
    }
}