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

namespace mod_facetoface\notification;

defined('MOODLE_INTERNAL') || die();


use mod_facetoface\seminar_event;
use mod_facetoface\signup\state\waitlisted;
use mod_facetoface\task\send_notifications_task;
use mod_facetoface\facetoface_user;


/**
 * A notification helper class that helps to sending notifications within f2f modules
 *
 * Class notification_helper
 *
 * @package mod_facetoface\notification
 */
class notification_helper {
    /**
     * @var bool
     */
    private $iscli;

    /**
     * @var bool
     */
    private $istest;

    /**
     * A config value for $CFG->facetoface_session_rolesnotify
     * @var string|mixed
     */
    private $roles;

    /**
     * notification_helper constructor.
     */
    public function __construct() {
        global $CFG;

        $this->roles = $CFG->facetoface_session_rolesnotify;
        $this->iscli = defined(CLI_SCRIPT) && CLI_SCRIPT;
        $this->istest = defined(PHPUNIT_TEST) && PHPUNIT_TEST;
    }

    /**
     * Send out email notifications for all sessions that are under capacity at the cut-off of any event.
     *
     * @return void
     */
    public function notify_under_capacity(): void {
        global $DB;

        // If there are no recipients, don't bother to send email !
        if (empty($this->roles)) {
            return;
        }

        $now = time();
        $lastcron = $this->get_notification_task_last_cron();

        $params = [
            'lastcron' => $lastcron,
            'now' => $now
        ];

        $sql = "
            SELECT s.*, dt.minstart
            FROM {facetoface_sessions} s
            INNER JOIN (
              SELECT s.id as sessid, MIN(d.timestart) AS minstart
              FROM {facetoface_sessions} s
              INNER JOIN {facetoface_sessions_dates} d ON s.id = d.sessionid
              GROUP BY s.id
            ) dt ON dt.sessid = s.id
            WHERE s.mincapacity > 0
              AND (dt.minstart - s.cutoff) < :now
              AND (dt.minstart - s.cutoff) >= :lastcron
              AND s.cancelledstatus = 0";


        $records = $DB->get_records_sql($sql, $params);
        foreach ($records as $record) {
            // Clone the object without minstart here, so that crud_mapper would not complain about properties that are not defined
            // in the child class.
            $rc = clone $record;
            unset($rc->minstart);

            $seminarevent = new seminar_event();
            $seminarevent->from_record($rc);

            $booked = facetoface_get_num_attendees($seminarevent->get_id(), waitlisted::get_code());
            if ($booked >= $seminarevent->get_mincapacity()) {
                continue;
            }

            // No cutoff period means the notify bookings email checkbox was not checked.
            if (!$seminarevent->has_cutoff()) {
                continue;
            }

            // We've found a session that has not reached the minimum bookings by the cut-off - time to send out emails.
            $seminar = $seminarevent->get_seminar();
            $cm = $seminar->get_coursemodule();

            $info = new \stdClass();
            $info->name = format_string($seminar->get_name());
            $info->capacity = $seminarevent->get_capacity();
            $info->mincapacity = $seminarevent->get_mincapacity();
            $info->booked = $booked;
            $info->link = (new \moodle_url('/mod/facetoface/view.php', ['id' => $cm->id]))->out(false);

            if (!$record->minstart) {
                $info->starttime = get_string('nostarttime', 'facetoface');
            } else {
                $info->starttime = userdate($record->minstart, get_string('strftimedatetime'));
            }

            $eventdata = new \stdClass();
            $eventdata->userfrom = facetoface_user::get_facetoface_user();
            $eventdata->subject = get_string('sessionundercapacity', 'mod_facetoface', $info->name);
            $eventdata->fullmessage = get_string('sessionundercapacity_body', 'mod_facetoface', $info);
            $eventdata->msgtype = TOTARA_MSG_TYPE_FACE2FACE;
            $eventdata->msgstatus = TOTARA_MSG_STATUS_NOTOK;
            $eventdata->urgency = TOTARA_MSG_URGENCY_NORMAL;
            $eventdata->sendmail = TOTARA_MSG_EMAIL_YES;

            if ($this->iscli && !$this->istest) {
                mtrace(
                    "Facetoface '{$info->name}' in course {$seminar->get_course()} is under minimum bookings" .
                    " - {$info->booked}/{$info->capacity} (min capacity {$info->mincapacity}) - emailing session roles."
                );
            }

            // Get all the users who need to receive the under capacity warning.
            $modcontext = \context_module::instance($cm->id);

            // Note: remove the true to limit to users with the roles within the module.
            $recipients = get_role_users(explode(',', $this->roles), $modcontext, true, 'u.*');

            // And send them the notifications.
            foreach ($recipients as $recipient) {
                // At this point, to prevent the object $eventdata added up data, it needed to be cloned and reset after sent.
                $copy = clone $eventdata;
                $copy->userto = $recipient;

                tm_alert_send($copy);
                unset($copy);
            }
        }
    }

    /**
     * Getting the last crontime run here, so that our query can be based on it.
     * @return int
     */
    private function get_notification_task_last_cron(): int {
        global $DB;

        $conditions = [
            'component' => 'mod_facetoface',
            'classname' => send_notifications_task::class
        ];

        $lastcron = $DB->get_field('task_scheduled', 'lastruntime', $conditions);
        return (int) $lastcron;
    }

    /**
     * Send out email notifications for all sessions where registration period has ended.
     *
     * @return void
     */
    public function notify_registration_ended(): void {
        global $DB;

        if (empty($this->roles)) {
            return;
        }

        $now = time();
        $lastcron = $this->get_notification_task_last_cron();

        $params = [
            'now' => $now,
            'lastcron' => $lastcron
        ];

        $sql = "SELECT s.*, dates.minstart
            FROM {facetoface_sessions} s
                INNER JOIN (
                    SELECT s.id AS sessid, MIN(d.timestart) AS minstart
                    FROM {facetoface_sessions} s
                    INNER JOIN {facetoface_sessions_dates} d ON s.id = d.sessionid
                    GROUP BY s.id
                ) dates ON dates.sessid = s.id
            WHERE s.registrationtimefinish < :now
            AND s.registrationtimefinish >= :lastcron
            AND s.registrationtimefinish != 0";

        $records = $DB->get_records_sql($sql, $params);

        foreach ($records as $record) {
            $notification = new \facetoface_notification((array) $record, false);
            $notification->send_notification_registration_expired($record);
        }
    }
}