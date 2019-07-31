<?php
/**
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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package engage_survey
 */
defined('MOODLE_INTERNAL') || die();

use engage_survey\totara_engage\resource\survey;
use totara_engage\answer\answer_type;
use totara_engage\exception\resource_exception;

class engage_survey_delete_testcase extends advanced_testcase {
    /**
     * @return void
     */
    public function test_delete_survey(): void {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $data = [
            'questions' => [
                [
                    'value' => 'ubuntu bolobala ?',
                    'answertype' => answer_type::MULTI_CHOICE,
                    'options' => [
                        '12', '13', '14', '15', '16'
                    ]
                ]
            ]
        ];

        $component = survey::get_resource_type();

        /** @var survey $resource */
        $resource = survey::create($data);

        $sql = '
            SELECT 1 FROM "ttr_engage_survey" ps
            INNER JOIN "ttr_engage_resource" er ON er.instanceid = ps.id AND er.resourcetype = :component
            WHERE ps.id = :surveyid
        ';

        $params = [
            'surveyid' => $resource->get_instanceid(),
            'component' => $component
        ];

        $this->assertTrue($DB->record_exists_sql($sql, $params));

        $x = [$resource->get_instanceid()];
        $questionsql = 'SELECT 1 FROM "ttr_engage_survey_question" WHERE surveyid = ?';
        $this->assertTrue($DB->record_exists_sql($questionsql, $x));

        $resource->delete();
        $this->assertFalse($DB->record_exists_sql($sql, $params));
        $this->assertFalse($DB->record_exists_sql($questionsql, $x));
    }

    /**
     * @return void
     */
    public function test_delete_resource_without_permissions(): void {
        $gen = $this->getDataGenerator();

        $user = $gen->create_user();
        $this->setUser($user);

        $data = [
            'questions' => [
                [
                    'value' => 'Hello world ???',
                    'answertype' => answer_type::SINGLE_CHOICE,
                    'options' => ['bolobala', 'balabolo']
                ]
            ]
        ];

        $resource = survey::create($data);
        $context = $resource->get_context();

        $user2 = $gen->create_user();
        $this->setUser($user2);

        $roles = get_archetype_roles('user');
        foreach ($roles as $role) {
            role_assign($role->id, $user2->id, $context->id);
            assign_capability('engage/survey:delete', CAP_PREVENT, $role->id, $context->id);
        }

        try {
            $resource->delete($user2->id);
        } catch (resource_exception $e) {
            $this->assertEquals(get_string('error:delete', 'engage_survey'), $e->getMessage());
            return;
        }

        $this->fail("No exception had been captured");
    }
}