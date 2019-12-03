<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package pathway_manual
 */

namespace pathway_manual\controllers;

use pathway_manual\models\roles;
use pathway_manual\models\roles\manager;
use pathway_manual\models\roles\role;
use pathway_manual\models\roles\role_factory;
use pathway_manual\models\roles\self_role;
use totara_competency\controllers\profile\base;
use totara_mvc\tui_view;

class rate_competencies extends base {

    /**
     * @var role
     */
    private $role;

    public function __construct() {
        parent::__construct();

        $this->role = $this->get_role();
    }

    /**
     * Set up the page.
     *
     * @return tui_view
     */
    public function action() {
        $page_title = get_string('rate_competencies', 'pathway_manual');
        $this->add_navigation($page_title);

        return tui_view::create('pathway_manual/pages/RateCompetencies', [
            'user-id' => $this->user->id,
            'role' => $this->role::get_name(),
            'current-user-id' => (int)$this->currently_logged_in_user()->id,
            'go-back-link' => (string)$this->get_profile_url(),
        ])
            ->set_title($page_title);
    }

    /**
     * Validate that the logged in user has permission to view this page.
     *
     * @return self
     */
    protected function authorize() {
        // This is a subpage of competency profile, so make sure we are allowed to view it first.
        parent::authorize();

        if (empty($this->role)) {
            // The user has no roles, so requiring they have the manager role will print an error message.
            manager::require_for_user($this->user->id);
        }

        $this->role::require_for_user($this->user->id);

        return $this;
    }

    /**
     * Get the role that the user wishes to view competencies of.
     *
     * @return role|string
     */
    private function get_role() {
        if ($this->user->is_logged_in()) {
            // If the user is rating themselves, then it is always ROLE_SELF no matter what.
            return self_role::class;
        }

        $role_param = optional_param('role', null,PARAM_ALPHA);

        if ($role_param) {
            return role_factory::create($role_param);
        }

        $all_user_roles = roles::get_current_user_roles($this->user->id);

        if ($all_user_roles) {
            // No role specified via params, so get the first role the user has.
            return reset($all_user_roles);
        }

        // User has no roles
        return null;
    }

}
