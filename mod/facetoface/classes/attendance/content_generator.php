<?php
/*
 * This file is part of Totara Learn
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

namespace mod_facetoface\attendance;
defined('MOODLE_INTERNAL') || die();

use mod_facetoface\{seminar_event, seminar_session_list, attendees_list_helper};
use totara_table;
use moodle_url;
use stdClass;
use html_writer;
use mod_facetoface\signup\state\{not_set, booked};
use mod_facetoface\output\take_attendance_status_picker;

/**
 * This class was created with the purpose to only be used within this sub-package.
 * Please do not instantiate it somewhere else apart from this sub-package and test place.
 *
 * It is being used to generate the table of content for taking attendance, could be either an interactive content or
 * downloadable content.
 *
 * Class content_generator
 * @package mod_facetoface\attendance
 */
abstract class content_generator {
    /**
     * @var string
     */
    protected $action;

    /**
     * @var seminar_event
     */
    protected $seminarevent;

    /**
     * This will be loaded when needed, however it can be set at instantiation of the object.
     *
     * @var seminar_session_list|null
     */
    private $sessions;

    /**
     * Array<int, string> (status code -> status label)
     *
     * @var array
     */
    protected $statusoptions;

    /**
     * content_generator constructor.
     * @param seminar_event         $seminarevent
     * @param string                $action
     * @param seminar_session_list  $sessions
     */
    final public function __construct(seminar_event $seminarevent,
                                      string $action, ?seminar_session_list $sessions = null) {
        $this->seminarevent = $seminarevent;
        $this->action = $action;
        $this->sessions = $sessions;

        // Loading status options and reorder it here.
        $this->statusoptions = attendees_list_helper::get_status();
        krsort($this->statusoptions, SORT_NUMERIC);
    }

    /**
     * @param stdClass[] $data
     * @param moodle_url $url
     * @return totara_table
     */
    abstract public function generate_allowed_action_content(array $data, moodle_url $url): totara_table;

    /**
     * The keys of returning array should be specified as below:
     * + rows: array|stdClass[]
     * + headers: string[]
     *
     * @param stdClass[] $data
     * @return array    Array<string, array>
     */
    abstract public function generate_downloadable_content(array $data): array;

    /**
     * Lazy loading the seminar_session_list here, as if it is not set, we should set it.
     * @return seminar_session_list
     */
    final protected function get_sessions(): seminar_session_list {
        if (null == $this->sessions) {
            $this->sessions = $this->seminarevent->get_sessions();
        }

        return $this->sessions;
    }

    /**
     * Returning a HTML block for the checkbox to manipulate the attendance's status record. This
     * should only be called in generating the content that allow action on.
     *
     * @param stdClass $attendee
     * @return string
     */
    protected function create_checkbox(stdClass $attendee): string {
        $checkoptionid = 'check_submissionid_' . $attendee->submissionid;
        return html_writer::checkbox(
            $checkoptionid,
            $attendee->statuscode,
            false,
            '',
            [
                'class' => 'selectedcheckboxes',
                'data-selectid' => 'menusubmissionid_' . $attendee->submissionid
            ]
        );
    }

    /**
     * Returning a HTML string for a selection box of the attendance state, for the user to pick.
     * @param stdClass $attendee
     * @return string
     */
    protected function create_attendance_status(stdClass $attendee): string {
        global $OUTPUT;
        return $OUTPUT->render(
            take_attendance_status_picker::create($attendee, $this->statusoptions, false)
        );
    }

    /**
     * If the statuscode is booked, which means it should be not_set in taking attendance
     *
     * @param stdClass $attendee
     * @return void
     */
    protected function reset_attendee_statuscode(stdClass &$attendee): void {
        if ($attendee->statuscode == booked::get_code() || $attendee->statuscode == null) {
            $attendee->statuscode = not_set::get_code();
        }
    }
}