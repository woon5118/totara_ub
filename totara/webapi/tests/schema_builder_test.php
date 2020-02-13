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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package totara_webapi
 */

use GraphQL\Type\Schema;
use totara_webapi\schema_builder;
use totara_webapi\schema_file_loader;

class totara_webapi_schema_builder_test  extends \advanced_testcase {

    public function test_build_schema() {
        global $CFG;

        $schema_files = [
            'test_schema_1.grahql' => file_get_contents(__DIR__.'/fixtures/webapi/test_schema_1.graphqls'),
            'test_schema_2.grahql' => file_get_contents(__DIR__.'/fixtures/webapi/test_schema_2.graphqls'),
            'test_schema_3.grahql' => file_get_contents(__DIR__.'/fixtures/webapi/test_schema_3.graphqls'),
            'format.graphqls' => file_get_contents($CFG->dirroot.'/lib/webapi/format.graphqls'),
            'status.graphqls' => file_get_contents($CFG->dirroot.'/totara/webapi/webapi/status.graphqls'),
            'course.graphqls' => file_get_contents($CFG->dirroot.'/lib/webapi/course.graphqls'),
            'template.graphqls' => file_get_contents($CFG->dirroot.'/lib/webapi/template.graphqls'),
            'user.graphqls' => file_get_contents($CFG->dirroot.'/lib/webapi/user.graphqls'),
        ];

        // Mocking the file loader as a growing schema would automatically affect this test.
        // This keeps the impact to the minimum and allows us to test not building the real whole schema
        // but just the logic to build the schema itself
        $file_loader = $this->getMockBuilder(schema_file_loader::class)
            ->getMock();

        $file_loader->expects($this->any())
            ->method('load')
            ->willReturn($schema_files);

        $builder = new schema_builder($file_loader);
        $schema = $builder->build();

        $this->assertInstanceOf(Schema::class, $schema);
        $this->assertEmpty($schema->validate());

        // Test some of the core types, testing all types is overkill
        $this->assertTrue($schema->hasType('core_user'));
        $this->assertTrue($schema->hasType('param_alpha'));
        $this->assertTrue($schema->hasType('core_date'));
        $this->assertTrue($schema->hasType('totara_webapi_status'));

        // Test some of the test schemas
        $this->assertTrue($schema->hasType('test_schema_type1'));
        $this->assertTrue($schema->hasType('test_schema_type2'));
        $this->assertTrue($schema->hasType('test_schema_type3'));

        // Test some of the queries
        $queries = $schema->getQueryType()->getFields();
        $this->assertArrayHasKey('totara_webapi_status', $queries);
        $this->assertArrayHasKey('core_template', $queries);
        $this->assertArrayHasKey('core_my_courses', $queries);
    }


}