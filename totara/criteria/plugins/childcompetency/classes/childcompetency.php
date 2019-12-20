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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package criteria_childcompetency
 */

namespace criteria_childcompetency;

use coding_exception;
use totara_criteria\criterion;
use totara_criteria\evaluators\competency_item_evaluator;
use totara_criteria\validators\competency_item_validator;

/**
 * Class containing information of child competency criteria
 */
class childcompetency extends criterion {

    /**
     * Get the type of items stored in this criterion
     */
    public function get_items_type() {
        return 'competency';
    }

    /**
     * Does this criterion have associated items
     *
     * @return bool
     */
    public function has_items(): bool {
        return false;
    }

    /**
     * Return the display class for this criterion
     *
     * @return string Class to use for displaying
     */
    protected function get_display_class(): string {
        return childcompetency_display::class;
    }

    /*******************************************************************************************************
     * Retrieve and Save
     *******************************************************************************************************/

    /**
     * Validate the criterion attributes
     * A childcompetency should contain the competency metadata
     * @return string|null Error description
     */
    protected function validate_attributes(): ?string {
        $comp_id = $this->get_competency_id();
        return is_null($comp_id) ? 'Competency id metadata is required in childcompetency criteria' : null;
    }

    /**
     * Update derived items
     * An item is added for each child competency
     */
    public function update_items(): criterion {
        global $DB;

        $comp_id = $this->get_competency_id();
        if (is_null($comp_id)) {
            throw new coding_exception('Competency id must be set before items are updated');
        }

        $child_competencies = $DB->get_fieldset_select('comp', 'id', 'parentid = :parentid', ['parentid' => $comp_id]);
        $this->set_item_ids($child_competencies);

        return $this;
    }

    /**
     * @return string|null Class name of item_validator for this criteria type.
     */
    public static function get_item_validator_class(): ?string {
        return competency_item_validator::class;
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
     * Export the edit template name and data
     *
     * @return string
     */
    public function get_edit_template(): string {
        return 'criteria_childcompetency/childcompetency_edit';
    }

    /**
     * Export the view template name and data
     *
     * @return string
     */
    public function get_view_template(): string {
        return 'criteria_childcompetency/childcompetency';
    }

    // TODO:
    // When exporting for the user profile page, we will
    // retrieve the child competencies and export them as items

    /**
     * Export detail for viewing this criterion
     *
     * @return array
     */
    public function export_view_detail(): array {
        $result = [
            'title' => get_string('pluginname', 'criteria_childcompetency'),
            'aggregation' => $this->export_view_aggregation(),
        ];

        return $result;
    }

}
