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

namespace mod_facetoface\output;
defined('MOODLE_INTERNAL') || die();

use core\output\template;
use mod_facetoface\seminar_event;
use moodle_url;

/**
 * Class take_attendance
 * @package mod_facetoface\output
 */
class take_attendance extends template {
    /**
     * Creating a renderable object for taking attendance form here.
     *
     * @param seminar_event                 $seminarevent   The seminar's event that this form is
     *                                                      taking attendance for
     *
     * @param moodle_url                    $url            Base url, without any parameters
     *
     * @param string                        $tablecontent   HTML code of totara_table
     *
     * @param array                         $formattributes The form's attribute for
     *
     * @param take_attendance_bulk_action   $bulkaction     Bulk action widget
     *
     * @param int                           $sessiondateid  Session date id, of the same event
     *
     * @param take_attendance_session_picker $sessionpicker session picker widget, if this is being
     *                                                      set, then most likely it will be displayed
     *                                                      otherwise, it will not, as this behaviour
     *                                                      is for whether setting of attendance
     *                                                      tracking is being set or not.
     * @return take_attendance
     */
    public static function create(seminar_event $seminarevent,
                                  moodle_url $url,
                                  string $tablecontent,
                                  array $formattributes,
                                  take_attendance_bulk_action $bulkaction,
                                  int $sessiondateid = 0,
                                  ?take_attendance_session_picker $sessionpicker = null): take_attendance {
        global $USER;
        $backurl = new moodle_url("/mod/facetoface/view.php");
        $backurl->param('f', $seminarevent->get_seminar()->get_id());

        $data = [
            'url' => $url->out(),
            'backurl' => $backurl->out(),
            'sessionid' => $seminarevent->get_id(),
            'sessiondateid' => $sessiondateid,
            'formattributes' => [],
            'hiddeninputs' => [
                [
                    'name' => 'sesskey',
                    'value' => $USER->sesskey,
                ],
                [
                    'name' => 's',
                    'value' => $seminarevent->get_id(),
                ]
            ],

            'tablecontent' => $tablecontent,
            'sessionpicker' => null,
            'bulkaction' => $bulkaction->get_template_data(),
            'exports' => [
                [
                    'value' => 'exportcsv',
                    'label' => get_string('exportcsv', 'mod_facetoface')
                ],
                [
                    'value' => 'exportxls',
                    'label' => get_string('exportxls', 'mod_facetoface'),
                ],
                [
                    'value' => 'exportods',
                    'label' => get_string('exportods', 'mod_facetoface'),
                ]
            ]
        ];

        foreach ($formattributes as $key => $value) {
            $data['formattributes'][] = [
                'name' => $key,
                'value' => $value
            ];
        }

        if (null != $sessionpicker) {
            $data['sessionpicker'] = $sessionpicker->get_template_data();
        }

        return new static($data);
    }
}