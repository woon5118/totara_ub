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
 * @author Brendan Cox <brendan.cox@totaralearning.com>
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_pathway
 */

namespace pathway_learning_plan;


use totara_competency\base_achievement_detail;
use totara_competency\pathway;

class learning_plan extends pathway {

    public const CLASSIFICATION = self::PATHWAY_MULTI_VALUE;

    protected function fetch_configuration() {
        // Do nothing.
    }

    protected function save_configuration() {
        // Do nothing.
    }

    protected function configuration_is_dirty(): bool {
        return false;
    }

    protected function delete_configuration() {
        // Do nothing.
    }

    public function aggregate_current_value(int $user_id): base_achievement_detail {
        global $DB;

        $record = $DB->get_record(
            'dp_plan_competency_value',
            ['competency_id' => $this->get_competency()->id, 'user_id' => $user_id]
        );

        $achievement_detail = new achievement_detail();

        if ($record !== false) {
            $achievement_detail->set_scale_value_id($record->scale_value_id);
        }

        return $achievement_detail;
    }

    public function get_edit_template(): string {
        return 'pathway_learning_plan/edit';
    }

    public function get_view_template(): string {
        return 'pathway_learning_plan/view';
    }
}