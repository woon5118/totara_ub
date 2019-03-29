<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author  Valerii Kuznetsov <valerii.kuznetsov@totaralearning.com>
 * @package mod_facetoface
 */
namespace mod_facetoface;

use \mod_facetoface\signup\state\waitlisted;
use \mod_facetoface\signup\state\booked;
use \stdClass;
use mod_facetoface\signup_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/facetoface/notification/lib.php');


/**
 * Class notice_sender is just a wrapper that sends typical signup related notices and notifications to users.
 */
class notice_sender {
    /**
     * Send manager request notices
     *
     * @param signup $signup
     * @return string
     */
    public static function request_manager(signup $signup) {
        $managers = signup_helper::find_managers_from_signup($signup);

        $hasemail = false;
        foreach ($managers as $manager) {
            if (!empty($manager->email)) {
                $hasemail = true;
                break;
            }
        }

        if ($hasemail) {
            $params = [
                'type'          => MDL_F2F_NOTIFICATION_AUTO,
                'conditiontype' => MDL_F2F_CONDITION_BOOKING_REQUEST_MANAGER
            ];
            return static::send($signup, $params);
        }
        return 'error:nomanagersemailset';
    }

    /**
     * Send booking request notice to user and all users with the specified sessionrole
     *
     * @param signup $signup
     * @return string
     */
    public static function request_role(signup $signup) : string {
        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_BOOKING_REQUEST_ROLE
        ];

