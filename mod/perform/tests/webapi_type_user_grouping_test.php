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

use mod_perform\webapi\resolver\type\user_grouping;

/**
 * @coversDefaultClass user_grouping.
 *
 * @group perform
 *
 * TODO: this should be combined with totara_competency/user_groups and put into
 * totara core somewhere.
 */
class mod_perform_webapi_type_user_grouping_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_invalid_input(): void {
        [, $context] = $this->create_grouping();
        $webapi_context = $this->get_webapi_context($context);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageRegExp("/user_grouping/");
        user_grouping::resolve('id', new \stdClass(), [], $webapi_context);
    }

    /**
     * @covers ::resolve
     */
    public function test_invalid_field(): void {
        [$grouping, $context] = $this->create_grouping();
        $webapi_context = $this->get_webapi_context($context);
        $field = 'unknown';

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageRegExp("/$field/");
        user_grouping::resolve($field, $grouping, [], $webapi_context);
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
        $webapi_context = $this->get_webapi_context($context);
        $plain_name = format_string($source->get_name(), true, ['context' => $context]);

        $testcases = [
            'id' => ['id', null, $source->get_id()],
            'type' => ['type', null, $source->get_type()],
            'type_label' => ['type_label', null, $source->get_type_label()],
            'default name' => ['name', null, $plain_name],
            'html name' => ['name', format::FORMAT_PLAIN, $plain_name]
        ];

        foreach ($testcases as $id => $testcase) {
            [$field, $format, $expected] = $testcase;
            $args = $format ? ['format' => $format] : [];

            $value = user_grouping::resolve($field, $source, $args, $webapi_context);
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

        $grouping = null;
        switch ($type) {
            case grouping::COHORT:
                $cohort = $generator->create_cohort([
                    'name' => 'My testing cohort'
                ])->id;

                $grouping = grouping::cohort($cohort);
                break;

            case grouping::ORG:
                $org = $hierarchies->create_org([
                    'frameworkid' => $hierarchies->create_org_frame([])->id,
                    'shortname' => 'My short org name',
                    'fullname' => 'My really long org name'
                ])->id;

                $grouping = grouping::org($org);
                break;

            case grouping::POS:
                $pos = $hierarchies->create_pos([
                    'frameworkid' => $hierarchies->create_pos_frame([])->id,
                    'shortname' => 'My short pos name',
                    'fullname' => 'My really long pos name'
                ])->id;

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