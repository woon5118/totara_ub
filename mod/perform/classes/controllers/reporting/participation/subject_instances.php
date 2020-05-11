<?php
/*
 * This file is part of Totara Perform
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers\reporting\participation;

use context;
use mod_perform\controllers\perform_controller;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\subject_instance;
use moodle_exception;
use totara_mvc\has_report;
use totara_mvc\report_view;

class subject_instances extends perform_controller {

    use has_report;

    private $activity;

    public function setup_context(): context {
        return $this->get_activity()->get_context();
    }

    public function action() {
        parent::action();

        $report = $this->load_embedded_report('perform_subject_instance', ['activity_id' => $this->get_activity()->id]);

        $sid = $this->get_param('sid', PARAM_INT, 0);
        $debug = $this->get_param('debug', PARAM_INT, 0);

        return (new report_view('mod_perform/report', $report, $sid, $debug))
            ->set_title($this->get_activity()->name)
            ->set_url(static::get_url(['activity_id' => $this->get_activity()->id]))
            ->set_backto(
                new \moodle_url('/mod/perform/manage/activity/index.php'),
                get_string('back_to_all_activities', 'mod_perform')
            );
    }

    public static function get_base_url(): string {
        return '/mod/perform/reporting/participation/index.php';
    }

    private function get_activity(): activity {
        if (!isset($this->activity)) {
            $activity_id = $this->get_param('activity_id', PARAM_INT, null, true);

            try {
                $this->activity = activity::load_by_id($activity_id);
            } catch (\Exception $e) {
                throw new moodle_exception('error_activity_id_wrong', 'mod_perform', '', null, $e);
            }
        }

        return $this->activity;
    }

}
