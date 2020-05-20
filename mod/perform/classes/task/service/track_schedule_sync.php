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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\task\service;

use core\orm\collection;
use core\orm\lazy_collection;
use core\orm\query\builder;
use mod_perform\entities\activity\track;
use mod_perform\entities\activity\track_assignment;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\models\activity\track as track_model;

/**
 * This class is responsible for synchronising track schedule settings to the
 * track_user_assignment records.
 */
class track_schedule_sync {

    public function sync_all() {
        $tracks = $this->get_tracks_to_be_synced();
        foreach ($tracks as $track) {
            $this->sync_track_schedule($track);
        }
    }

    /**
     * Synchronize schedule for one track.
     *
     * @param track $track
     */
    private function sync_track_schedule(track $track): void {

        // Get active track_user_assignments and update the start-/end-dates.
        // For now we don't have to filter out the ones that already have subject_instances.
        $track_user_assignments = track_user_assignment::repository()
            ->filter_by_track_id($track->id)
            ->filter_by_active()
            ->get_lazy();

        builder::get_db()->transaction(function () use ($track, $track_user_assignments) {
            // Reset the flag.
            $track->schedule_needs_sync = false;
            $track->save();

            $track_model = track_model::load_by_entity($track);
            /** @var track_user_assignment $track_user_assignment */
            foreach ($track_user_assignments as $track_user_assignment) {
                // TODO TL-25161: adjust this to use the resolver concept.
                $track_user_assignment->period_start_date = $track_model
                    ->calculate_user_assignment_start_date($track_user_assignment->subject_user_id);
                $track_user_assignment->period_end_date = $track_model
                    ->calculate_user_assignment_end_date($track_user_assignment->subject_user_id);
                $track_user_assignment->save();
            }
        });
    }

    /**
     * Get all tracks that need schedule synchronisation.
     * Only active tracks of active activities are picked up. That means e.g. schedule changes to paused tracks
     * will not have any effect until the track is re-activated.
     *
     * @return lazy_collection
     */
    private function get_tracks_to_be_synced(): lazy_collection {
        return track::repository()
            ->filter_by_schedule_needs_sync()
            ->filter_by_active()
            ->filter_by_active_activity()
            ->get_lazy();
    }

}