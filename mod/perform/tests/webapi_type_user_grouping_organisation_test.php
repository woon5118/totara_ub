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

use mod_perform\webapi\resolver\type\user_grouping_organisation;

/**
 * @coversDefaultClass organisation.
 *
 * @group perform
 *
 * TODO: this should be combined with totara_competency/user_groups and put into
 * totara core somewhere.
 */
class mod_perform_webapi_type_user_grouping_organisation_testcase extends advanced_testcase {
    /**
     * @covers ::resolve
     */
    public function test_invalid_input(): void {
        [, $context] = $this->create_org();
        $webapi_context = $this->get_webapi_context($context);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessageRegExp("/stdClass/");
        user_grouping_organisation::resolve('id', [], [], $webapi_context);
    }

    /**
     * @covers ::resolve
     */
    public function test_invalid_field(): void {
        [, $context] = $this->create_org();
        $webapi_context = $this->get_webapi_context($context);
        $field = 'unknown';

        $organisation = new stdClass();

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessageRegExp("/$field/");
        user_grouping_organisation::resolve($field, $organisation, [], $webapi_context);
    }

    /**
     * @covers ::runorganisation
     * @dataProvider td_resolve
     *
     * @param context $context totara context.
     * @param stdClass $source raw data source.
     * @param string $field field to resolve.
     * @param string $format field format.
     * @param mixed $expected expected field value.
     */
    public function test_resolve(
        context $context,
        stdClass $source,
        string $field,
        ?string $format,
        $expected
    ): void {
        $webapi_context = $this->get_webapi_context($context);
        $args = $format ? ['format' => $format] : [];

        $value = user_grouping_organisation::resolve($field, $source, $args, $webapi_context);
        $this->assertEquals($expected, $value, 'wrong value');
    }

    /**
     * Data provider for test_resolve().
     *
     * Note: do NOT create database records in this method. Due to the way PHPUnit
     * works with dataproviders, these records will also be visible to _all_ tests.
     * In other words, with dataproviders, current and yet unborn tests do not
     * start in a clean state!
     */
    public function td_resolve(): array {
        [$source, $context] = $this->create_org(
            '<h1>This is a <strong>test</strong> description</h1>'
        );
        $plain_desc = format_string($source->description, true, ['context' => $context]);

        return [
            'id' => [$context, $source, 'id', null, $source->id],
            'shortname' => [$context, $source, 'shortname', null, $source->shortname],
            'fullname' => [$context, $source, 'fullname', null, $source->fullname],
            'default desc' => [$context, $source, 'description', null, $plain_desc],
            'plain desc' => [$context, $source, 'description', format::FORMAT_PLAIN, $plain_desc],
            'idnumber' => [$context, $source, 'idnumber', null, $source->idnumber]
        ];
    }

    /**
     * Generates a test organisation.
     *
     * @param string $description organisation description.
     *
     * @return array (test organisation, context) tuple.
     */
    private function create_org(string $description=''): array {
        global $USER;
        $this->setAdminUser();
        $context = context_user::instance($USER->id);

        $source = (object) [
            'id' => 34324,
            'shortname' => 'My test organisation',
            'fullname' => 'My test organisation long name',
            'idnumber' => 'My test organisation idnumber',
            'description' => $description
        ];

        return [$source, $context];
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