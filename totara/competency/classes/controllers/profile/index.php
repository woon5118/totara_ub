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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package totara_competency
 */

namespace totara_competency\controllers\profile;

use totara_mvc\tui_view;
use user_picture;


class index extends base {

    public function action() {

        // Add breadcrumbs.
        $this->add_navigation();

        $props = [
            'profile-picture' => $this->get_my_profile_picture_url(),
            'self-assignment-url' => (string) $this->get_self_assignment_url(),
            'user-id' => $this->user->id,
            'user-name' => $this->user->fullname,
            'is-mine' => $this->is_for_current_user(),
            'base-url' => (string) $this->get_base_url(),
            'can-assign' => $this->can_assign()
        ];

        return tui_view::create('totara_competency/pages/CompetencyProfile', $props)
            ->set_title(get_string('competency_profile', 'totara_competency'));
    }

    protected function get_my_profile_picture_url(int $size = 100): string {
        $avatar = new user_picture((object)($this->user->to_array()));
        $avatar->size = $size;
        return $avatar->get_url($this->page);
    }

    protected function can_assign(): bool {
        $capability = $this->is_for_current_user() ? 'tassign/competency:assignself' : 'tassign/competency:assignother';
        return has_capability($capability, $this->context);
    }
}