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
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers\reporting\performance;

use context_coursecat;
use core\entities\user;
use mod_perform\controllers\perform_controller;
use mod_perform\util;
use totara_mvc\has_report;

class user_responses extends perform_controller {

    use has_report;

    /**
     * @inheritDoc
     */
    protected function setup_context(): \context {
        $category_id = util::get_default_category_id();
        return context_coursecat::instance($category_id);
    }

    public function action() {
        $this->set_url(static::get_url());

        $user_id = $this->get_required_param('user_id', PARAM_INT);

        /** @var user $user */
        $user = user::repository()->find($user_id);

        return 'Performance data for ' . $user->fullname;
    }

    public static function get_base_url(): string {
        return '/mod/perform/reporting/performance/user_responses.php';
    }
}
