<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\query;

use core\collection;
use core\entities\user;
use core\orm\query\builder;
use core\orm\query\sql\query as sql_query;
use core\orm\query\table;
use core\webapi\execution_context;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;
use moodle_url;
use totara_core\advanced_feature;

require_once($CFG->dirroot . '/totara/appraisal/lib.php');
require_once($CFG->dirroot . '/totara/feedback360/lib.php');

class historic_activities implements query_resolver, has_middleware {

    /**
     * @inheritDoc
     */
    public static function resolve(array $args, execution_context $ec) {

        $subject_id = user::logged_in()->id;

        $appraisals = self::get_appraisals($subject_id);
        $feedbacks = self::get_feedbacks($subject_id);

        $data = array_merge($appraisals, $feedbacks);

        return new collection($data);
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            new require_login()
        ];
    }


    private static function get_appraisals(int $userid): array {

        $data = [];
        if (advanced_feature::is_disabled('appraisals')) {
            return $data;
        }

        if (!\appraisal::can_view_own_appraisals($userid)) {
            return $data;
        }

        $appraisals = \appraisal::get_user_appraisals_extended($userid, \appraisal::ROLE_LEARNER);
        foreach ($appraisals as $appraisal) {
            $params = [
                'role' => \appraisal::ROLE_LEARNER,
                'subjectid' => $appraisal->userid,
                'appraisalid' => $appraisal->id,
                'action' => 'stages'
            ];
            $appraisal_link = new moodle_url('/totara/appraisal/myappraisal.php', $params);

            $data[] = [
                'activity_name' => format_string($appraisal->name),
                'activity_link' => $appraisal_link->out(false),
                'type' => get_string('appraisal_legacy', 'totara_appraisal'),
                'status' => \appraisal::display_status($appraisal->status)
            ];
        }
        return $data;
    }

    private static function get_feedbacks(int $userid): array {
        global $DB;

        $data = [];
        if (advanced_feature::is_disabled('feedback360')) {
            return $data;
        }

        if (!\feedback360::can_view_feedback360s($userid)) {
            return $data;
        }

        // Join the user assignment to the feedback360 so we have the name later.
        $builder = builder::table('feedback360_user_assignment', 'ua')
            ->join((new table('feedback360'))->as('fb'), 'fb.id', '=', 'ua.feedback360id')
            ->select(['ua.*', 'fb.name', 'fb.anonymous', 'fb.status'])
            ->where('ua.userid', '=', $userid);
        [$sql, $params] = sql_query::from_builder($builder)->build();
        $user_assignments = $DB->get_records_sql($sql, $params);
        foreach ($user_assignments as $user_assignment) {
            $feedback_link = new moodle_url('/totara/feedback360/index.php');
            $data[] = [
                'activity_name' => format_string($user_assignment->name),
                'activity_link' => $feedback_link->out(false),
                'type' => get_string('feedback360:utf8', 'totara_feedback360'),
                'status' => \feedback360::display_status($user_assignment->status)
            ];
        }
        return $data;
    }
}