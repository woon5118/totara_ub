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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers\reporting\performance;

use context;
use context_system;
use core\entities\user;
use mod_perform\controllers\perform_controller;
use mod_perform\util;
use moodle_url;
use required_capability_exception;

class activity_response_data extends perform_controller {
    use activity_response_data_tabs;

    /** @var string URL used in menu */
    public const URL =  '/mod/perform/reporting/performance/index.php';

    /**
     * @var string
     */
    private $current_tab;

    public function setup_context(): context {
        return context_system::instance();
    }

    public static function get_base_url(): string {
        return '/mod/perform/reporting/performance/';
    }

    public function action() {
        return $this->action_by_user();
    }

    public function action_by_user() {
        $this->check_access();

        $this->current_tab = 'by_user';

        // TODO: Replace with embedded_report_view with correct template and data
        return $this->render_dummy_page();
    }

    public function action_by_content() {
        $this->check_access();

        $this->current_tab = 'by_content';

        // TODO: Replace with correct template and data
        return $this->render_dummy_page();
    }

    private function check_access() {
        if (!util::can_potentially_report_on_subjects(user::logged_in()->id)) {
            throw new required_capability_exception($this->get_context(),
                'mod/perform:report_on_subject_responses', 'nopermissions', ''
            );
        }
    }

    private function render_dummy_page() {
        $data = [
            'tabs' => self::get_activity_response_data_tabs($this->current_tab),
            'page_heading' => get_string('performance_activity_response_data_heading', 'mod_perform'),
            'name' => $this->current_tab,
        ];

        $url = static::get_base_url() . 'activity_responses_' . $this->current_tab . '.php';
        $this->set_url(new moodle_url($url));

        return self::create_view('mod_perform/tabbed_dummy', $data)
            ->set_title($data['page_heading']);
    }
}
