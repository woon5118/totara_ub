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

use context;
use moodle_url;
use totara_mvc\controller;
use totara_mvc\tui_view;
use totara_tenant\entity\tenant;

class theme_settings extends controller {

    /**
     * @var string
     */
    protected $theme;

    /**
     * The tenant id, if one was provided.
     * @var int
     */
    protected $tenant_id;

    /**
     * @inheritDoc
     */
    protected $layout = 'standard';

    /**
     * @inheritDoc
     */
    protected function setup_context(): context {
        return \context_system::instance();
    }

    /**
     * @inheritDoc
     */
    public function process(string $action = '') {
        global $CFG, $USER;

        // Get the theme name from parameter.
        $this->theme = $this->get_required_param('theme_name', PARAM_COMPONENT);
        $this->tenant_id = $this->get_optional_param('tenant_id', null, PARAM_INT);

        require_login(null, false);
        $url = new moodle_url('/totara/tui/theme_settings.php', ['theme_name' => $this->theme]);
        if (!empty($this->tenant_id)) {
            $url->param('tenant_id', $this->tenant_id);
        }
        $this->set_url($url);

        // Redirect to the correct tenant this user belongs to.
        if (!empty($USER->tenantid) && $USER->tenantid != $this->tenant_id) {
            redirect(new moodle_url($url, ['tenant_id' => $USER->tenantid]));
        }

        // Request access to theme settings for tenant or site.
        $tenant = null;
        if (!empty($this->tenant_id)) {
            $tenant = tenant::repository()->find($this->tenant_id);
            if (empty($tenant)) {
                throw new \moodle_exception('errorinvalidtenant', 'totara_tenant');
            }
            $context = \context_tenant::instance($this->tenant_id);
            $this->check_user_access($context);

            // If user does not belong to the tenancy but has the required access then let the user through.
            if (empty($USER->tenantid)) {
                $categorycontext = \context_coursecat::instance($tenant->categoryid);
                require_capability('totara/tui:themesettings', $categorycontext);
                $this->get_page()->set_pagelayout('admin');
                $this->get_page()->set_title(get_site()->shortname);
                $this->get_page()->set_heading(get_site()->fullname);
                parent::process($action);
                return;
            }
        } else {
            $context = \context_system::instance();
            $this->check_user_access($context);
        }

        // At this point:
        // 1. Tenant ID is not set as a parameter which means we want access to site's theme settings.
        // 2. Tenant ID is set and user belongs to the tenancy.
        require_once($CFG->libdir.'/adminlib.php');
        admin_externalpage_setup(
            "{$this->theme}_editor",
            '', // not used
            ['theme' => $this->theme],
            '',
            []
        );

        parent::process($action);
    }

    /**
     * Confirm that the user has access to this page.
     *
     * @param context $context
     */
    private function check_user_access(context $context): void {
        if ($context->is_user_access_prevented()) {
            throw new \moodle_exception(
                'accessdenied',
                'admin',
                '',
                null,
                $context->id . ' prevented access'
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function action(): tui_view {
        global $CFG;

        $props = [
            'theme' => $this->theme,
            'themeName' => get_string_manager()->string_exists('pluginname', 'theme_' . $this->theme)
                ? get_string('pluginname', 'theme_' . $this->theme)
                : null,
        ];

        // If tenant is selected then get selected tenant details.
        $tenant_id = $this->get_optional_param('tenant_id', null,PARAM_INT);
        if (!empty($tenant_id) && $CFG->tenantsenabled) {
            /** @var tenant $tenant */
            $tenant = tenant::repository()->find($tenant_id);
            if (empty($tenant)) {
                throw new \moodle_exception('errorinvalidtenant', 'totara_tenant');
            }

            // First confirm that the user has access to the specific tenant appearance settings.
            $categorycontext = \context_coursecat::instance($tenant->categoryid);
            require_capability('totara/tui:themesettings', $categorycontext);

            // Set up props for the component.
            $props['selectedTenantId'] = $tenant->id;
            $props['selectedTenantName'] = $tenant->name;
        } else {
            // User need capability to access site appearance settings.
            require_capability('totara/core:appearance', \context_system::instance());
        }

        $tui_view = tui_view::create('tui/pages/ThemeSettings', $props);
        $tui_view->set_title(get_string('theme_settings', 'totara_tui'));

        return $tui_view;
    }

}