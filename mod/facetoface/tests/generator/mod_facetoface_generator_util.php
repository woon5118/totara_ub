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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package mod_facetoface
 */

use mod_facetoface\{signup, seminar_event, seminar};
use mod_facetoface\signup\state\{
    booked,
    waitlisted,
    requested,
    requestedadmin,
    requestedrole,
    fully_attended,
    partially_attended,
    unable_to_attend,
    no_show,
    state,
    not_set
};

defined('MOODLE_INTERNAL') || die();

final class mod_facetoface_generator_util {
    /**
     * Map for status in behat step with the one in code
     * @var array
     */
    private static $map = [
        'booked' => booked::class,
        'waitlisted' => waitlisted::class,
        'requested' => requested::class,
        'requestedadmin' => requestedadmin::class,
        'requestedrole' => requestedrole::class,
        'fully_attended' => fully_attended::class,
        'partially_attended' => partially_attended::class,
        'unable_to_attend' => unable_to_attend::class,
        'no_show' => no_show::class
    ];

    private static function get_event_id_from_detail(string $details): int {
        global $DB;
        $sql = "SELECT id FROM {facetoface_sessions} WHERE {$DB->sql_compare_text('details')} = '{$details}'";

        // This seems to be a bad idea, but presumably that the test environment does only have one record
        // per test suite, then it is okay to do so. Just pass MUST_EXIST here, so that it can fail the test straight
        // away when record is not found.
        return (int) $DB->get_record_sql($sql, null, MUST_EXIST)->id;
    }

