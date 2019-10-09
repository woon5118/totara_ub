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
 * @package totara_competency
 */

namespace aggregation_test_aggregation;

use totara_competency\pathway_aggregation;

defined('MOODLE_INTERNAL') || die();

/**
 * To use this in tests, call totara_competency_generator::create_test_aggregation() instead of instantiating it directly.
 */
class test_aggregation extends pathway_aggregation {

    private $test_achieved_value_ids = [];
    private $test_achieved_vias = [];

    /**
     * @param $achieved_values
     * @param $achieved_vias
     * @return test_aggregation
     */
    public function set_test_aggregated_data($achieved_values, $achieved_vias): self {
        $this->test_achieved_value_ids = $achieved_values;
        $this->test_achieved_vias = $achieved_vias;
        return $this;
    }

    /**
     * Test aggregation.
     */
    protected function do_aggregation() {
        foreach ($this->test_achieved_value_ids as $userid => $achieved_value_id) {
            $this->set_achieved_value_id($userid, $achieved_value_id);
        }
        foreach ($this->test_achieved_vias as $userid => $achieved_via) {
            $this->set_achieved_via($userid, $achieved_via);
        }
    }

}
