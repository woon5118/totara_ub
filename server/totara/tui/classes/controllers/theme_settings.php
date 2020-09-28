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
    protected $tenantid;

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
        global $CFG, $USER;

        // Get the theme name from parameter.
        $this->theme = $this->get_required_param('theme', PARAM_COMPONENT);
        $this->tenantid = $this->get_optional_param('tenant_id', null, PARAM_INT);

        require_login(null, false);

        if (!is_null($this->tenantid)) {
            $tenant = tenant::repository()->find($this->tenantid);
            if (empty($tenant)) {
                throw new \moodle_exception('errorinvalidtenant', 'totara_tenant');
            }
            $context = \context_tenant::instance($this->tenantid);
        } else {
            $context = \context_system::instance();
        }
        if ($context->is_user_access_prevented()) {
            throw new \moodle_exception('accessdenied', 'admin', '', null, $context->id . ' prevented access');
        }

        $url = new \moodle_url('/totara/tui/classes/controllers/theme_settings.php', ['theme' => $this->theme]);

        if (!empty($USER->tenantid) && $USER->tenantid != $this->tenantid) {
            redirect(new \moodle_url($url, ['tenant_id' => $USER->tenantid]));
        }

        if (!empty($this->tenantid) && empty($USER->tenantid)) {
            $tenant = \core\record\tenant::fetch($this->tenantid);
            $categorycontext = \context_coursecat::instance($tenant->categoryid);
            require_capability('totara/tui:themesettings', $categorycontext);
            $this->get_page()->set_pagelayout('admin');
            $this->get_page()->set_title(get_site()->shortname);
            $this->get_page()->set_heading(get_site()->fullname);
        } else {
            require_once($CFG->libdir.'/adminlib.php');
            admin_externalpage_setup(
                'ventura_editor',
                '', // not used
                ['theme' => $this->theme],
                '',
                []
            );
        }

        $this->get_page()->set_url($url);
        $this->set_url($url);

        parent::process($action);
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