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

use totara_competency\linked_courses;
use totara_criteria\course_item_evaluator;
use totara_criteria\criterion;

require_once($CFG->dirroot . '/totara/plan/lib.php');

/**
 * Class containing information of linked courses criteria
 */
class linkedcourses extends criterion {

    /** Link type consts */
    const LINKTYPE_OPTIONAL = linked_courses::LINKTYPE_OPTIONAL;
    const LINKTYPE_MANDATORY = linked_courses::LINKTYPE_MANDATORY;
    const LINKTYPE_ALL = 2;

    const METADATA_LINKTYPE_KEY = 'linkedtype';

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
     * Return the display class for this criterion
     *
     * @return string Class to use for displaying
     */
    protected function get_display_class(): string {
        return linkedcourses_display::class;
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
     * @return Array
     */
    public function export_view_detail(): array {
        $result = [
            'title' => get_string('linkedcourses', 'criteria_linkedcourses'),
            'items' => ['name' => $this->export_view_linkedtype()],
            'aggregation' => $this->export_view_aggregation(),
        ];

        return $result;
    }


    /**
     * Export linkedtype
     *
     * @return string Linked type
     */
    private function export_view_linkedtype(): string {
        return $this->get_summarized_linkedtyped();
    }

    /**
     * Get a summarized linked type
     *
     * @return string
     */
     private function get_summarized_linkedtype(): string {
        $str_mandatory = ucfirst(get_string('mandatory', 'totara_competency'));
        $str_all = ucfirst(get_string('all', 'totara_competency'));

        foreach ($this->get_metadata() as $metakey => $metaval) {
            if ($metakey == static::METADATA_LINKTYPE_KEY) {
                return $metaval == static::LINKTYPE_MANDATORY ? $str_mandatory : $str_all;
            }
        }

        return '';
    }

}
