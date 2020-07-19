<?php
/**
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use core\format;
use core\webapi\execution_context;

use mod_perform\user_groups\grouping;

use totara_job\job_assignment;
use totara_webapi\phpunit\webapi_phpunit_helper;


/**
 * @coversDefaultClass user_grouping.
 *
 * @group perform
 *
 * TODO: this should be combined with totara_competency/user_groups and put into
 * totara core somewhere.
 */
class mod_perform_webapi_type_user_grouping_testcase extends advanced_testcase {

    use webapi_phpunit_helper;

    private const TYPE = 'mod_perform_user_grouping';

    /**
     * @covers ::resolve
     */
    public function test_invalid_input(): void {
        $this->create_grouping();

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageMatches("/user_grouping/");

        $this->resolve_graphql_type(self::TYPE, 'id', new \stdClass());
    }

    /**
     * @covers ::resolve
     */
    public function test_invalid_field(): void {
        [$grouping, $context] = $this->create_grouping();
        $field = 'unknown';

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageMatches("/$field/");

        $this->resolve_graphql_type(self::TYPE, $field, $grouping, [], $context);
    }

    /**
     * @covers ::run
     */
    public function test_resolve(): void {
        // Note: cannot use dataproviders here because PHPUnit runs these before
        // everything else. Incredibly, if a dataprovider in a random testsuite
        // creates database records or sends messages, etc, those will also be
        // visible to _all_ tests. In other words, with dataproviders, current
        // and yet unborn tests do not start in a clean state!
        [$source, $context] = $this->create_grouping(grouping::ORG);
        $plain_name = format_string($source->get_name(), true, ['context' => $context]);

        $testcases = [
            'id' => ['id', null, $source->get_id()],
            'type' => ['type', null, $source->get_type()],
            'type_label' => ['type_label', null, $source->get_type_label()],
            'default name' => ['name', null, $plain_name],
            'html name' => ['name', format::FORMAT_PLAIN, $plain_name],
            'size' => ['size', null, $source->get_size()]
        ];

        foreach ($testcases as $id => $testcase) {
            [$field, $format, $expected] = $testcase;
            $args = $format ? ['format' => $format] : [];

            $value = $this->resolve_graphql_type(self::TYPE, $field, $source, $args, $context);
            $this->assertEquals($expected, $value, "[$id] wrong value");
        }
    }

    /**
     * Generates a test grouping.
     *
     * @param string $type grouping type.
     *
     * @return array (test grouping, context) tuple.
     */
    private function create_grouping(?string $type=null): array {
        global $USER;
        $this->setAdminUser();
        $context = context_user::instance($USER->id);

        $generator = $this->getDataGenerator();
        $hierarchies = $generator->get_plugin_generator('totara_hierarchy');

        $group_users = [];
        for ($i = 0; $i < 3; $i++) {
            $group_users[] = $generator->create_user()->id;
        }

        $grouping = null;
        switch ($type) {
            case grouping::COHORT:
                $cohort = $generator->create_cohort([
                    'name' => 'My testing cohort'
                ])->id;

                foreach ($group_users as $user) {
                    cohort_add_member($cohort, $user);
                }

                $grouping = grouping::cohort($cohort);
                break;

            case grouping::ORG:
                $org = $hierarchies->create_org([
                    'frameworkid' => $hierarchies->create_org_frame([])->id,
                    'shortname' => 'My short org name',
                    'fullname' => 'My really long org name'
                ])->id;

                foreach ($group_users as $user) {
                    job_assignment::create([
                        'userid' => $user,
                        'idnumber' => "$user",
                        'organisationid' => $org
                    ]);
                }

                $grouping = grouping::org($org);
                break;

            case grouping::POS:
                $pos = $hierarchies->create_pos([
                    'frameworkid' => $hierarchies->create_pos_frame([])->id,
                    'shortname' => 'My short pos name',
                    'fullname' => 'My really long pos name'
                ])->id;

                foreach ($group_users as $user) {
                    job_assignment::create([
                        'userid' => $user,
                        'idnumber' => "$user",
                        'positionid' => $pos
                    ]);
                }

                $grouping = grouping::pos($pos);
                break;

            default:
                $user = $generator->create_user([
                    'firstname' => 'Tester',
                    'middlename' => 'Number',
                    'lastname' => 'Two'
                ])->id;

                $grouping = grouping::user($user);
        }

        return [$grouping, $context];
    }

    /**
     * Creates a graphql execution context.
     *
     * @param \context totara context to pass to the execution context.
     *
     * @return execution_context the context.
     */
    private function get_webapi_context(\context $context): execution_context {
        $ec = execution_context::create('dev', null);
        $ec->set_relevant_context($context);

        return $ec;
    }
}