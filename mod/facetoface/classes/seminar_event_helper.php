<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package mod_facetoface
 */

namespace mod_facetoface;

defined('MOODLE_INTERNAL') || die();

/**
 * Additional seminar_event functionality.
 */
final class seminar_event_helper {
    /**
     * Merge and purge sessions
     * If dates provided matches any current session then update current session with new dates
     * If dates provided do not match any current sessions then remove unmatched current sessions and insert new dates
     *
     * @param array $dates Dates used for updating/creating sessions
     */
    public static function merge_sessions(seminar_event $seminarevent, array $dates) {
        global $DB;

        // Refresh list of current sessions from the database for merging, then clear it again.  This ensures that
        // all other singleton instances down the line will get the updated list when get_sessions() is called.
        $currentsessions = $seminarevent->get_sessions(true);
        $seminarevent->clear_sessions();

        // Cloning dates to prevent messing with original data. $dates = unserialize(serialize($dates)) will also work.
        $dates = array_map(function ($date) {
            return clone $date;
        }, $dates);

        // Get a list of sessions that should be updated/inserted.
        $dates = self::filter_sessions($dates, $currentsessions);

        // Delete the current sessions that were not filtered out. These sessions did not match any input date provided
        // so we assume that they should be deleted.
        $currentsessions->delete();

        // Update or create sessions with their associated assets.
        foreach ($dates as $date) {
            $assets = isset($date->assetids) ? $date->assetids : [];
            unset($date->assetids);

            if ($date->id > 0) {
                $DB->update_record('facetoface_sessions_dates', $date);
            } else {
                $date->sessionid = $seminarevent->get_id();
                $date->id = $DB->insert_record('facetoface_sessions_dates', $date);
            }

            asset_helper::sync($date->id, array_unique($assets));
        }
    }

    /**
     * Filtering dates: throwing out dates that haven't changed and
     * throwing out old dates which present in the new dates array therefore
     * leaving a list of dates to safely remove from the database.
     * Also it is important to note that we have to unset all the dates
     * from a new dates array with the ID which is not in the old dates array
     * and != 0 (not a new date) to prevent users from messing with the input
     * and other seminar dates since we rely on the date id came from a form.
     *
     * @param array $dates
     * @param seminar_session_list $sessions
     * @return array $dates
     */
    private static function filter_sessions(array $dates, seminar_session_list $sessions) {
        return array_filter($dates, function ($date) use (&$sessions) {
            $date->id = isset($date->id) ? $date->id : 0;
            if ($sessions->contains($date->id)) {
                $session = $sessions->get($date->id);
                $sessions->remove($date->id);
                $room = isset($date->roomid) ? $date->roomid : 0;
                if ($session->get_sessiontimezone() == $date->sessiontimezone
                    && $session->get_timestart() == $date->timestart
                    && $session->get_timefinish() == $date->timefinish
                    && $session->get_roomid() == $room) {
                    $date->assetids = (isset($date->assetids) && is_array($date->assetids)) ? $date->assetids : [];
                    asset_helper::sync($date->id, array_unique($date->assetids));
                    return false;
                }
            } else if ($date->id != 0) {
                return false;
            }
            return true;
        });
    }

    /**
     * Keep this in mind that $seminarevent will mutate itself after deleting. Its own properties will be reset to
     * default values after deleting is complete.
     *
     * This function will try to cancel the event first before start deleting it. When an event is being cancelled, it
     * will try to send emails/notifications out to users/admins that are involving with this seminar event. And unlink
     * the rooms/assets that are being used by session dates. Therefore, it needs to cache the rooms/assets before any
     * kind of this action happens.
     *
     * The steps that this function will be doing:
     * + cache custom rooms
     * + cache custom assets
     * + cancel the seminar event
     * + delete seminar event itself.
     * + deletet hose custom assets that would become orphan
     * + delete those custom rooms that would become orphan
     *
     * @param seminar_event $seminarevent
     * @return bool
     */
    public static function delete_seminarevent(seminar_event $seminarevent): bool {
        global $DB;

        if (!$seminarevent->exists()) {
            return false;
        }

        $id = $seminarevent->get_id();

        // Either before or not able to cancelling the event, it still needs to cache the list of custom rooms and
        // assets, because at the very end of this functionality, these custom rooms/assets should be deleted
        // as well, straight away or via the cron tasks. Either ways, let make sure that these custom rooms/assets not
        // to be left for other seminar events to use.
        $customrooms = room_list::get_custom_rooms_from_seminarevent($id);
        $customassets = asset_list::get_custom_assets_from_seminarevent($id);

        // It does not matter whether the event is able to cancel or not. In the end, records are going to be
        // hard deleted anyway.
        $seminarevent->cancel();
        $seminarevent->delete();

        // These deleting custom rooms/assets functionalities needed to be happened by the very end of the process,
        // because in middle of deleting process, there would have un-expected calls to use these kind of
        // records to send the notifications out to the users/admins.
        //
        // The event was cancelled prior to this point, so most likely the links between custom rooms/assets
        // and this seminar event that we are trying to delete would have been removed at this point. Hence, it is
        // possible for us ot check whether these custom rooms/assets are still being used by different event or not.
        // If some of it is still being used then it should not be deleted, otherwsie it becomes an orphan room/asset
        // and there is a little chance that it can be reused.
        if (!$customassets->is_empty()) {
            foreach ($customassets as $customasset) {
                // Only deleting the custom assets that are NOT being used by different seminar event.
                if ($DB->record_exists('facetoface_asset_dates', ['assetid' => $customasset->get_id()])) {
                    continue;
                }

                $customasset->delete();
            }
        }

        if (!$customrooms->is_empty()) {
            foreach ($customrooms as $customroom) {
                // Only deleting the custom rooms that are NOT being used by different seminar event.
                if ($DB->record_exists('facetoface_sessions_dates', ['roomid' => $customroom->get_id()])) {
                    continue;
                }

                $customroom->delete();
            }
        }

        return true;
    }
}