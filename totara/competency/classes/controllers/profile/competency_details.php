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

use totara_competency\entities\competency;
use totara_mvc\tui_view;

class competency_details extends base {

    public function action() {
        global $OUTPUT;

        // TODO Fix it so that tui_component is not requiring this
        $renderer = $this->page->get_renderer('core');

        // Add breadcrumbs.
        $this->add_navigation(get_string('competencydetails', 'totara_hierarchy'));

        $competency = competency::repository()->find_or_fail((int) $this->get_param('competency_id', PARAM_INT, null, true));

        $props = [
            'user-id' => $this->user->id,
            'competency-id' => $competency->id,
            'base-url' => (string) $this->get_base_url(),
            'go-back-link' => (string)$this->get_profile_url(),
        ];

        return tui_view::create('totara_competency/pages/CompetencyDetail', $props)
            ->set_title(get_string('competencydetails', 'totara_hierarchy'));
    }
}