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
use mod_perform\models\activity\subject_instance;
use moodle_exception;
use moodle_url;
use totara_mvc\has_report;
use totara_mvc\report_view;

class participant_instances extends perform_controller {

    use has_report;

    private $subject_instance;

    public function setup_context(): context {
        return $this->get_subject_instance()->get_context();
    }

    public function action() {
        parent::action();

        $report = $this->load_embedded_report('perform_participant_instance', [
            'subject_instance_id' => $this->get_subject_instance()->id,
        ]);

        $page_title = get_string('participant_instances_title', 'mod_perform', [
            'activity_name' => $this->get_subject_instance()->get_activity()->name,
            'fullname' => $this->subject_instance->subject_user->fullname
        ]);

        return (new report_view('mod_perform/report', $report))
            ->set_title($page_title)
            ->set_url(static::get_url(['subject_instance_id' => $this->get_subject_instance()->id]))
            ->set_backto(
                new moodle_url(
                    '/mod/perform/reporting/participation/index.php',
                    ['activity_id' => $this->get_subject_instance()->get_activity()->id]
                ),
                get_string('back_to_activity', 'mod_perform')
            );
    }

    public static function get_base_url(): string {
        return '/mod/perform/reporting/participation/participants.php';
    }

    private function get_subject_instance(): subject_instance {
        if (!isset($this->subject_instance)) {
            $subject_instance_id = $this->get_param('subject_instance_id', PARAM_INT, null, true);

            try {
                $this->subject_instance = subject_instance::load_by_id($subject_instance_id);
            } catch (\Exception $e) {
                throw new moodle_exception('error_subject_instance_id_wrong', 'mod_perform', '', null, $e);
            }
        }

        return $this->subject_instance;
    }

}
