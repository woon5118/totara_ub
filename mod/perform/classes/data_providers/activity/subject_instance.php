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

use core\orm\collection;
use core\orm\query\builder;
use mod_perform\entities\activity\filters\subject_instance_id;
use mod_perform\entities\activity\participant_instance;
use mod_perform\models\activity\subject_instance as subject_instance_model;
use mod_perform\entities\activity\subject_instance as subject_instance_entity;
use mod_perform\entities\activity\filters\subject_instances_about;

class subject_instance {

    /**
     * @var int
     */
    protected $participant_id;

    /**
     * @var collection
     */
    protected $items;

    /** @var array */
    private $filters = [];

    /**
     * subject_instance constructor.
     *
     * @param int $participant_id The id of the user we would like to get activities that they are participating in
     */
    public function __construct(int $participant_id) {
        $this->participant_id = $participant_id;
    }

    /**
     * Set filter for who the activities are about (who is the subject)
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
     * Fetch subject instances that from the database
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
            ->where_exists($this->get_target_participant_exists())
            // Newest subject instances at the top of the list
            ->order_by('si.created_at', 'desc')
            // Order by id as well is so that tests wont fail if two rows are inserted within the same second
            ->order_by('si.id', 'desc');

        $repo->set_filters($this->filters);

        $this->items = $repo->get()->map_to(subject_instance_model::class);

        return $this;
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