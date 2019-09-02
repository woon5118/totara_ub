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
 */

use totara_competency\plugintypes;

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

/**
 * @group totara_criteria
 */
class totara_criteria_services_testcase extends advanced_testcase {

    private function setup_data() {
        $data = new class() {
        };

        $this->setAdminUser();
        $GLOBALS['USER']->ignoresesskey = true;

        return $data;
    }

    public function test_totara_criteria_get_definition_template_service() {
        $data = $this->setup_data();

        // Test foreach enabled criterion type
        $enabledtypes = plugintypes::get_enabled_plugins('criteria', 'totara_criteria');
        foreach ($enabledtypes as $type) {
            $res = \external_api::call_external_function(
                'totara_criteria_get_definition_template',
                ['type' => $type]
            );

            $result = $res['data'] ?? null;
            $error = $res['error'] ?? null;

            $this->assertEquals(false, $error);
            $this->assertTrue(is_array($result));
        }
    }

}
