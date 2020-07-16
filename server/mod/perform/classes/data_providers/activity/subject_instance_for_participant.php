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

use core\collection;
use core\orm\entity\repository;
use core\orm\query\builder;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\filters\subject_instance_id;
use mod_perform\entities\activity\filters\subject_instances_about;
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_user_assignment as track_user_assignment_entity;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\models\response\subject_sections;
use mod_perform\state\subject_instance\active;

/**
 * Class subject_instance
 *
 * @package mod_perform\data_providers\activity
 */
class subject_instance_for_participant {

    /** @var int */
    protected $participant_id;

    /** @var collection */
    protected $items = null;

    /** @var array */
    private $filters = [];

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
        global $CFG;
        require_once($CFG->dirroot . "/totara/coursecatalog/lib.php");

        [$totara_visibility_sql, $totara_visibility_params] = totara_visibility_where();

        $repo = subject_instance_entity::repository()
            ->as('si')
            ->with('subject_user')
            ->with('track.activity.settings')
            ->with([
                'participant_instances' => function (repository $repository) {
                    $repository->with('participant_sections.section')
                        ->with('core_relationship.resolvers')
                        ->with('subject_instance.track.activity')
                        ->with('participant_user');
                }
            ])
            ->join([track_user_assignment_entity::TABLE, 'tua'], 'track_user_assignment_id', 'id')
            ->join([track_entity::TABLE, 't'], 'tua.track_id', 'id')
            ->join([activity_entity::TABLE, 'a'], 't.activity_id', 'id')
            ->join('course', 'a.course', 'id')
            ->where_raw($totara_visibility_sql, $totara_visibility_params)
            // Eager loaded relationship resolvers because they are returned in the subject instance gql query
            ->where_exists($this->get_target_participant_exists())
            ->where('status', active::get_code())
            // Newest subject instances at the top of the list
            ->order_by('si.created_at', 'desc')
            // Order by id as well is so that tests wont fail if two rows are inserted within the same second
            ->order_by('si.id', 'desc');

        $repo->set_filters($this->filters);

        $subject_instance_entities = $repo->get();

        $this->items = $subject_instance_entities->map_to(subject_instance_model::class);

        return $this;
    }

    /**
     * get items for the model
     *
     * @return collection|subject_instance_model[]
     */
    public function get(): collection {
        if (is_null($this->items)) {
            $this->fetch();
        }

        return $this->items;
    }

    private function get_target_participant_exists(): builder {
        return participant_instance::repository()
            ->as('target_participant')
            ->where_raw('target_participant.subject_instance_id = si.id')
            ->where('participant_id', $this->participant_id)
            ->get_builder();
    }

    /**
     * Returns sections and their participants related to the current set of
     * subject instances.
     *
     * @return collection|subject_sections[] a list of subject sections.
     */
    public function get_subject_sections(): collection {
        $subject_instances = $this->get();
        return subject_sections::create_from_subject_instances($subject_instances);
    }
}