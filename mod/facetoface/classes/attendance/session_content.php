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

use moodle_url;
use totara_table;
use mod_facetoface\signup\state\not_set;
use html_writer;

/**
 * This class was created with the purpose to only be used within this sub-package. And the purpose
 * of it is to generate the content table of session attendance tracking, which is either interactive
 * content or downloadable content.
 *
 * Please do not instantiate it somewhere else apart from this sub-package and test place.
 *
 * Class session_content
 * @package mod_facetoface\attendance
 */
final class session_content extends content_generator {
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
        $table = new totara_table('event-take-attendance');
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

        $headers = [
            '',
            get_string('learner'),
            get_string('sessionattendanceheader', 'mod_facetoface'),
        ];

        $columns = ['checkbox', 'name', 'attendance'];
        if (!$iseditable) {
            // If it is not able to edit the content, there is no point to display the checkbox
            // here before the learner name.
            array_shift($headers);
            array_shift($columns);
        }

        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->setup();
        return $table;
    }

    /**
     * Generating the sessions table that is allowed to be edit on browser for user user.
     *
     * @param array         $rows
     * @param moodle_url    $url
     * @return totara_table
     */
    public function generate_allowed_action_content(array $rows, moodle_url $url): totara_table {
        $table = $this->do_create_table(true, $url);

        foreach ($rows as $row) {
            $data = [];
            $attendee = clone($row);
            if (!$attendee->id) {
                // We only populate the attendee name if it is has an Id associated with, otherwise,
                // we skip it. This happened because a manager could reserve a space for their staff
                // but the staff never completed the reservation, therefore it should not be count
                // as taking attendance.
                continue;
            }

            $this->reset_attendee_statuscode($attendee);

            $data[] = $this->create_checkbox($attendee);
            $data[] = html_writer::link(
                new moodle_url(
                    '/user/view.php',
                    [
                        'id' => $attendee->id,
                        'course' => $attendee->course,
                    ]
                ),
                fullname($attendee)
            );

            // Attendance's status of the single attendee
            $data[] = $this->create_attendance_status($attendee);
            $table->add_data($data);
        }

        $table->finish_html();
        return $table;
    }

    /**
     * @param array $rows
     * @return array Array<string, array>
     *
     * @inheritdoc
     */
    public function generate_downloadable_content(array $rows): array {
        $headers = [
            get_string('learner'),
            get_string('sessionattendanceheader', 'mod_facetoface')
        ];

        $definition = [
            'headers' => $headers,
            'rows' => [],
        ];

        if (empty($rows)) {
            return $definition;
        }

        $exportrows = [];

        foreach ($rows as $attendee) {
            $data = [];
            if (!$attendee->id) {
                continue;
            }

            $this->reset_attendee_statuscode($attendee);
            $data[] = fullname($attendee);

            $attendancestatus = not_set::get_code();
            if (isset($this->statusoptions[$attendee->statuscode])) {
                $attendancestatus = $this->statusoptions[$attendee->statuscode];
            }

            $data[] = $attendancestatus;
            $exportrows[] = $data;
        }

        $definition['rows'] = $exportrows;
        return $definition;
    }
}