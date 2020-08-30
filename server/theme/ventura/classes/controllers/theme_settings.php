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

class theme_settings extends admin_controller {

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
        $props = [
            'theme' => 'ventura',
        ];

        // If tenant is selected then get selected tenant details.
        $tenant_id = $this->get_optional_param('tenant_id', null,PARAM_INT);
        if (!empty($tenant_id)) {
            /** @var tenant $tenant */
            $tenant = tenant::repository()->find($tenant_id);
            if (!empty($tenant)) {
                $props['selectedTenantId'] = $tenant->id;
                $props['selectedTenantName'] = $tenant->name;
            }
        }

        $tui_view = tui_view::create('theme_ventura/pages/Settings', $props);
        $tui_view->set_title(get_string('theme_settings', 'theme_ventura'));

        return $tui_view;
    }

}