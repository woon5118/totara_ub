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
 * @package theme_ventura
 */

namespace theme_ventura\controllers;

use totara_mvc\admin_controller;
use totara_mvc\tui_view;
use totara_tenant\entity\tenant;

class theme_tenants extends admin_controller {

    /**
     * @inheritDoc
     */
    protected $admin_external_page_name = 'ventura_editor';

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
    public function action(): tui_view {
        global $CFG;

        // Redirect to settings if tenants disabled.
        if (empty($CFG->tenantsenabled)) {
            $settings_url = "{$CFG->wwwroot}/theme/ventura/theme_settings.php";
            redirect($settings_url);
        }

        // Get theme_config.
        $theme_config = \theme_config::load('ventura');

        // Get tenant information for the tui view.
        $tenants = tenant::repository()->select(['id', 'idnumber', 'name'])->get()->to_array();
        $tenants = array_map(function ($tenant) use ($theme_config) {
            $theme_settings = new \core\theme\settings($theme_config, $tenant['id']);
            $tenant['customBranding'] = $theme_settings->is_enabled('tenant', 'formtenant_field_tenant');
            return $tenant;
        }, $tenants);
        $props = [
            'theme' => 'ventura',
            'tenants' => $tenants
        ];

        $tui_view = tui_view::create('theme_ventura/pages/Tenants', $props);
        $tui_view->set_title(get_string('select_tenant', 'theme_ventura'));

        return $tui_view;
    }

}