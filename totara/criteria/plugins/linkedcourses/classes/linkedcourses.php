<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @package totara_criteria
 */

namespace criteria_linkedcourses;

use coding_exception;
use totara_competency\linked_courses;
use totara_criteria\criterion;
use totara_criteria\evaluators\course_item_evaluator;

global $CFG;

require_once($CFG->dirroot . '/totara/plan/lib.php');

/**
 * Class containing information of linked courses criteria
 */
class linkedcourses extends criterion {

    /**
     * Get the type of items stored in this criterion
     */
    public function get_items_type() {
        return 'course';
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
        return linkedcourses_display::class;
    }

    /*******************************************************************************************************
     * Retrieve and Save
     *******************************************************************************************************/

    /**
     * Validate the criterion attributes
     * linkedcourse criteria should contain the competency metadata
     * @return string|null Error description
     */
    protected function validate(): ?string {
        return !($this->get_competency_id() > 0) ? 'Competency id metadata is required in linkedcourses criteria' : null;
    }

    /**
     * Update derived items
     * An item is added for each currently linked course - This is in anticipation of merging of TL-22455
     */
    public function update_items(): criterion {
        $comp_id = $this->get_competency_id();
        if (is_null($comp_id)) {
            throw new coding_exception('Competency id must be set before items are updated');
        }

        $linked_courses = linked_courses::get_linked_course_ids($comp_id);
        $this->set_item_ids($linked_courses);

        return $this;
    }


    /************************************************************************************
     * Evaluation
     ************************************************************************************/

    public static function item_evaluator(): string {
        return course_item_evaluator::class;
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
        return 'criteria_linkedcourses/linkedcourses_edit';
    }

    /**
     * Export the view template name and data
     *
     * @return string
     */
    public function get_view_template(): string {
        return 'criteria_linkedcourses/linkedcourses';
    }

    // TODO:
    // When exporting for the user profile page, we will
    // retrieve the currently linked courses and export them
    // as items in the same way as coursecompletion is doing it

    /**
     * Export detail for viewing this criterion
     *
     * @return array
     */
    public function export_view_detail(): array {
        return [
            'title' => get_string('linkedcourses', 'criteria_linkedcourses'),
            'aggregation' => $this->export_view_aggregation(),
        ];
    }

}
