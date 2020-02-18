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
 * @author Marco Song <marco.song@totaralearning.com>
 * @package criteria_othercompetency
 */

namespace criteria_othercompetency;

use totara_competency\entities\competency as competency_entity;
use totara_criteria\criterion;
use totara_criteria\evaluators\competency_item_evaluator;

/**
 * Class containing information of other competencies criteria instances
 */
class othercompetency extends criterion {

    /**
     * Get the type of items stored in this criterion
     * V1 - Assuming that a criterion can only store items of a single type
     */
    public function get_items_type() {
        return 'competency';
    }

    /**
     * Does this criterion have associated metadata
     *
     * @return bool
     */
    public function has_metadata(): bool {
        return false;
    }

    /**
     * Return the display class for this criterion
     *
     * @return string Class to use for displaying
     */
    protected function get_display_class(): string {
        return othercompetency_display::class;
    }


    /*******************************************************************************************************
     * Retrieve and Save
     *******************************************************************************************************/

    /**
     * Update derived items
     */
    public function update_items(): criterion {
        // Other Competency has no derived items
        return $this;
    }


    /************************************************************************************
     * Evaluation
     ************************************************************************************/


    public static function item_evaluator(): string {
        return competency_item_evaluator::class;
    }

    /************************************************************************************
     * Data exporting
     * TODO - remove once all APIs have been replaced by GraphQL
     ************************************************************************************/

    /**
     * Return the name of the template for defining this criterion
     *
     * @return string Edit template name
     */
    public function get_edit_template(): string {
        return 'criteria_othercompetency/othercompetency_edit';
    }

    /**
     * Return the name of the template to view this criterion
     *
     * @return string Summary template's name
     */
    public function get_view_template(): string {
        return '';
    }

    /**
     * Export definition item data
     *
     * @return  array Array of item detail
     */
    public function export_edit_items(): array {
        $items = [];

        foreach ($this->get_item_ids() as $competency_id) {
            $competency = new competency_entity($competency_id);
            $items[] = [
                'type' => $this->get_items_type(),
                'id' => $competency_id,
                'name' => format_string($competency->fullname),
            ];
        }

        return $items;
    }

}
