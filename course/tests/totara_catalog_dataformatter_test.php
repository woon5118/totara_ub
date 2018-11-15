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
 * @author Matthias Bonk <matthias.bonk@totaralearning.com>
 * @package totara_catalog
 */

namespace core_course\totara_catalog\course\dataformatter;

use context_system;
use core_completion_generator;
use stdClass;
use totara_catalog\dataformatter\dataformatter_test_base;
use totara_catalog\dataformatter\formatter;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . "/course/lib.php");
require_once($CFG->dirroot . "/totara/catalog/tests/dataformatter_test_base.php");

/**
 * @group totara_catalog
 */
class core_course_totara_catalog_dataformatter_testcase extends dataformatter_test_base {

    public function test_activity_type_icons() {
        $context = context_system::instance();

        $df = new activity_type_icons('modulesfield');
        $this->assertCount(1, $df->get_required_fields());
        $this->assertSame('modulesfield', $df->get_required_fields()['modules']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_ICONS], $df->get_suitable_types());

        $test_params = ['modules' => 'forum,book,assign,resource'];
        $result = $df->get_formatted_value($test_params, $context);
        $this->assertCount(4, $result);
        foreach ($result as $icon_object) {
            $this->assertInstanceOf(stdClass::class, $icon_object);
            $this->assertContains('flex-icon', $icon_object->icon);
        }
        // Result should be sorted predictably.
        $this->assertContains('Assignment', $result[0]->icon);
        $this->assertContains('Book', $result[1]->icon);
        $this->assertContains('File', $result[2]->icon);
        $this->assertContains('Forum', $result[3]->icon);

        $result = $df->get_formatted_value(['modules' => ''], $context);
        $this->assertSame([], $result);

        $result = $df->get_formatted_value(['modules' => ',,,forum ,,,book ,,,'], $context);
        $this->assertCount(2, $result);
        $this->assertContains('Book', $result[0]->icon);
        $this->assertContains('Forum', $result[1]->icon);

