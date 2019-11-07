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

use totara_competency\entities\competency_framework;
use totara_competency\entities\competency_type;
use totara_mvc\tui_view;

class self_assignment extends base {

    public function action() {

        $capability = $this->is_for_current_user() ? 'totara/competency:assign_self' : 'totara/competency:assign_other';
        $this->require_capability($capability, $this->context);

        // Add breadcrumbs.
        $this->add_navigation('Self assignment');

        $props = [
            'user-id' => $this->user->id,
            'go-back-link' => (string)$this->get_profile_url(),
            'frameworks' => $this->get_frameworks(),
            'types' => $this->get_types()
        ];

        return tui_view::create('totara_competency/pages/SelfAssignment', $props)
            ->set_title(get_string('assign_competencies', 'totara_competency'));
    }

    protected function get_frameworks() {
        $frameworks = competency_framework::repository()
            ->filter_by_visible()
            ->order_by('sortorder', 'asc')
            ->get();

        $result = [];
        /** @var competency_framework $framework */
        foreach ($frameworks as $framework) {
            $result[] = [
                'id' => $framework->id,
                'name' => $framework->fullname
            ];
        }
        return $result;
    }

    protected function get_types() {
        $competency_types = competency_type::repository()->get();

        $result = [];
        /** @var competency_framework $framework */
        foreach ($competency_types as $type) {
            $result[] = [
                'id' => $type->id,
                'name' => $type->fullname
            ];
        }
        return $result;
    }
}