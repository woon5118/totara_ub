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


global $CFG;

use totara_competency\external;

require_once($CFG->dirroot . '/lib/externallib.php');

/**
 * @group totara_competency
 */
class pathway_competency_services_testcase extends advanced_testcase {

    private function setup_data() {
        $data = new class() {
            public $comp;
            public $courses = [];
            public $course_ids = [];
        };

        $this->setAdminUser();
        $GLOBALS['USER']->ignoresesskey = true;

        $hierarchy_generator = $this->getDataGenerator()->get_plugin_generator('totara_hierarchy');
        $compfw = $hierarchy_generator->create_comp_frame([]);
        $data->comp = $hierarchy_generator->create_comp(['frameworkid' => $compfw->id]);

        $ids = [];
        for ($i = 0; $i < 5; $i++) {
            $data->courses[$i] = $this->getDataGenerator()->create_course();
            $data->course_ids[] = $data->courses[$i]->id;
        }

        return $data;
    }

    public function test_totara_competency_link_default_preset() {
        $data = $this->setup_data();

        $res = \external_api::call_external_function(
            'totara_competency_link_default_preset',
            ['competency_id' => $data->comp->id]
        );

        $result = $res['data'] ?? null;
        $error = $res['error'] ?? null;

        $this->assertEquals(false, $error);
        $this->assertEquals(1, $result);
    }

    public function test_totara_competency_get_courses() {
        $data = $this->setup_data();


        $res = \external_api::call_external_function(
            'totara_competency_get_courses',
            [
                'filters' => [
                    'ids' => $data->course_ids,
                ],
                'page' => 0,
                'order' => 'fullname',
                'direction' => 'asc',
            ]
        );

        $result = $res['data'] ?? null;
        $error = $res['error'] ?? null;

        $this->assertEquals(false, $error);
        // $this->assertEquals(1, $result);
    }

    public function test_totara_competency_get_frameworks() {
        /** @var totara_competency_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('totara_competency');

        $frameworks = [
            $generator->create_framework(null, '<span>Framework 1</span>'),
            $generator->create_framework(null, '<span>Framework 2</span>'),
            $generator->create_framework(null, '<span>Framework 3</span>'),
        ];

        $returned_frameworks = external::get_frameworks();

        $expected_data = [];
        foreach ($frameworks as $framework) {
            $expected_data[] = [
                'id'       => $framework->id,
                'fullname' => format_string($framework->fullname),
            ];
        }

        $this->assertEqualsCanonicalizing($expected_data, $returned_frameworks['items']);
    }

}
