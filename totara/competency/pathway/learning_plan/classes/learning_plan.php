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
use totara_core\advanced_feature;

class learning_plan extends pathway {

    public const CLASSIFICATION = self::PATHWAY_MULTI_VALUE;

    /**
     * @inheritDoc
     */
    protected function fetch_configuration(): void {
        // Do nothing.
    }

    /**
     * @inheritDoc
     */
    protected function save_configuration() {
        // Do nothing.
    }

    /**
     * @inheritDoc
     */
    protected function configuration_is_dirty(): bool {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function delete_configuration(): void {
        // Do nothing.
    }

    /**
     * @return bool
     */
    public function is_validated(): bool {
        // We need to check the system status everytime
        return false;
    }

    /**
     * Validate the configuration
     * @return bool
     */
    protected function is_configuration_valid(): bool {
        return advanced_feature::is_enabled('learningplans');
    }


    /**
     * Get the current value from the learning plan and aggregate the pathway with it
     *
     * @param int $user_id
     * @return base_achievement_detail
     */
    public function aggregate_current_value(int $user_id): base_achievement_detail {
        global $DB;

        // TODO: At the moment if a user's learning plan is deleted after he received a rating for the competency
        //       the rating is not deleted from dp_plan_competency_value.
        //       Waiting for Raven to confirm what is expected behaviour.
        //       May need to also join with dp_plan and dp_plan_cmpetency_assign
        $scale_value_id = $DB->get_field(
            'dp_plan_competency_value',
            'scale_value_id',
            ['competency_id' => $this->get_competency()->id, 'user_id' => $user_id]
        );

        $achievement_detail = new achievement_detail();
        if ($scale_value_id) {
            $achievement_detail->set_scale_value_id($scale_value_id);
        }

        return $achievement_detail;
    }

    /**
     * @inheritDoc
     */
    public function get_edit_template(): string {
        return 'pathway_learning_plan/edit';
    }

    /**
     * @inheritDoc
     */
    public function get_view_template(): string {
        return 'pathway_learning_plan/view';
    }

    /**
     * @inheritDoc
     */
    public static function get_label(): string {
        return get_string('achievementpath_group_label', 'pathway_learning_plan');
    }

}
