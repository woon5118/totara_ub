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
 * @author Chris Snyder <chris.snyder@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\notification;

use coding_exception;
use core\entities\user;
use mod_perform\controllers\activity\user_activities_select_participants;
use mod_perform\controllers\activity\view_user_activity;
use mod_perform\models\activity\participant_instance;
use mod_perform\models\activity\subject_instance;
use totara_core\relationship\relationship as relationship_model;
use html_writer;

/**
 * The placeholder class, to provide a well-defined, public set of placeholders for notifications.
 */
class placeholder {
    /** @var string */
    public $recipient_fullname;

    /** @var string */
    public $activity_name;

    /** @var string */
    public $activity_type;

    /** @var string */
    public $subject_fullname;

    /** @var string */
    public $participant_fullname;

    /** @var string */
    public $participant_relationship;

    /** @var string */
    public $instance_duedate;

    /** @var string */
    public $conditional_duedate;

    /** @var string */
    public $instance_created_at;

    /** @var int */
    public $instance_days_active;

    /** @var int */
    public $instance_days_remaining;

    /** @var int */
    public $instance_days_overdue;

    /** @var string */
    public $activity_url;

    /** @var string HTML link */
    public $activity_link;

    /** @var string */
    public $participant_selection_url;

    /** @var string HTML link */
    public $participant_selection_link;

    /**
     * Factory method to initialise placeholder values from an array of [key => value].
     * **Do not use this function in production code!!**
     *
     * @param string[] $values
     * @return placeholder
     * @throws coding_exception thrown when a placeholder name is invalid
     * @internal
     */
    public static function from_data(array $values): placeholder {
        $new = new placeholder();
        foreach ($values as $key => $value) {
            if (!property_exists($new, $key)) {
                throw new coding_exception("{$key} is not a valid placeholder name");
            }
            $new->{$key} = $value;
        }
        return $new;
    }

    /**
     * Factory method to initialise placeholder values from a participant instance.
     *
     * @param participant_instance $participant_instance
     * @return placeholder
     */
    public static function from_participant_instance(participant_instance $participant_instance): placeholder {
        $time = factory::create_clock()->get_time();
        $participant = $participant_instance->get_participant();
        $subject_instance = $participant_instance->get_subject_instance();
        $activity = $subject_instance->get_activity();

        $new = new placeholder();
        $new->recipient_fullname = $participant->fullname;
        $new->activity_name = $activity->name;
        $new->activity_type = $activity->get_type()->get_display_name();
        $new->subject_fullname = $subject_instance->subject_user->fullname;
        $new->participant_fullname = $participant->fullname;
        $new->participant_relationship = $participant_instance->get_core_relationship()->get_name();
        $new->instance_duedate = $subject_instance->due_date ?? 0;
        $new->instance_created_at = $subject_instance->created_at;
        $new->instance_days_active = $new->format_duration($new->instance_created_at, $time);
        if ($new->instance_duedate) {
            $due_delta = $new->format_duration($time, $new->instance_duedate);
            if ($time >= $new->instance_duedate) {
                // Due date is here or has passed.
                $new->instance_days_remaining = 0;
                $new->instance_days_overdue = $due_delta;
            } else {
                // Not yet overdue
                $new->instance_days_remaining = $due_delta;
                $new->instance_days_overdue = 0;
            }
            // Format the due date
            $strftimedate = get_string('strftimedate');
            $new->instance_duedate = userdate($new->instance_duedate, $strftimedate);
            $a = new \stdClass();
            $a->duedate = $new->instance_duedate;
            $new->conditional_duedate = get_string('conditional_duedate_participant_placeholder', 'mod_perform', $a);
        } else {
            $new->conditional_duedate = '';
        }
        $new->activity_url = $participant_instance->get_participation_url();
        $new->activity_link = html_writer::link($new->activity_url, $new->activity_name);
        $new->participant_selection_url = user_activities_select_participants::get_url();
        $new->participant_selection_link = html_writer::link(
            $new->participant_selection_url,
            get_string('user_activities_select_participants_page_title', 'mod_perform')
        );
        return $new;
    }

    /**
     * Factory method to initialise placeholder values from a participant instance.
     *
     * @param subject_instance $subject_instance
     * @return placeholder
     */
    public static function from_subject_instance(subject_instance $subject_instance): placeholder {
        $time = factory::create_clock()->get_time();
        $subject = $subject_instance->get_subject_user();
        $activity = $subject_instance->get_activity();

        $new = new placeholder();
        $new->recipient_fullname = $subject->fullname;
        $new->activity_name = $activity->name;
        $new->activity_type = $activity->get_type()->get_display_name();
        $new->subject_fullname = $subject->fullname;
        $new->participant_fullname = $subject->fullname;
        $new->participant_relationship = 'subject';
        $new->instance_duedate = $subject_instance->due_date ?? 0;
        $new->instance_created_at = $subject_instance->created_at;
        $new->instance_days_active = $new->format_duration($new->instance_created_at, $time);
        if ($new->instance_duedate) {
            $due_delta = $new->format_duration($time, $new->instance_duedate);
            if ($time >= $new->instance_duedate) {
                // Due date is here or has passed.
                $new->instance_days_remaining = 0;
                $new->instance_days_overdue = $due_delta;
            } else {
                // Not yet overdue
                $new->instance_days_remaining = $due_delta;
                $new->instance_days_overdue = 0;
            }
            // Format the due date
            $strftimedate = get_string('strftimedate');
            $new->instance_duedate = userdate($new->instance_duedate, $strftimedate);
            $a = new \stdClass();
            $a->duedate = $new->instance_duedate;
            $new->conditional_duedate = get_string('conditional_duedate_subject_placeholder', 'mod_perform', $a);
        } else {
            $new->conditional_duedate = '';
        }
        $new->activity_url = view_user_activity::get_url();
        $new->activity_link = html_writer::link($new->activity_url, $new->activity_name);
        $new->participant_selection_url = user_activities_select_participants::get_url();
        $new->participant_selection_link = html_writer::link(
            $new->participant_selection_url,
            get_string('user_activities_select_participants_page_title', 'mod_perform')
        );
        return $new;
    }

    /**
     * Format the duration time in a friendly way rather than just subtraction.
     * If the duration is longer than $cutoff i.e. working time, then the duration is round up to the nearest days.
     *
     * @param integer       $timestart      Start time in Unix timestamp
     * @param integer       $timefinish     Finish time in Unix timestamp
     * @param integer       $cutoff         Cut off time in seconds, 8 hours by default
     * @return integer      Duration in days.
     */
    public static function format_duration(int $timestart, int $timefinish, int $cutoff = HOURSECS * 8): int {
        $duration = (int)abs($timefinish - $timestart);    // Call abs() like format_time
        // Subtract full days from duration, we'll add them back later.
        $days = floor($duration / DAYSECS);
        $duration = $duration - ($days * DAYSECS);
        // If duration is greater that cutoff, round up to a full day.
        if ($duration >= $cutoff) {
            $duration = DAYSECS;
        }
        // Add full days back in to duration.
        $duration = $duration + ($days * DAYSECS);
        return ($duration / DAYSECS);
    }

    /**
     * Sets the participant/recipient properties appropriately.
     *
     * @param user $participant
     * @param relationship_model $relationship
     */
    public function set_participant(user $participant, relationship_model $relationship) {
        $this->recipient_fullname = $participant->fullname;
        $this->participant_fullname = $participant->fullname;
        $this->participant_relationship = $relationship->get_name();
    }

}
