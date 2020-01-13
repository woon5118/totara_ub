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
use pathway_manual\models\roles\self_role;
use totara_competency\controllers\profile\base;
use totara_mvc\tui_view;

class rate_competencies extends base {

    /**
     * @var role|null
     */
    private $role;

    /**
     * Set up the page.
     *
     * @return tui_view
     */
    public function action() {
        $page_title = get_string('rate_competencies', 'pathway_manual');
        $this->add_navigation($page_title);

        $vue_props = [
            'user' => [
                'id' => $this->user->id,
                'fullname' => $this->user->fullname,
                'profileimageurl' => $this->get_user_picture_url(),
            ],
            'current-user-id' => (int)$this->currently_logged_in_user()->id,
            'go-back-link' => (string)$this->get_profile_url(),
        ];

        if ($this->role) {
            $vue_props['role'] = $this->role::get_name();
        }

        if ($assignment_id = $this->get_param('assignment_id', PARAM_INT)) {
            $vue_props['assignment-id'] = $assignment_id;
        }

        return tui_view::create('pathway_manual/pages/RateCompetencies', $vue_props)
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

        $role_param = $this->get_param('role', PARAM_ALPHANUMEXT);
        if ($role_param) {
            $specified_role = roles\role_factory::create($role_param);
            $specified_role::require_for_user($this->user->id);
            $this->role = $specified_role;
        } else {
            $roles = roles::get_current_user_roles($this->user->id);

            if (empty($roles)) {
                // The user has no roles, so requiring they have the self/manager role will print an error message.
                if ($this->user->is_logged_in()) {
                    self_role::require_for_user($this->user->id);
                } else {
                    manager::require_for_user($this->user->id);
                }
            }
        }

        return $this;
    }

}
