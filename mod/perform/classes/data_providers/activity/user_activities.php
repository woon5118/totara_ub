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
use mod_perform\entities\activity\participant_instance;
use mod_perform\entities\activity\subject_instance;
use mod_perform\models\activity\user_activity as user_activity_model;
use mod_perform\entities\activity\filters\user_activities_about;

class user_activities {

    /**
     * @var int
     */
    protected $participant_id;

    /**
     * @var collection
     */
    protected $items;

    /** @var array */
    private $filters;

    /**
     * user_activity constructor.
     *
     * @param int $participant_id The id of the user we would like to get activities they are a participant in
     */
    public function __construct(int $participant_id) {
        $this->participant_id = $participant_id;
    }

    /**
     * Set filter for who the activities are about (who is the subject)
     *
     * @see user_activities_about::VALUE_ABOUT_SELF
     * @see user_activities_about::VALUE_ABOUT_OTHERS
     * @param array $about
     * @return $this
     */
    public function set_about_filter(array $about): self {
        $this->filters[] = (new user_activities_about($this->participant_id, 'si'))->set_value($about);

        return $this;
    }

    /**
     * Fetch user activities from the database
     *
     * @return $this
     */
    public function fetch(): self {
        $this->fetch_user_activities();

        return $this;
    }

    /**
     * Fetch user activities that can be managed by the logged in user.
     *
     * @return $this
     */
    protected function fetch_user_activities(): self {
        $repo = subject_instance::repository()
            ->as('si')
            ->with('subject_user')
            ->with('track.activity')
            ->where_exists($this->get_target_participant_exists())
            ->order_by('si.created_at', 'desc');

        $repo->set_filters($this->filters);

        $this->items = $repo->get()->map_to(user_activity_model::class);

        return $this;
    }

    /**
     * get items for the model
     *
     * @return collection|user_activity_model[]
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