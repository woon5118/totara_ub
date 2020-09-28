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
use totara_mvc\tui_view;
use totara_tenant\entity\tenant;

class theme_tenants extends admin_controller {

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
    protected $layout = 'standard';

    /**
     * @inheritDoc
     */
    protected function setup_context(): \context {
        return \context_system::instance();
    }

    /**
     * @inheritDoc
     */
    public function process(string $action = '') {
        // Get the theme name from parameter.
        $this->theme = $this->get_required_param('theme', PARAM_COMPONENT);
        $this->admin_external_page_name = "{$this->theme}_editor";
        parent::process($action);
    }

    /**
     * @inheritDoc
     */
    public function action(): tui_view {
        global $CFG;

        // Redirect to settings if tenants disabled.
        if (empty($CFG->tenantsenabled)) {
            $settings_url = new \moodle_url("/totara/tui/theme_settings.php", ['theme' => $this->theme]);
            redirect($settings_url->out());
        }

        // Get theme_config.
        $theme_config = \theme_config::load($this->theme);

        // Get tenant information for the tui view.
        $tenants = tenant::repository()->select(['id', 'idnumber', 'name'])->get()->to_array();

        // Only get the tenants that the user has access to.
        $tenants = array_filter($tenants, function ($tenant) {
            $context = \context_tenant::instance($tenant['id']);
            return has_capability('totara/tui:themesettings', $context);
        });

        $tenants = array_map(function ($tenant) use ($theme_config) {
            $theme_settings = new \core\theme\settings($theme_config, $tenant['id']);
            $tenant['customBranding'] = $theme_settings->is_tenant_branding_enabled();
            return $tenant;
        }, $tenants);
        $props = [
            'theme' => $this->theme,
            'themeName' => get_string_manager()->string_exists('pluginname', 'theme_' . $this->theme)
                ? get_string('pluginname', 'theme_' . $this->theme)
                : null,
            'tenants' => $tenants
        ];

        $tui_view = tui_view::create('tui/pages/ThemeTenants', $props);
        $tui_view->set_title(get_string('select_tenant', 'totara_tui'));

        return $tui_view;
    }

}