    /**
     * For start/finish time, we use the format that php is supporting. Therefore, please provided it if
     * @see https://www.php.net/manual/en/datetime.formats.relative.php
     * @param array $record
     * @return int
     */
    public static function create_sessiondates_for_behat(array $record): int {
        global $DB;

        $keys = ['eventdetails', 'start', 'finish'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $record)) {
                throw new coding_exception("The property '{$key}' is missing");
            }
        }

        // This seems to be a bad idea, but presumably that the test environment does only have one record
        // per test suite, then it is okay to do so. Just pass MUST_EXIST here, so that it can fail the test straight
        // away when record is not found
        $eventid = static::get_event_id_from_detail($record['eventdetails']);

        $times = [
            'start' => [
                'original' => $record['start'],
                'cumputed' => 0
            ],
            'finish' => [
                'original' => $record['finish'],
                'computed' => 0
            ]
        ];

        foreach ($times as $key => $time) {
            $original = $time['original'];
            $computed = 0;

            if (!is_numeric($original)) {
                // This could mean that it is a format instead of normal returned value from `time()`.
                try {
                    // Using DateTime object, because it can throw exception, and so we could catch and fail the
                    // behat step pretty much.
                    $datetimeobject = new DateTime($original);
                    $computed = $datetimeobject->getTimestamp();
                } catch (\Throwable $e) {
                    throw new coding_exception("An exception occured: {$e->getMessage()}'");
                }
            } else {
                $computed = (int) $original;
            }

            $times[$key]['computed'] = $computed;
        }

        $rc = new stdClass();
        $rc->sessionid = $eventid;
        $rc->timestart = $times['start']['computed'];
        $rc->timefinish = $times['finish']['computed'];
        $rc->sessiontimezone = isset($record['sessiontimezone']) ? $record['sessiontimezone'] : 99;

        if (isset($record['room'])) {
            $rc->roomid = $DB->get_field('facetoface_room', 'id', ['name' => $record['room']], MUST_EXIST);
        }

        $rc->id = $DB->insert_record('facetoface_sessions_dates', $rc);

        if (isset($record['assets'])) {
            // Start processing on assets if there are any provided.
            $assets = explode(",", $record['assets']);
            array_walk(
                $assets,
                function (string &$asset): void {
                    trim($asset);
                }
            );

            foreach ($assets as $asset) {
                $o = new \stdClass();
                $o->sessiondateid = $rc->id;

                // Expecting asset to be existing in the storage, with the given name from the step.
                $o->assetid = $DB->get_field('facetoface_asset', 'id', ['name' => $asset], MUST_EXIST);
                $DB->insert_record('facetoface_asset_dates', $o);
            }
        }

        return $rc->id;
    }

    /**
     * Just returning an id of seminar event to the usage. Creating seminar event for an instance.
     *
     * @param array $record
     * @return int
     */
    public static function create_session_for_behat(array $record): int {
        global $DB;

        if (!isset($record['facetoface'])) {
            throw new coding_exception("No property 'facetoface' defined in \$record");
        }

        $f2f = $record['facetoface'];
        $seminarevent = new seminar_event();
        $seminarevent->set_details($record['details']);

        if (!is_numeric($f2f)) {
            // Must be idnumber or name, if it is not an id/integer.
            $f2fid = $DB->get_field('facetoface', 'id', ['name' => $f2f]);
            if (!$f2fid) {
                // If facetoface id is not being found via name, then we should try the idnumber instead, allow the
                // developer to have more than one way to create the seminar within generator.
                $f2fid = $DB->get_field('course_modules', 'instance', ['idnumber' => $f2f]);
                if (!$f2fid) {
                    throw new coding_exception(
                        "The property 'facetoface' must be an idnumber that is associated with seminar"
                    );
                }

            }

            $seminarevent->set_facetoface((int) $f2fid);
        } else {
            $seminarevent->set_facetoface((int) $record['facetoface']);
        }

        if (isset($record['capacity'])) {
            $seminarevent->set_capacity((int) $record['capacity']);
        }

        if (isset($record['allowoverbook'])) {
            $seminarevent->set_allowoverbook((int) $record['allowoverbook']);
        }

        if (isset($record['waitlistedeveryone'])) {
            $seminarevent->set_waitlisteveryone((int) $record['waitlistedeveryone']);
        }

        if (isset($record['normalcost'])) {
            $seminarevent->set_normalcost($record['normalcost']);
        }

        if (isset($record['discountcost'])) {
            $seminarevent->set_discountcost($record['discountcost']);
        }

        if (isset($record['allowcancellations'])) {
            $seminarevent->set_allowcancellations((int) $record['allowcancellations']);
        }

        if (isset($record['cancellationcutoff'])) {
            $seminarevent->set_cancellationcutoff($record['cancellationcutoff']);
        }

        if (isset($record['cutoff'])) {
            $seminarevent->set_cutoff($record['cutoff']);
        }

        if (isset($record['approval'])) {
            if (seminar::APPROVAL_SELF == $record['approval']) {
                $seminarevent->set_selfapproval(1);
            }
        }

        if (isset($record['mincapacity'])) {
            $seminarevent->set_mincapacity($record['mincapacity']);
        }

        if (isset($record['sendcapacityemail'])) {
            $seminarevent->set_sendcapacityemail((int) $record['sendcapacityemail']);
        }


        $items = ['registrationtimestart', 'registrationtimefinish'];
        $registrationtime = [];
        foreach ($items as $item) {
            if (isset($record[$item])) {
                $original = $record[$item];
                if (!is_numeric($original)) {
                    // Must be some kind of time format string that the PHP is able to understand.
                    // Otherwise, we should throw exception here to let the test just fail.
                    try {
                        $dt = new DateTime($original);
                        $original = $dt->getTimestamp();
                    } catch (\Throwable $e) {
                        throw new coding_exception("Something went wrong: {$e->getMessage()}");
                    }
                }

                $registrationtime[$item] = $original;
            }
        }

        if (isset($registrationtime['registrationtimestart'])) {
            $seminarevent->set_registrationtimestart($registrationtime['registrationtimestart']);
        }

        if (isset($registrationtime['registrationtimefinish'])) {
            $seminarevent->set_registrationtimefinish($registrationtime['registrationtimefinish']);
        }

        if (isset($record['cancelledstatus'])) {
            $seminarevent->set_cancelledstatus((int) $record['cancelledstatus']);
        }

        $seminarevent->save();
        return $seminarevent->get_id();
    }

    /**
     * Create signup for an event. The property 'user' of $record must be a username of the specific user that
     * we are trying to create a signup for.
     *
     * NOTE: When you are creating a signup within an event, make sure the user had been already enrolled to the
     * course first.
     *
     * @param array $record
     * @return int
     */
    public static function create_signups_for_behat(array $record): int {
        global $DB;

        $keys = array('user', 'eventdetails');
        foreach ($keys as $key) {
            if (!array_key_exists($key, $record)) {
                throw new coding_exception("The property '{$key}' does not exist in \$record");
            }
        }

        $signup = new signup();
        $signup->set_userid($DB->get_field('user', 'id', ['username' => $record['user']], MUST_EXIST));
        $signup->set_sessionid(static::get_event_id_from_detail($record['eventdetails']));

        if (isset($record['discountcode'])) {
            $signup->set_discountcode($record['discountcode']);
        }

        if (isset($record['notificationtype'])) {
            $signup->set_notificationtype((int) $record['notificationtype']);
        } else {
            $signup->set_notificationtype(MDL_F2F_BOTH);
        }

        if (isset($record['bookedby'])) {
            $signup->set_bookedby((int) $record['bookedby']);
        }

        if (isset($record['jobassignmentid'])) {
            $signup->set_jobassignmentid((int) $record['jobassignmentid']);
        }

        if (isset($record['managerid'])) {
            $signup->set_managerid((int) $record['managerid']);
        };

        $signup->save();

        if (isset($record['status'])) {
            $status = $record['status'];
            if (isset(self::$map[$status])) {
                $signup->switch_state(self::$map[$status]);
            }
        } else {
            // Status is not provided, try to switch to many different type of state here.
            $states = state::get_all_states();
            foreach ($states as $state) {
                if ($state == not_set::class) {
                    // Skip not set.
                    continue;
                }

                if ($signup->can_switch($state)) {
                    $signup->switch_state($state);
                    break;
                }
            }
        }

        return $signup->get_id();
    }

}