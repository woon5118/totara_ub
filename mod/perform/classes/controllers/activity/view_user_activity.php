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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers\activity;

use context;
use mod_perform\models\activity\subject_instance;
use mod_perform\util;
use moodle_url;
use totara_mvc\controller;
use totara_mvc\tui_view;

/*
 * This page lists perform activities the logged in user are a participant in.
 */
class view_user_activity extends controller {

    /**
     * @inheritDoc
     */
    protected function setup_context(): context {
        $category_id = util::get_default_categoryid();
        return \context_coursecat::instance($category_id);
    }

    /**
     * @return tui_view
     */
    public function action(): tui_view {
        $props = [
            'subject-instance-id' => $this->get_subject_instance_id(),
        ];

        return tui_view::create('mod_perform/pages/UserActivity', $props)
            ->set_title(get_string('user_activities:page_title', 'mod_perform'))
            ->set_url(self::get_url());
    }

    public static function get_url(): moodle_url {
        return new moodle_url('/mod/perform/activity/view.php');
    }

    protected function get_subject_instance_id(): int {
        return required_param('subject_instance_id', PARAM_INT);
    }

}