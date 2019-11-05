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

use pathway_manual\manual;
use totara_competency\controllers\profile\base;
use totara_mvc\tui_view;

class rate_competencies extends base {

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
            'role' => $this->get_role_from_params(),
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

        $role = $this->get_role_from_params();

        if ($role == manual::ROLE_SELF) {
            require_capability('totara/competency:rate_own_competencies', $this->context);
        } else {
            require_capability('totara/competency:rate_other_competencies', $this->context);
        }

        if (!manual::user_has_role($this->user->id, $this->currently_logged_in_user()->id, $role)) {
            print_error('error:user_lacks_role', 'pathway_manual', $this->get_profile_url(), [
                'user' => $this->user->fullname,
                'role' => strtolower(get_string('role_' . $role, 'pathway_manual')),
            ]);
        }

        return $this;
    }

    /**
     * Get the role that the user wishes to view competencies of.
     *
     * @return string The role e.g. manual::ROLE_SELF, manual::ROLE_MANAGER
     */
    private function get_role_from_params(): string {
        if ($this->user->is_logged_in()) {
            // If the user is rating themselves, then it is always ROLE_SELF no matter what.
            return manual::ROLE_SELF;
        }

        // TODO: Change role default parameter in TL-22011 or TL-23002 to support appraisers
        return optional_param('role', manual::ROLE_MANAGER, PARAM_ALPHA);
    }

}
