<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 * @author Sam Hemelryk <sam.hemelryk@totaralms.com>
 * @package flavour_professional
 */

namespace flavour_professional;

defined('MOODLE_INTERNAL') || die();

/**
 * Lite flavour definition.
 *
 * The professional flavour is Totara with cut back features, closer to what you get with Moodle.
 *
 * @author Sam Hemelryk <sam.hemelryk@totaralms.com>
 * @package flavour_professional
 */
class definition extends \totara_flavour\definition {

    /**
     * Returns the component name.
     * @return string
     */
    public function get_component() {
        return 'flavour_professional';
    }

    /**
     * Returns an array of enforced settings.
     * @return array
     */
    protected function load_enforced_settings() {
        return array(
            '' => array(
                'enableappraisals' => TOTARA_DISABLEFEATURE,
                'enablecertifications' => TOTARA_DISABLEFEATURE,
                'enablecompetencies' => TOTARA_DISABLEFEATURE,
                'enablefeedback360' => TOTARA_DISABLEFEATURE,
                'enablegoals' => TOTARA_DISABLEFEATURE,
                'enablelearningplans' => TOTARA_DISABLEFEATURE,
                'enablepositions' => TOTARA_DISABLEFEATURE,
                'enableprograms' => TOTARA_DISABLEFEATURE,
                'enablerecordoflearning' => TOTARA_DISABLEFEATURE,
                'enableglobalrestrictions' => 0,
                'enablereportgraphs' => TOTARA_DISABLEFEATURE,
                'enabletotaradashboard' => TOTARA_DISABLEFEATURE,
                'enablemyteam' => TOTARA_DISABLEFEATURE,
            ),
            'theme_customtotararesponsive' => array(
                'customcss' => '',
            ),
        );
    }
}
