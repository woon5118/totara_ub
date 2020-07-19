<?php

namespace container_perform\watcher;

use container_perform\perform;
use core_container\hook\base_redirect;
use core_course\hook\course_edit_view;
use moodle_url;

/**
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_userstatus
 */

class course {

    public static function redirect_to_other_page(base_redirect $hook) {
        $container = $hook->get_container();
        if ($container->is_typeof(perform::get_type())) {
            // TODO: Add a new page to redirect to with proper error message
            redirect(new moodle_url('/mod/perform/manage/activity/index.php'));
        }
    }

}