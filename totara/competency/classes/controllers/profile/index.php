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

use totara_mvc\view;
use user_picture;


class index extends base {

    public function action() {

        global $OUTPUT;

        // Add breadcrumbs.
        $this->add_navigation();

        $title = get_string('competency_profile', 'totara_competency', $this->user->firstname . ' ' . $this->user->lastname);

        // TODO Fix it so that tui_component is not requiring this
        $renderer = $this->page->get_renderer('core');

        $props = [
            'profile-picture' => $this->get_my_profile_picture_url(),
            'self-assignment-url' => (string) $this->get_self_assignment_url(),
            'user-id' => $this->user->id,
            'user-name' => $this->user->fullname,
            'is-mine' => $this->is_for_current_user(),
            'base-url' => (string) $this->get_base_url(),
            'can-assign' => $this->can_assign()
        ];

        $data = [
            'title' => $title,
            'competency_profile' => $OUTPUT->tui_component('totara_competency/pages/CompetencyProfile', $props),
        ];

        return view::create('totara_competency/profile_index', $data)
            ->set_title($title);
    }

    protected function get_my_profile_picture_url(int $size = 100): string {
        $avatar = new user_picture((object)($this->user->to_array()));
        $avatar->size = $size;
        return $avatar->get_url($this->page);
    }

    protected function can_assign(): bool {
        if ($this->is_for_current_user()) {
            return has_capability('tassign/competency:assignself', \context_system::instance());
        } else {
            return has_capability('tassign/competency:assignother', $this->context);
        }
    }
}