<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package core_user
 */


namespace core_user;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use stdClass;

/**
 * A class to update the `email_bounce_count` and also `email_send_count` within table user_preference. It will most
 * likely working with the action of editing the user email. Since, user has another option to cancel the request of
 * changing the email.
 * Therefore, the system need to keep track the history of these preferences (temporally). Until the user confirm the
 * request, then these track records will be removed
 *
 * Class user_email_bounce
 * @package core_user
 */
final class email_bounce_counter {
    /**
     * @var stdClass
     */
    private $user;

    /**
     * user_email_preference constructor.
     * @param stdClass $user        The target $user that is being updated
     */
    public function __construct(stdClass $user) {
        $this->user = $user;
        if (!isset($this->user->id)) {
            throw new coding_exception("Missing \$user->id");
        }
    }

    /**
     * The method only create the history of user's preference, only if there is any preference. If there is no such
     * preference found for the user, then the method will not create any history track
     *
     * @param string $name
     * @return void
     */
    private function create_history_preference(string $name): void {
        $currentvalue = get_user_preferences($name, null, $this->user->id);
        // Using is_null($currentvalue) here, as !0 could evaluated as true and sometimes 0 is an actual intended value
        // of the preference $name
        if (is_null($currentvalue)) {
            // There is no such preference, therefore, we should not create a history of the preference
            return;
        }

        set_user_preference("old_{$name}", $currentvalue, $this->user->id);
    }

    /**
     * Only restoring this history track of preference, if both preference and history track preference exists concurrently.
     * However, if either preference or history track is missing, then the restore process would not restore anything.
     *
     * @param string $name
     */
    private function restore_preference(string $name): void {
        $currentvalue = get_user_preferences($name, null, $this->user->id);
        // Using is_null($currentvalue) here, as !0 could evaluated as true and sometimes 0 is an actual intended value
        // of the preference $name
        if (is_null($currentvalue)) {
            // If there is no preference record found, then there is no point to proceed
            debugging("There was no user's preference ($name)", DEBUG_DEVELOPER);
            return;
        }

        $oldvalue = get_user_preferences("old_{$name}", null, $this->user->id);
        // Using is_null($oldvalue) here, as !0 could evaluated as true and sometimes 0 is an actual intended value
        // of the preference $name
        if (is_null($oldvalue)) {
            // If there is no old history preference, then there is no point to proceed
            debugging("There was no user's old preference (old_{$name})", DEBUG_DEVELOPER);
            return;
        }

        if ($currentvalue != $oldvalue) {
            // Only updating when the current value is different than then old value. When it is different, restoring
            // the old value to the preference
            set_user_preference($name, $oldvalue, $this->user->id);
        }

        // After restore the preferences, it is quite important to clean up the old preference that is being used
        // to track the history of current preference
        $this->delete_old_preference($name);
    }

    /**
     * Deleting the history track of preference
     *
     * @param string $name
     * @return void
     */
    private function delete_old_preference(string $name): void {
        unset_user_preference("old_{$name}", $this->user->id);
    }

    /**
     * A method to update the user's bounce/send count preference. As when user requests to change the email, these
     * preferences need to be resetted so that an email is able to send out to user.
     *
     * @param bool      $track      Flag this field to record the history of user's email bounce/send count preference.
     * @param bool      $reset      Flag this field to reset the email bounce/send count preference of a user
     * @return void
     * @see useredit_update_bounces
     */
    public function update_bounces(bool $track = true, bool $reset = true): void {
        // Before updating the mail bounce and mail send, at this point, the system need to record the history of
        // these values first, before it make any changes to the current preferences. Only create a history track
        // if the the email actually changed.
        if ($track) {
            $this->create_history_preference("email_bounce_count");
            $this->create_history_preference("email_send_count");
        }

        // Bundle setters here
        $this->set_bounce_count($reset);
        $this->set_send_count($reset);
    }

    /**
     * @param bool $reset
     * @return void
     */
    public function set_bounce_count(bool $reset = false): void {
        $this->set_preference("email_bounce_count", $reset);
    }

    /**
     * @param bool $reset
     * @return void
     */
    public function set_send_count(bool $reset = false): void {
        $this->set_preference("email_send_count", $reset);
    }

    /**
     * @param string    $name       This is only either "email_bounce_count" or "email_send_count"
     * @param bool      $reset
     * @return void
     */
    private function set_preference(string $name, bool $reset = true): void {
        if ($reset) {
            // If it is a reset, make it zero anyway. Don't bother to find one
            $newvalue = 0;
        } else {
            $value = get_user_preferences($name, 0, $this->user->id);
            $newvalue = $value + 1;
        }

        set_user_preference($name, $newvalue, $this->user->id);
    }

    /**
     * A bundle method of restoring the 'email_bounce_count' and 'email_send_count'
     * @return void
     */
    public function restore(): void {
        $this->restore_preference("email_send_count");
        $this->restore_preference("email_bounce_count");
    }
}