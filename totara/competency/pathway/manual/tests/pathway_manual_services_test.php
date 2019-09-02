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

use pathway_manual\external;
use pathway_manual\manual;

global $CFG;
require_once($CFG->dirroot . '/lib/externallib.php');

/**
 * @group totara_competency
 */
class pathway_manual_services_testcase extends advanced_testcase {

    public function test_get_roles_no_filter() {

        $return = external::get_roles([], 1, '', 'asc');
        $result = external::clean_returnvalue(external::get_roles_returns(), $return);

        $this->assertEquals($return, $result);

        $expected = [
            'page' => 1,
            'pages' => 1,
            'items_per_page' => 3,
            'total' => 3,
            'items' => [
                [
                    'id' => 1,
                    'role' => manual::ROLE_MANAGER,
                    'name' => ucfirst(manual::ROLE_MANAGER),
                ],
                [
                    'id' => 2,
                    'role' => manual::ROLE_APPRAISER,
                    'name' => ucfirst(manual::ROLE_APPRAISER),
                ],
                [
                    'id' => 3,
                    'role' => manual::ROLE_SELF,
                    'name' => ucfirst(manual::ROLE_SELF),
                ],
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    public function test_get_roles_ids_filter() {

        $return = external::get_roles(['ids' => [1, 3]], 1, '', 'asc');
        $result = external::clean_returnvalue(external::get_roles_returns(), $return);

        $this->assertEquals($return, $result);

        $expected = [
            'page' => 1,
            'pages' => 1,
            'items_per_page' => 2,
            'total' => 2,
            'items' => [
                [
                    'id' => 1,
                    'role' => manual::ROLE_MANAGER,
                    'name' => ucfirst(manual::ROLE_MANAGER),
                ],
                [
                    'id' => 3,
                    'role' => manual::ROLE_SELF,
                    'name' => ucfirst(manual::ROLE_SELF),
                ],
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_get_roles_name_filter() {

        $return = external::get_roles(['name' => 'manage'], 1, '', 'asc');
        $result = external::clean_returnvalue(external::get_roles_returns(), $return);

        $this->assertEquals($return, $result);

        $expected = [
            'page' => 1,
            'pages' => 1,
            'items_per_page' => 1,
            'total' => 1,
            'items' => [
                [
                    'id' => 1,
                    'role' => manual::ROLE_MANAGER,
                    'name' => ucfirst(manual::ROLE_MANAGER),
                ],
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    // TODO: Test pw filter
}
