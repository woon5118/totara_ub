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
 * @package criteria_linkedcourses;
 */

namespace criteria_linkedcourses;

use totara_criteria\criterion_display;

/**
 * Display class for linkedcourses criteria
 */

class linkedcourses_display extends criterion_display {

    /**
     * Return the display type of items associated with the criterion
     * TODO: make protected when all UI is on vueJs
     *
     * @return string
     */
    public function get_display_items_type(): string {
        return get_string('linkedcourses', 'criteria_linkedcourses');
    }

    /**
     * Return a summarized view of the criterion items for display
     * For linkedcourses the link type is return as the item
     *
     * @return string[]
     */
    protected function get_display_configuration_items(): array {
        $str_mandatory = ucfirst(get_string('mandatory', 'totara_competency'));
        $str_all = ucfirst(get_string('all', 'totara_competency'));

        $linkedtype = '';
        foreach ($this->criterion->get_metadata() as $metakey => $metaval) {
            if ($metakey == linkedcourses::METADATA_LINKTYPE_KEY) {
                $linkedtype = ($metaval == linkedcourses::LINKTYPE_MANDATORY ? $str_mandatory : $str_all);
                break;
            }
        }

        return empty($linkedtype) ? [] : [$linkedtype];
    }

}
