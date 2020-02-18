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
use totara_competency\helpers\capability_helper;
use totara_mvc\tui_view;

class user_assignment extends base {

    public function action() {
        capability_helper::require_can_assign($this->user->id, $this->context);

        $this->add_navigation($this->get_page_name());

        $props = [
            'user-id' => $this->user->id,
            'base-page-heading' => $this->get_base_page_heading(),
            'go-back-link' => (string) $this->get_profile_url(),
            'go-back-text' => $this->get_back_to_profile_text(),
            'frameworks' => $this->get_frameworks(),
            'types' => $this->get_types(),
        ];

        return tui_view::create('totara_competency/pages/UserAssignment', $props)
            ->set_title(get_string('assign_competencies', 'totara_competency'));
    }

    protected function get_frameworks(): array {
        $frameworks = competency_framework::repository()
            ->filter_by_visible()
            ->order_by('sortorder', 'asc')
            ->get();

        $result = [];
        /** @var competency_framework $framework */
        foreach ($frameworks as $framework) {
            $result[] = [
                'id' => $framework->id,
                'name' => format_string($framework->fullname)
            ];
        }
        return $result;
    }

    protected function get_types(): array {
        $competency_types = competency_type::repository()->get();

        $result = [];
        /** @var competency_framework $framework */
        foreach ($competency_types as $type) {
            $result[] = [
                'id' => $type->id,
                'name' => format_string($type->fullname)
            ];
        }
        return $result;
    }

    private function get_page_name(): string {
        if ($this->is_for_current_user()) {
            return get_string('user_assignment_page_title:self', 'totara_competency');
        }

        return get_string('user_assignment_page_title:other', 'totara_competency');
    }

    private function get_base_page_heading(): string {
        if ($this->is_for_current_user()) {
            return get_string('user_assignment_page_heading:self', 'totara_competency');
        }

        return get_string('user_assignment_page_heading:other', 'totara_competency');
    }

}