        $this->assert_exceptions($df, $test_params);
    }

    public function test_activity_types() {
        $context = context_system::instance();

        $df = new activity_types('modulesfield');
        $this->assertCount(1, $df->get_required_fields());
        $this->assertSame('modulesfield', $df->get_required_fields()['modules']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_TEXT, formatter::TYPE_FTS], $df->get_suitable_types());

        // Results should come from translations and get sorted.
        $test_params = ['modules' => 'forum,book,assign,resource'];
        $result = $df->get_formatted_value($test_params, $context);
        $this->assertSame('Assignment, Book, File, Forum', $result);

        $result = $df->get_formatted_value(['modules' => ''], $context);
        $this->assertSame('', $result);

        $result = $df->get_formatted_value(['modules' => ',,,forum ,,,book ,,,'], $context);
        $this->assertSame('Book, Forum', $result);

        // Activity types that are not in translations just get uppercased.
        $result = $df->get_formatted_value(['modules' => 'orangutan,resource,assign'], $context);
        $this->assertSame('Assignment, File, Orangutan', $result);

        $this->assert_exceptions($df, $test_params);
    }

    public function test_format() {
        $context = context_system::instance();

        $df = new format('formatfield');
        $this->assertCount(1, $df->get_required_fields());
        $this->assertSame('formatfield', $df->get_required_fields()['format']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_TEXT, formatter::TYPE_FTS], $df->get_suitable_types());

        $test_params = ['format' => 'weeks'];
        $result = $df->get_formatted_value($test_params, $context);
        $this->assertSame('Weekly format', $result);

        $result = $df->get_formatted_value(['format' => ''], $context);
        $this->assertSame('', $result);

        $this->assert_exceptions($df, $test_params);
    }

    public function test_image() {
        global $CFG;
        $this->resetAfterTest();

        $context = context_system::instance();

        $df = new image('courseidfield', 'altfield');
        $this->assertCount(2, $df->get_required_fields());
        $this->assertSame('courseidfield', $df->get_required_fields()['courseid']);
        $this->assertSame('altfield', $df->get_required_fields()['alt']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_IMAGE], $df->get_suitable_types());

        $course = $this->getDataGenerator()->create_course();
        $test_params = [
            'courseid' => $course->id,
            'alt' => 'test_alt_text',
        ];
        $result = $df->get_formatted_value($test_params, $context);
        $this->assertInstanceOf(stdClass::class, $result);
        // Check that we get a url back that includes default icon in its path.
        $this->assertContains($CFG->wwwroot, $result->url);
        $this->assertContains('/course/defaultimage', $result->url);
        $this->assertSame('test_alt_text', $result->alt);

        $this->assert_exceptions($df, $test_params);
    }

    public function test_language() {
        $context = context_system::instance();

        $df = new language('languagefield');
        $this->assertCount(1, $df->get_required_fields());
        $this->assertSame('languagefield', $df->get_required_fields()['language']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_TEXT, formatter::TYPE_FTS], $df->get_suitable_types());

        $test_params = ['language' => 'en'];
        $result = $df->get_formatted_value($test_params, $context);
        // Remove left-to-right marks, so we can compare it with our simple string.
        $lrm = json_decode('"\u200E"');
        $result = str_replace($lrm, '', $result);
        $this->assertSame('English (en)', $result);

        $result = $df->get_formatted_value(['language' => ''], $context);
        $this->assertSame('', $result);

        $this->assert_exceptions($df, $test_params);
    }

    public function test_progressbar() {
        global $CFG, $DB;
        $this->resetAfterTest();

        $CFG->enablecompletion = true;
        $context = context_system::instance();
        $generator = $this->getDataGenerator();

        // Set up student with some kind of progress, so we can expect progressbar data as a result.
        $student = $generator->create_user();
        $this->setUser($student);
        $course = $generator->create_course();
        $module_data = $generator->create_module('data', ['course' => $course->id]);
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user($student->id, $course->id, $studentrole->id);
        /** @var core_completion_generator $cgen */
        $cgen = $generator->get_plugin_generator('core_completion');
        $cgen->enable_completion_tracking($course);
        $cgen->set_activity_completion($course->id, [$module_data]);

        $df = new progressbar('courseidfield', 'statusfield');
        $this->assertCount(2, $df->get_required_fields());
        $this->assertSame('courseidfield', $df->get_required_fields()['courseid']);
        $this->assertSame('statusfield', $df->get_required_fields()['status']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_PROGRESS], $df->get_suitable_types());

        $test_params = [
            'courseid' => $course->id,
            'status' => COMPLETION_STATUS_INPROGRESS,
        ];
        $result = $df->get_formatted_value($test_params, $context);

        // Make sure result looks like progress data.
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('progress', $result);
        $this->assertArrayHasKey('progresstext', $result);
        $this->assertArrayHasKey('popover', $result);

        // Empty array expected if no progress data available.
        $course2 = $generator->create_course();
        $result = $df->get_formatted_value(['courseid' => $course2->id, 'status' => COMPLETION_STATUS_INPROGRESS], $context);
        $this->assertSame([], $result);

        // Empty array expected for empty course id.
        $result = $df->get_formatted_value(['courseid' => null, 'status' => COMPLETION_STATUS_INPROGRESS], $context);
        $this->assertSame([], $result);

        $this->assert_exceptions($df, $test_params);

        $this->expectException('coding_exception');
        $this->expectExceptionMessage(
            "Unknown or empty status passed to progress bar dataformatter when courseid was also provided"
        );
        $df->get_formatted_value(['courseid' => $course2->id, 'status' => 'bad_key'], $context);
    }

    public function test_type() {
        global $TOTARA_COURSE_TYPES;

        $context = context_system::instance();

        $df = new type('coursetypefield');
        $this->assertCount(1, $df->get_required_fields());
        $this->assertSame('coursetypefield', $df->get_required_fields()['coursetype']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_TEXT, formatter::TYPE_FTS], $df->get_suitable_types());

        $test_params = ['coursetype' => $TOTARA_COURSE_TYPES['elearning']];
        $result = $df->get_formatted_value($test_params, $context);
        $this->assertSame('E-learning', $result);

        $result = $df->get_formatted_value(['coursetype' => ''], $context);
        $this->assertSame('', $result);

        $this->assert_exceptions($df, $test_params);
    }

    public function test_type_icon() {
        global $TOTARA_COURSE_TYPES;

        $context = context_system::instance();

        $df = new type_icon('coursetypefield');
        $this->assertCount(1, $df->get_required_fields());
        $this->assertSame('coursetypefield', $df->get_required_fields()['coursetype']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_ICON], $df->get_suitable_types());

        $test_params = ['coursetype' => $TOTARA_COURSE_TYPES['elearning']];
        $result = $df->get_formatted_value($test_params, $context);
        $this->assertInstanceOf(stdClass::class, $result);
        $this->assertContains('flex-icon', $result->icon);
        $this->assertContains('E-Learning', $result->icon);

        $result = $df->get_formatted_value(['coursetype' => ''], $context);
        $this->assertSame(null, $result);

        $result = $df->get_formatted_value(['coursetype' => 'bad_type'], $context);
        $this->assertSame(null, $result);

        $this->assert_exceptions($df, $test_params);
    }

    public function test_type_icons() {
        global $TOTARA_COURSE_TYPES;

        $context = context_system::instance();

        $df = new type_icons('coursetypefield');
        $this->assertCount(1, $df->get_required_fields());
        $this->assertSame('coursetypefield', $df->get_required_fields()['coursetype']);

        $this->assertSame([formatter::TYPE_PLACEHOLDER_ICONS], $df->get_suitable_types());

        $test_params = ['coursetype' => $TOTARA_COURSE_TYPES['elearning']];
        $result = $df->get_formatted_value($test_params, $context);
        $result = $result[0];
        $this->assertInstanceOf(stdClass::class, $result);
        $this->assertContains('flex-icon', $result->icon);
        $this->assertContains('E-Learning', $result->icon);

        $result = $df->get_formatted_value(['coursetype' => ''], $context);
        $this->assertSame([], $result);

        $result = $df->get_formatted_value(['coursetype' => 'bad_type'], $context);
        $this->assertSame([], $result);

        $this->assert_exceptions($df, $test_params);
    }
}
