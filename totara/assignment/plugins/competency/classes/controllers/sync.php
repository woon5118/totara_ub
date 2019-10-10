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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package tassign_competency
 */

namespace tassign_competency\controllers;

use context;
use context_system;
use core\output\notification;
use moodle_url;
use tassign_competency\task\expand_assignment_task;
use totara_core\advanced_feature;
use totara_mvc\controller;

class sync extends controller {

    protected $layout = 'noblocks';

    public function action() {
        $this->require_capability('tassign/competency:manage', $this->context);

        $back_url = new moodle_url('/totara/assignment/plugins/competency/users.php');
        if (expand_assignment_task::is_scheduled()) {
            $message = get_string('sync:is_scheduled', 'tassign_competency');
            $message_type = notification::NOTIFY_ERROR;
        } else {
            $this->trigger_adhoc_task();

            $message = get_string('sync:success', 'tassign_competency');
            $message_type = notification::NOTIFY_SUCCESS;
        }

        redirect($back_url, $message, null, $message_type);
    }

    private function trigger_adhoc_task() {
        expand_assignment_task::schedule_for_all($this->currently_logged_in_user()->id);
    }

    /**
     * Override get_default_context() either returning a system or a specific context.
     * You can also call $this->set_context() in your action later
     *
     * @return context
     */
    protected function setup_context(): context {
        advanced_feature::require('perform');

        return context_system::instance();
    }
}