        return static::send($signup, $params);
    }

    /**
     * Send booking request notice to user, manager, all session admins.
     *
     * @param signup $signup
     * @return string
     */
    public static function request_admin(signup $signup) : string {
        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_BOOKING_REQUEST_ADMIN
        ];

        return static::send($signup, $params);
    }

    /**
     * Send a booking confirmation email to the user and manager
     *
     * @param signup $signup Signup
     * @param int $notificationtype Type of notifications to be sent @see {{MDL_F2F_INVITE}}
     * @param stdClass $fromuser User object describing who the email is from.
     * @return string Error message (or empty string if successful)
     */
    public static function confirm_booking(signup $signup, int $notificationtype, stdClass $fromuser = null) : string {
        global $DB;

        $params = [
            'type' => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_BOOKING_CONFIRMATION
        ];

        $fromuser = $signup->get_fromuser();
        if (empty($fromuser) && !empty($signup->get_managerid())) {
            $fromuser = $DB->get_record('user', ['id' => $signup->get_managerid()]);
        }

        return static::send($signup, $params, $notificationtype, MDL_F2F_INVITE, $fromuser);
    }

    /**
     * Send a waitlist confirmation email to the user and manager
     *
     * @param signup $signup Signup
     * @param int $notificationtype Type of notifications to be sent @see {{MDL_F2F_INVITE}}
     * @param stdClass $fromuser User object describing who the email is from.
     * @return string Error message (or empty string if successful)
     */
    public static function confirm_waitlist(signup $signup, int $notificationtype, stdClass $fromuser = null) : string {
        global $DB;

        $params = [
            'type' => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_WAITLISTED_CONFIRMATION
        ];

        $fromuser = $signup->get_fromuser();
        if (empty($fromuser) && !empty($signup->get_managerid())) {
            $fromuser = $DB->get_record('user', ['id' => $signup->get_managerid()]);
        }

        return static::send($signup, $params, $notificationtype, MDL_F2F_INVITE, $fromuser);
    }


    /**
     * Send a confirmation email to the user and manager regarding the
     * cancellation
     *
     * @param signup $signup Signup
     * @return string Error message (or empty string if successful)
     */
    public static function decline(signup $signup) {
        global $CFG;

        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_DECLINE_CONFIRMATION
        ];

        $includeical = empty($CFG->facetoface_disableicalcancel);
        return static::send($signup, $params, $includeical ? MDL_F2F_BOTH : MDL_F2F_TEXT, MDL_F2F_CANCEL);
    }

    /**
     * Send a email to the not signed up attendees (e.g. roles)
     *
     * @param integer $recipientid ID of the recipient of the email
     * @param seminar_event $seminarevent
     * @param array $olddates array of previous dates
     * @return string Error message (or empty string if successful)
     */
    public static function event_datetime_changed(int $recipientid, seminar_event $seminarevent, array $olddates) : string {
        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_SESSION_DATETIME_CHANGE
        ];

        return static::send_event($recipientid, $seminarevent, $params, MDL_F2F_BOTH, MDL_F2F_INVITE, null, $olddates);
    }

    /**
     * Send a email to the user and manager regarding the
     * session date/time change
     *
     * @param signup $signup
     * @param array $olddates
     * @return string Error message or empty string if success
     */
    public static function signup_datetime_changed(signup $signup, array $olddates) : string {
        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_SESSION_DATETIME_CHANGE
        ];

        $invite = ($signup->get_state() instanceof waitlisted) ? MDL_F2F_TEXT : MDL_F2F_BOTH;
        return static::send($signup, $params, $invite, MDL_F2F_INVITE, null, $olddates);
    }

    /**
     * Send a message to a user who has just had their waitlisted signup cancelled due to the event starting
     * and the automatic waitlist cleaner cancelling all waitlisted records.
     *
     * @param signup $signup
     * @return string
     */
    public static function signup_waitlist_autoclean(signup $signup) : string {
        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_WAITLIST_AUTOCLEAN
        ];

        return static::send($signup, $params);
    }

    /**
     * Send a confirmation email to the trainer
     *
     * @param integer $recipientid ID of the recipient of the email
     * @param seminar_event $seminarevent
     * @return string Error message (or empty string if successful)
     */
    public static function trainer_confirmation(int $recipientid, seminar_event $seminarevent) {
        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_TRAINER_CONFIRMATION
        ];

        return static::send_event($recipientid, $seminarevent, $params, MDL_F2F_BOTH, MDL_F2F_INVITE);
    }

    /**
     * Send a cancellation email to the trainer
     *
     * @param integer $recipientid ID of the recipient of the email
     * @param seminar_event $seminarevent
     * @return string Error message (or empty string if successful)
     */
    public static function event_trainer_cancellation(int $recipientid, seminar_event $seminarevent) {
        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_TRAINER_SESSION_CANCELLATION
        ];
        return static::send_event($recipientid, $seminarevent, $params, MDL_F2F_BOTH, MDL_F2F_CANCEL);
    }

    /**
     * Send a unassignment email to the trainer
     *
     * @param integer $recipientid ID of the recipient of the email
     * @param seminar_event $seminarevent
     * @return string Error message (or empty string if successful)
     */
    public static function event_trainer_unassigned(int $recipientid, seminar_event $seminarevent) {
        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_TRAINER_SESSION_UNASSIGNMENT
        ];

        return static::send_event($recipientid, $seminarevent, $params, MDL_F2F_BOTH, MDL_F2F_CANCEL);
    }

    /**
     * Send a confirmation email to the user and manager regarding the
     * signup cancellation
     *
     * @param signup $signup Signup
     * @param bool $attachical Should cancellation ical be attached
     * @return string Error message (or empty string if successful)
     */
    public static function signup_cancellation(signup $signup, $attachical = true) {
        global $CFG;

        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_CANCELLATION_CONFIRMATION
        ];

        $icalattachmenttype = (empty($CFG->facetoface_disableicalcancel) && $attachical) ? MDL_F2F_BOTH : MDL_F2F_TEXT;
        return static::send($signup, $params, $icalattachmenttype, MDL_F2F_CANCEL);
    }

    /**
     * Send a confirmation email to the recepient regarding seminar event cancellation
     *
     * @param integer $recipientid ID of the recipient of the email
     * @param seminar_event $seminarevent
     * @param bool $attachical Should cancellation ical be attached
     * @return string Error message (or empty string if successful)
     */
    public static function event_cancellation(int $recipientid, seminar_event $seminarevent, bool $attachical = true) {
        global $CFG;

        $params = [
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_SESSION_CANCELLATION
        ];

        $icalattachmenttype = (empty($CFG->facetoface_disableicalcancel) && $attachical) ? MDL_F2F_BOTH : MDL_F2F_TEXT;
        return static::send_event($recipientid, $seminarevent, $params, $icalattachmenttype, MDL_F2F_CANCEL);
    }

    /**
     * Notify managers that a session they had reserved spaces on has been deleted.
     *
     * @param seminar_event $seminarevent
     */
    public static function reservation_cancelled(seminar_event $seminarevent) {
        global $CFG, $DB;

        $helper = new attendees_helper($seminarevent);
        $attendees = $helper->get_reservations();

        $reservedids = array();
        foreach ($attendees as $attendee) {
            if ($attendee->has_bookedby() && !$attendee->is_valid()) {
                // If the attendee is booked by a manager, and the reservation is not a valid record, because it has
                // no user to fill up this reservation yet. Then we add the manager id here.
                // Managers can already get booking cancellation notices - just adding reserve cancellation notices.
                $reservedids[] = $attendee->get_bookedby();
            }
        }
        if (!$reservedids) {
            return;
        }
        $reservedids = array_unique($reservedids);

        $facetoface = $DB->get_record('facetoface', ['id' => $seminarevent->get_facetoface()]);
        $facetoface->ccmanager = false; // Never Cc the manager's manager (that would just be too much).

        // Notify all managers that have reserved spaces for their team.
        $params = array(
            'facetofaceid'  => $facetoface->id,
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_RESERVATION_CANCELLED
        );

        $includeical = empty($CFG->facetoface_disableicalcancel);
        foreach ($reservedids as $reservedid) {
            static::send_notice($seminarevent, $reservedid, $params, $includeical ? MDL_F2F_BOTH : MDL_F2F_TEXT, MDL_F2F_CANCEL);
        }
    }

    /**
     * Send a notice (all session dates in one message).
     *
     * @param seminar_event $seminarevent
     * @param integer $userid ID of the recipient of the email
     * @param array $params The parameters for the notification
     * @param int $icalattachmenttype The ical attachment type, or MDL_F2F_TEXT to disable ical attachments
     * @param int $icalattachmentmethod The ical method type: MDL_F2F_INVITE or MDL_F2F_CANCEL
     * @param object $fromuser User object describing who the email is from.
     * @param array $olddates array of previous dates
     * @param bool $notifyuser Send user notification
     * @param bool $notifymanager Send manager notification
     * @return string Error message (or empty string if successful)
     */
    public static function send_notice(seminar_event $seminarevent, $userid, $params, $icalattachmenttype = MDL_F2F_TEXT,
                                       $icalattachmentmethod = MDL_F2F_INVITE, $fromuser = null, array $olddates = array(),
                                       $notifyuser = true, $notifymanager = true) {
        global $DB;

        $notificationdisable = get_config(null, 'facetoface_notificationdisable');
        if (!empty($notificationdisable)) {
            return false;
        }

        $user = $DB->get_record('user', ['id' => $userid]);
        if (!$user) {
            return 'userdoesnotexist';
        }

        // Make it not fail if more then one notification found. Just use one.
        // Other option is to change data_object, but so far it's facetoface issue that we hope to fix soon and remove workaround
        // code from here.
        $checkrows = $DB->get_records('facetoface_notification', $params);
        if (count($checkrows) > 1) {
            $params['id'] = reset($checkrows)->id;
            debugging("Duplicate notifications found for (excluding id): " . json_encode($params), DEBUG_DEVELOPER);
        }

        // By definition, the send one email per day feature works on sessions with
        // dates. However, the current system allows sessions to be created without
        // dates and it allows people to sign up to those sessions. In this cases,
        // the sign ups still need to get email notifications; hence the checking of
        // the existence of dates before allowing the send one email per day part.
        // Note, that's not always the case, if all dates have been deleted from a
        // seminar event we still need to send the emails to cancel the dates,
        // thus need to check whether old dates have been supplied.
        if (get_config(null, 'facetoface_oneemailperday')
            && ($seminarevent->is_sessions() || !empty($olddates))) {
            return static::send_notice_oneperday($seminarevent, $userid, $params, $icalattachmenttype, $icalattachmentmethod,
                $olddates, $notifyuser, $notifymanager);
        }

        $seminareventid = $seminarevent->get_id();

        $notice = new \facetoface_notification($params);
        $notice->facetofaceid = $seminarevent->get_facetoface();
        $notice->set_newevent($user, $seminareventid, null, $fromuser);

        if ($notifyuser) {
            $icaldata = [];
            if ((int)$icalattachmenttype == MDL_F2F_BOTH && $notice->conditiontype != MDL_F2F_CONDITION_DECLINE_CONFIRMATION) {
                // add_ical_attachment needs session dates on the session stdClass object
                $session = $seminarevent->to_record();
                $session->sessiondates = $seminarevent->get_sessions()->sort('timestart')->to_records(false);
                $notice->add_ical_attachment($user, $session, $icalattachmentmethod, null, $olddates);
                $icaldata = [
                    'dates' => $session->sessiondates,
                    'olddates' => $olddates,
                    'method' => $icalattachmentmethod
                ];
            }
            $notice->send_to_user($user, $seminareventid, null, $icaldata);
        }
        if ($notifymanager) {
            $notice->send_to_manager($user, $seminareventid);
        }
        $notice->send_to_thirdparty($user, $seminareventid);
        $notice->send_to_roleapprovers_adhoc($user, $seminareventid);
        $notice->send_to_adminapprovers_adhoc($user, $seminareventid);
        $notice->delete_ical_attachment();

        return '';
    }

    /**
     * Send registration closure notice to user, manager, all session admins.
     *
     * @param object $facetoface    Facetoface instance
     * @param object $session       Session instance
     * @param int    $recipientid   The id of the user requesting a booking
     */
    public static function registration_closure(seminar_event $seminarevent, $recipientid) {
        global $DB, $USER;

        $notificationdisable = get_config(null, 'facetoface_notificationdisable');
        if (!empty($notificationdisable)) {
            return false;
        }

        $recipient = $DB->get_record('user', ['id' => $recipientid]);
        if (!$recipient) {
            return 'userdoesnotexist';
        }

        $params = array(
            'facetofaceid'  => $seminarevent->get_facetoface(),
            'type'          => MDL_F2F_NOTIFICATION_AUTO,
            'conditiontype' => MDL_F2F_CONDITION_BEFORE_REGISTRATION_ENDS
        );

        $seminareventid = $seminarevent->get_id();
        $notice = new \facetoface_notification($params);
        $notice->facetofaceid = $seminarevent->get_facetoface();
        $notice->set_newevent($recipient, $seminareventid, null, $USER);
        $notice->send_to_user($recipient, $seminareventid);
        $notice->send_to_manager($recipient, $seminareventid);

        return '';
    }

    /**
     * Send message to signed up attendee
     * @param signup $signup
     * @param array $params
     * @param int $icalattachmenttype
     * @param int $icalattachmentmethod
     * @param stdClass $fromuser
     * @param array $olddates
     * @return string
     */
    protected static function send(signup $signup, array $params, int $icalattachmenttype = MDL_F2F_TEXT, int $icalattachmentmethod = MDL_F2F_INVITE, stdClass $fromuser = null, array $olddates = []) : string {
        $recipientid = $signup->get_userid();
        $seminarevent = $signup->get_seminar_event();
        $params['facetofaceid']  = $seminarevent->get_facetoface();

        $notifyuser = true;
        $notifymanager = true;
        if ($signup->get_skipusernotification()) {
            $notifyuser = false;
        }

        if ($signup->get_skipmanagernotification()) {
            $notifymanager = false;
        }

        if ($notifymanager || $notifyuser || $seminarevent->is_started()) {
            return static::send_notice($seminarevent, $recipientid, $params, $icalattachmenttype, $icalattachmentmethod,
                $fromuser, $olddates, $notifyuser, $notifymanager);
        }

        return '';
    }

    /**
     * Send message to not signed up event attendee (e.g. role)
     * @param int $recipientid
     * @param seminar_event $seminarevent
     * @param array $params
     * @param int $icalattachmenttype
     * @param int $icalattachmentmethod
     * @param stdClass $fromuser
     * @param array $olddates
     * @return string
     */
    protected static function send_event(int $recipientid, seminar_event $seminarevent, array $params, int $icalattachmenttype = MDL_F2F_TEXT,
                                         int $icalattachmentmethod = MDL_F2F_INVITE, stdClass $fromuser = null, array $olddates = []) : string {
        global $DB;
        $params['facetofaceid']  = $seminarevent->get_facetoface();

        return static::send_notice($seminarevent, $recipientid, $params, $icalattachmenttype, $icalattachmentmethod, $fromuser, $olddates);
    }

    /**
     * Send a notice (one message per session date).
     *
     * @param seminar_event $seminarevent
     * @param integer $userid ID of the recipient of the email
     * @param array $params The parameters for the notification
     * @param int $icalattachmenttype The ical attachment type, or MDL_F2F_TEXT to disable ical attachments
     * @param int $icalattachmentmethod The ical method type: MDL_F2F_INVITE or MDL_F2F_CANCEL
     * @param array $olddates array of previous dates
     * @param bool $notifyuser Send user notification
     * @param bool $notifymanager Send manager notification
     * @return string Error message (or empty string if successful)
     */
    private static function send_notice_oneperday(seminar_event $seminarevent, $userid, $params, $icalattachmenttype = MDL_F2F_TEXT,
                                                  $icalattachmentmethod = MDL_F2F_INVITE, array $olddates = [],
                                                  $notifyuser = true, $notifymanager = true) {
        global $DB, $CFG;

        $notificationdisable = get_config(null, 'facetoface_notificationdisable');
        if (!empty($notificationdisable)) {
            return false;
        }

        $user = $DB->get_record('user', ['id' => $userid]);
        if (!$user) {
            return 'userdoesnotexist';
        }

        // Get sessions and convert to an array of stdClass objects
        // to fit in with the rest of the code down the line.
        $eventsessions = $seminarevent->get_sessions();
        $session = $seminarevent->to_record();
        $session->sessiondates = [];
        foreach ($eventsessions as $eventsession) {
            array_push($session->sessiondates, (object) [
                'id' => $eventsession->get_id(),
                'sessionid' => $eventsession->get_sessionid(),
                'sessiontimezone' => $eventsession->get_sessiontimezone(),
                'timestart' => $eventsession->get_timestart(),
                'timefinish' => $eventsession->get_timefinish(),
                'roomid' => $eventsession->get_roomid()
            ]);
        }

        // Filtering dates.
        // "Key by" date id.
        $get_id = function ($item) {
            return $item->id;
        };
        $olds = array_combine(array_map($get_id, $olddates), $olddates);

        $dates = array_filter($session->sessiondates, function ($date) use (&$olds) {
            if (isset($olds[$date->id])) {
                $old = $olds[$date->id];
                unset($olds[$date->id]);
                if ($old->sessiontimezone == $date->sessiontimezone &&
                    $old->timestart == $date->timestart &&
                    $old->timefinish == $date->timefinish &&
                    $old->roomid == $date->roomid) {
                    return false;
                }
            }

            return true;
        });

        $send = function ($dates, $cancel = false, $notifyuser = true, $notifymanager = true) use ($seminarevent, $session,
            $icalattachmenttype, $icalattachmentmethod, $user, $params, $DB, $CFG
        ) {
            $seminareventid = $seminarevent->get_id();
            foreach ($dates as $date) {
                if ($cancel) {
                    $params['conditiontype'] = MDL_F2F_CONDITION_CANCELLATION_CONFIRMATION;
                }
                $sendical =  (int)$icalattachmenttype == MDL_F2F_BOTH &&
                    (!$cancel || ($cancel && empty($CFG->facetoface_disableicalcancel)));

                $notice = new \facetoface_notification($params);
                $notice->facetofaceid = $seminarevent->get_facetoface();
                // Send original notice for this date.
                $notice->set_newevent($user, $seminareventid, $date);
                if ($sendical) {
                    $notice->add_ical_attachment($user, $session, $icalattachmentmethod, !$cancel ? $date : [], $cancel ? $date : []);
                }
                if ($notifyuser) {
                    $notice->send_to_user($user, $seminareventid, $date);
                }
                if ($notifymanager) {
                    $notice->send_to_manager($user, $seminareventid);
                }

                $notice->send_to_thirdparty($user, $seminareventid);
                $notice->send_to_roleapprovers_adhoc($user, $seminareventid);
                $notice->send_to_adminapprovers_adhoc($user, $seminareventid);

                $notice->delete_ical_attachment();
            }
        };

        $send($dates, false, $notifyuser, $notifymanager);
        $send($olds, true, $notifyuser, $notifymanager);

        return '';
    }
}