<?php
/*
 * This file is part of Totara Learn
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package core_course
 * @category totara_catalog
 */

namespace core_course\totara_catalog\course\dataholder_factory;

defined('MOODLE_INTERNAL') || die();

use totara_catalog\dataformatter\formatter;
use totara_catalog\dataholder;
use totara_catalog\dataholder_factory;
use core_course\totara_catalog\course\dataformatter\progressbar as course_progressbar_formatter;

class progressbar extends dataholder_factory {

    public static function get_dataholders(): array {
        global $USER;

        return [
            new dataholder(
                'progressbar',
                'notused',
                [
                    formatter::TYPE_PLACEHOLDER_PROGRESS => new course_progressbar_formatter(
                        'progressbar_cc.course',
                        'progressbar_cc.status'
                    ),
                ],
                [
                    'progressbar_cc' =>
                        'LEFT JOIN (
                            SELECT cc.*
                              FROM {course_completions} cc
                        INNER JOIN {user_enrolments} ue
                                ON cc.userid = ue.userid
                               AND ue.status = :progressbar_ue_active
                        INNER JOIN {enrol} e
                                ON e.id = ue.enrolid
                               AND e.courseid = cc.course
                         ) progressbar_cc
                           ON progressbar_cc.course = base.id
                          AND progressbar_cc.userid = :progressbar_userid',
                ],
                [
                    'progressbar_userid' => $USER->id,
                    'progressbar_ue_active' => ENROL_USER_ACTIVE,
                ]
            )
        ];
    }
}
