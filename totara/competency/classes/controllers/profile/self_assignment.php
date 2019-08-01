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

class self_assignment extends base {

    public function action() {
        global $OUTPUT;

        if ($this->is_for_current_user()) {
            $this->require_capability('tassign/competency:assignself', \context_system::instance());
        } else {
            $this->require_capability('tassign/competency:assignother', $this->context);
        }

        // Add breadcrumbs.
        $this->add_navigation('Self assignment');

        $props = [
            'user-id' => $this->user->id,
            'go-back-link' => (string)$this->get_profile_url(),
        ];

        $data = [
            'self_assignment' => $OUTPUT->tui_component('totara_competency/views/SelfAssignment', $props)
        ];

        return view::create('totara_competency/profile_self_assignment', $data)
            ->set_title(get_string('assign_competencies', 'totara_competency'));
    }
}