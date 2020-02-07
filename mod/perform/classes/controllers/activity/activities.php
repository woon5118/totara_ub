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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\controllers\activity;

use context;
use context_system;
use totara_mvc\controller;
use totara_mvc\tui_view;

class activities extends controller {

    /**
     * @inheritDoc
     */
    protected function setup_context(): context {
        return context_system::instance();
    }

    /**
     * @return tui_view
     */
    public function action(): tui_view {
        $this->require_capability('mod/perform:view', $this->get_context());

        return tui_view::create('mod_perform/pages/Activities', [])
            ->set_title(get_string('perform:manage', 'mod_perform'));
    }

}