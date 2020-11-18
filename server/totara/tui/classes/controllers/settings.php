<?php
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_tui
 */

namespace totara_tui\controllers;

use totara_mvc\admin_controller;

class settings extends admin_controller {

    /**
     * @var string
     */
    protected $theme;

    /**
     * @inheritDoc
     */
    protected $admin_external_page_name;

    /**
     * @inheritDoc
     */
    protected $layout = 'noblocks';

    /**
     * settings constructor.
     * @param string $theme
     */
    public function __construct(string $theme) {
        $this->theme = $theme;
        $this->admin_external_page_name = "{$theme}_editor";
        return parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function setup_context(): \context {
        return \context_system::instance();
    }

    /**
     * @inheritDoc
     */
    public function action() {
        global $CFG;

        // If multi-tenancy is enabled then we display tenants otherwise go straight to settings.
        if (!empty($CFG->tenantsenabled)) {
            $tenants_url = new \moodle_url("/totara/tui/theme_tenants.php", ['theme_name' => $this->theme]);
            redirect($tenants_url->out());
        } else {
            $settings_url = new \moodle_url("/totara/tui/theme_settings.php", ['theme_name' => $this->theme]);
            redirect($settings_url->out());
        }
    }

}