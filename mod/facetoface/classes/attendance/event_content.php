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

use mod_facetoface\output\take_attendance_status_picker;
use mod_facetoface\signup\state\{not_set, booked};
use moodle_url;
use totara_table;
use stdClass;
use html_writer;

/**
 * This class was created with the purpose to only be used within this sub-package. And the purpose
 * of it is to generate the content table of event attendance tracking, which is either interactive
 * content or readonly content (downloadable content as well)
 *
 * Please do not instantiate it somewhere else apart from this sub-package and test place.
 *
 * Class event_content
 * @package mod_facetoface\attendance
 */
final class event_content extends content_generator {
    /**
     * Creating the table for either editable content or readonly content.
     * For editable content, it include the checkbox at the very first column. Where as readonly
     * content, that column should not be included at all.
     *
     * @param bool $iseditable
     * @param moodle_url $url
     * @return totara_table
     */
    private function do_create_table(bool $iseditable, moodle_url $url): totara_table {
        $table = new totara_table('event-tracking-attendance');
        $actionurl = clone $url;
        $actionurl->params(
            [
                'sesskey' => sesskey(),
                'onlycontent' => true,
                'action' => $this->action,
                'takeattendance' => 1
            ]
        );

        $table->define_baseurl($actionurl);
        $table->set_attribute('class', 'generalbox mod-facetoface-attendees ' . $this->action);

        $headers = ['', get_string('learner')];
        $columns = ['checkbox', 'name'];

        if ($this->seminarevent->get_seminar()->get_sessionattendance()) {
            foreach ($this->statusoptions as $key => $label) {
                $identifier = "status-code-{$key}";
                $o = new stdClass();
                $o->statuslabel = $label;

                $headers[] = get_string('sessionstatus', 'mod_facetoface', $o);
                $columns[] = $identifier;
            }
        }

        // Adding the other columns here after those status columns
        $headers = array_merge($headers, [get_string('eventattendanceheader', 'mod_facetoface')]);
        $columns = array_merge($columns, ['attendance']);

        if (!$iseditable) {
            // Because this is not an edit able content, therefore, we should not allow these boxes
            // in the table.
            array_shift($headers);
            array_shift($columns);
        }

        $table->define_headers($headers);
        $table->define_columns($columns);
        $table->setup();
        return $table;
    }

    /**
     * Generating a table of taking attendance, however, with this table, the system is allowing
     * the editor to be able to edit the content. Therefore, it is quite different from downloadable
     * content or readonly table. As there should have no checkbox or selection box included.
     *
     * @param array $rows
     * @param moodle_url $url
     * @return totara_table
     */
    public function generate_allowed_action_content(array $rows, moodle_url $url): totara_table {
        $table = $this->do_create_table(true, $url);

        $seminar = $this->seminarevent->get_seminar();
        $courseid = $seminar->get_course();
        $isessionattendance = $seminar->get_sessionattendance();

        $helper = new attendance_helper();
        $stats = $helper->get_calculated_session_attendance_status($this->seminarevent->get_id());

        foreach ($rows as $row) {
            $data = [];

            $attendee = clone $row;
            if (!$attendee->id) {
                continue;
            }

            $this->reset_attendee_statuscode($attendee);
            $data[] = $this->create_checkbox($attendee);
            $data[] = html_writer::link(
                new moodle_url(
                    "/user/view.php",
                    [
                        'id' => $attendee->id,
                        'course' => $courseid,
                    ]
                ),
                fullname($attendee)
            );

            if ($isessionattendance) {
                $stat = isset($stats[$attendee->id]) ? $stats[$attendee->id] : [];
                // If seminar enabled session tracking, which means that our editor is able to
                // take attendance at session's date level.

                foreach ($this->statusoptions as $key => $label) {
                    $data[] = isset($stat[$key]) ? (int) $stat[$key] : 0;
                }
            }

            $data[] = $this->create_attendance_status($attendee);

            // If grade is needed, please add it here
            $table->add_data($data);
        }

        $table->finish_html();
        return $table;
    }

    /**
     * @inheritdoc
     * @param array $rows
     * @return array Array<string, array>
     */
    public function generate_downloadable_content(array $rows): array {
        $headers = [get_string('learner')];

        if ($this->seminarevent->get_seminar()->get_sessionattendance()) {
            // Only incude those statuses header, if the session attendance is enabled for seminar.
            foreach ($this->statusoptions as $key => $label) {
                $headers[] = $label;
            }
        }

        $headers[] = get_string('eventattendanceheader', 'mod_facetoface');
        $definition = [
            'headers' => $headers,
            'rows' => []
        ];

        if (empty($rows)) {
            return $definition;
        }

        $exportrows = [];
        $helper = new attendance_helper();
        $stats = $helper->get_calculated_session_attendance_status($this->seminarevent->get_id());

        $issessionattendance = $this->seminarevent->get_seminar()->get_sessionattendance();

        foreach ($rows as $attendee) {
            $data = [];
            if (!$attendee->id) {
                continue;
            }

            $this->reset_attendee_statuscode($attendee);
            $data[] = fullname($attendee);

            if ($issessionattendance) {
                $stat = isset($stats[$attendee->id]) ? $stats[$attendee->id] : [];
                foreach ($this->statusoptions as $code => $label) {
                    $data[] = isset($stat[$code]) ? (int) $stat[$code] : 0;
                }
            }

            $attendancestatus = not_set::get_string();
            if (isset($this->statusoptions[$attendee->statuscode])) {
                $attendancestatus = $this->statusoptions[$attendee->statuscode];
            }

            $data[] = $attendancestatus;
            $exportrows[] = $data;
        }

        $definition['rows'] = $exportrows;
        return $definition;
    }

    /**
     * @inheritdoc
     * @param stdClass $attendee
     * @return string
     */
    protected function create_attendance_status(stdClass $attendee): string {
        global $OUTPUT;

        // Disabled will happen if the seminar_event is not open for attendance.
        $disabled = !$this->seminarevent->is_attendance_open();
        return $OUTPUT->render(
            take_attendance_status_picker::create($attendee, $this->statusoptions, $disabled)
        );
    }
}