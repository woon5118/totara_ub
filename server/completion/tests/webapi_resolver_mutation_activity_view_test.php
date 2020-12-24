<?php
/*
* This file is part of Totara Learn
*
* Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
* @author Oleg Demeshev <oleg.demeshev@totaralearning.com>
* @package core_completion
*/

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot . '/completion/criteria/completion_criteria.php');
require_once($CFG->dirroot . '/completion/criteria/completion_criteria_self.php');

use totara_webapi\phpunit\webapi_phpunit_helper;

class webapi_resolver_mutation_activity_view_testcase extends advanced_testcase {

    private const MUTATION = 'core_completion_activity_view';

    use webapi_phpunit_helper;

    protected function setUp(): void {
        global $CFG;
        parent::setup();

        $CFG->enablecompletion = true;
    }

    private function get_execution_context(string $type = 'dev', ?string $operation = null) {
        return \core\webapi\execution_context::create($type, $operation);
    }

    private function create_completion_activity($activity) {

        $coursedefaults = array(
            'enablecompletion' => COMPLETION_ENABLED,
            'completionstartonenrol' => 1,
            'completionprogressonview' => 1,
        );

        $course = $this->getDataGenerator()->create_course($coursedefaults, array('createsections' => true));

        // Enrol user if present.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $module = $this->getDataGenerator()->create_module(
            $activity,
            ['course' => $course->id],
            ['completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => COMPLETION_VIEW_REQUIRED]
        );
        return [$module, $course];
    }

    /**
     * Supported modules
     * Provider for test_resolve_activity_view
     * @return array
     */
    public function module_provider() {
        return [
            ['certificate'], ['chat'], ['choice'], ['data'], ['facetoface'], ['feedback'], ['folder'], ['forum'], ['imscp'],
            ['lesson'], ['lti'], ['page'], ['quiz'], ['resource'], ['scorm'], ['url'], ['wiki']
        ];
    }

    /**
     * @dataProvider module_provider
     * @throws coding_exception
     */
    public function test_resolve_activity_view($activity) {
        [$module, $course] = $this->create_completion_activity($activity);
        try {
            $args = ['cmid' => $module->cmid, 'activity' => $activity];
            $result = $this->resolve_graphql_mutation(self::MUTATION, $args);
            self::assertTrue($result);
        } catch (\moodle_exception $ex) {
            self::fail($ex->getMessage());
        }

        // Check completion status.
        $cm = get_coursemodule_from_instance($activity, $module->id);
        $completion = new \completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->viewed);
    }

    public function test_resolve_activity_view_no_support() {
        $activity = 'book';
        [$module, $course] = $this->create_completion_activity($activity);
        try {
            $args = ['cmid' => $module->cmid, 'activity' => $activity];
            $result = $this->resolve_graphql_mutation(self::MUTATION, $args);
            self::assertFalse($result);
        } catch (\moodle_exception $ex) {
            self::fail("This '{$activity}' module does not support course_module_viewed event.");
        }
    }
}