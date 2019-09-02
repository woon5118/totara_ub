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

use totara_criteria\competency_item_evaluator;
use totara_criteria\criterion;

/**
 * Class containing information of child competency criteria
 */
class childcompetency extends criterion {

    /**
     * Get the type of items stored in this criterion
     */
    protected function get_items_type() {
        return '';
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
        return childcompetency_display::class;
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
     * @return Array
     */
    public function export_view_detail(): array {
        $result = [
            'title' => get_string('pluginname', 'criteria_childcompetency'),
            'aggregation' => $this->export_view_aggregation(),
        ];

        return $result;
    }

}
