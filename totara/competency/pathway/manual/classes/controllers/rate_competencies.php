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

use context;
use context_user;
use core\entities\user;
use moodle_url;
use pathway_manual\models\roles;
use pathway_manual\models\roles\manager;
use pathway_manual\models\roles\role;
use pathway_manual\models\roles\self_role;
use totara_competency\helpers\capability_helper;
use totara_mvc\controller;
use totara_mvc\tui_view;
use totara_mvc\view;

class rate_competencies extends controller {

    use has_role;

    /**
     * User id passed through the attribute
     *
     * @var user
     */
    protected $user;

    /**
     * @return context_user
     */
    protected function setup_context(): context {
        return context_user::instance($this->user->id);
    }

    public function __construct() {
        require_login();
        $user_id = $this->get_param('user_id', PARAM_INT);
        $this->user = $user_id ? new user($user_id) : user::logged_in();

        parent::__construct();
    }

    /**
     * Set up the page.
     *
     * @return tui_view
     */
    public function action() {
        $vue_props = [
            'user' => [
                'id' => (int) $this->user->id,
                'fullname' => $this->user->fullname,
                'profileimageurl' => $this->get_user_picture_url(),
            ],
            'current-user-id' => (int) $this->currently_logged_in_user()->id,
            'return-url' => $this->get_return_url()->out(),
        ];

        if ($this->role) {
            $vue_props['specified-role'] = $this->role::get_name();
        } else if ($this->user->is_logged_in()) {
            $vue_props['specified-role'] = self_role::get_name();
        }

        if ($assignment_id = $this->get_param('assignment_id', PARAM_INT)) {
            $vue_props['assignment-id'] = $assignment_id;
        }

        $view = tui_view::create('pathway_manual/pages/RateCompetencies', $vue_props);
        $this->apply_page_navigation($view);
        return $view;
    }

    /**
     * Get all the roles available.
     *
     * @return role[]
     */
    protected function get_roles(): array {
        return roles::get_current_user_roles($this->user->id);
    }

    /**
     * Throw an exception because the current user doesn't have the required roles to view this page.
     *
     * @throws \moodle_exception
     */
    protected function user_lacks_role() {
        // The user has no roles, so requiring they have the self/manager role will print an error message.
        if ($this->user->is_logged_in()) {
            self_role::require_for_user($this->user->id);
        } else {
            manager::require_for_user($this->user->id);
        }
    }

    /**
     * Get user profile picture URL
     */
    private function get_user_picture_url(): string {
        $user_picture = new \user_picture((object) $this->user->to_array());
        $user_picture->size = 1; // Size f1.
        return $user_picture->get_url($this->page);
    }

    /**
     * Get the URL to return to when leaving the page.
     *
     * @return moodle_url
     */
    private function get_return_url(): moodle_url {
        $url = capability_helper::can_view_profile($this->user->id, $this->context)
            ? '/totara/competency/profile/'
            : '/totara/competency/rate_users.php';

        return new moodle_url($url, ['user_id' => $this->user->id]);
    }

    /**
     * Add page navigation information (page title, url) to the navbar and to the page view.
     *
     * @param view $view
     */
    private function apply_page_navigation(view $view) {
        $parent_page_url = new \moodle_url('/totara/competency/rate_users.php');
        $page_url = new \moodle_url('/totara/competency/rate_competencies.php', ['user_id' => $this->user->id]);

        $parent_page_title = get_string('rate_competencies', 'pathway_manual');

        if ($this->user->is_logged_in()) {
            $page_title = $parent_page_title;
        } else {
            $page_title = get_string('rate_user', 'pathway_manual', $this->user->fullname);
            $this->page->navbar->add($parent_page_title, $parent_page_url);
        }

        $this->page->navbar->add($page_title);

        $view->set_title($page_title)->set_url($page_url);
    }

}
