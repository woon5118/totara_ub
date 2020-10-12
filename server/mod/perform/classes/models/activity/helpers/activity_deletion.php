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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\models\activity\helpers;

use core\orm\query\builder;
use mod_perform\entities\activity\activity as activity_entity;
use mod_perform\entities\activity\element as element_entity;
use mod_perform\entities\activity\element_response as element_response_entity;
use mod_perform\entities\activity\manual_relationship_selection;
use mod_perform\entities\activity\participant_section as participant_section_entity;
use mod_perform\entities\activity\section as section_entity;
use mod_perform\entities\activity\section_element as section_element_entity;
use mod_perform\entities\activity\section_relationship as section_relationship_entity;
use mod_perform\entities\activity\track as track_entity;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\entities\activity\track_user_assignment_via;
use mod_perform\event\activity_deleted;
use mod_perform\models\activity\activity;

/**
 * Class activity_deletion
 * Responsible for handling the deletion of a perform activity and it's child records.
 *
 * This class is not responsible for deleting the associated perform container and contexts.
 *
 * @see \container_perform\perform::delete()
 */
class activity_deletion {

    /**
     * @var activity
     */
    protected $activity;

    public function __construct(activity $activity) {
        $this->activity = $activity;
    }

    /**
     * Delete the activity and the associated child models.
     * Will only delete elements that are not used by any other perform activities.
     *
     * An activity_deleted event will be triggered on successful deletions.
     *
     * @return activity_deletion
     * @see activity_deleted
     */
    public function delete(): self {
        builder::get_db()->transaction(function () {
            $delete_event = activity_deleted::create_from_activity($this->activity);

            // Must be deleted first due to foreign key constraints.
            $this->delete_section_relationships();
            $this->delete_user_assignments(); // Must be deleted first due to foreign key constraints.
            $this->delete_manual_relationship_selections();

            // Delete any elements that are directly owned by this activity (through shared context).
            $this->delete_own_elements();

            // Cascading delete will also delete rows fom the following tables:
            // - perform
            // - perform_track
            // - perform_subject_instance
            // - perform_section
            // - perform_section_element
            activity_entity::repository()->where('id', $this->activity->get_id())->delete();

            $delete_event->trigger();
        });

        return $this;
    }

    /**
     * Delete a list of user assignments based on track ids.
     */
    protected function delete_user_assignments(): void {
        builder::get_db()->delete_records_select(
            track_user_assignment::TABLE,
            "track_id IN (
               SELECT id FROM {perform_track} WHERE activity_id = :activity_id
            )",
            ['activity_id' => $this->activity->id]
        );
    }

    /**
     * Delete elements that share the same context as this element.
     */
    protected function delete_own_elements(): void {
        builder::create()
            ->from(element_entity::TABLE, 'element')
            ->where('context_id', $this->activity->get_context()->id)
            ->delete();
    }

    /**
     * Delete a list of section relationship records.
     */
    protected function delete_section_relationships(): void {
        builder::get_db()->delete_records_select(
            section_relationship_entity::TABLE,
            "section_id IN (
                SELECT id FROM {perform_section} WHERE activity_id = :activity_id
            )",
            ['activity_id' => $this->activity->id]
        );
    }

    /**
     * Delete the manual relationship selection records associated with the activity.
     */
    protected function delete_manual_relationship_selections(): void {
        manual_relationship_selection::repository()
            ->where('activity_id', $this->activity->id)
            ->delete();
    }

}
