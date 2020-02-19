<?php
/*
 * This file is part of Totara LMS
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
 * @author David Curry <david.curry@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

use core\format;
use totara_webapi\phpunit\webapi_phpunit_helper;

/**
 * Tests the totara core learning item type resolver.
 */
class totara_core_webapi_resolver_type_course_testcase extends advanced_testcase {

    use webapi_phpunit_helper;

    private function resolve($field, $course, array $args = []) {
        return $this->resolve_graphql_type('core_course', $field, $course, $args);
    }

    /**
     * Create some courses and assign some users for testing.
     * @return []
     */
    private function create_faux_courses(array $users = []) {
        $users = [];
        $users[] = $this->getDataGenerator()->create_user();
        $users[] = $this->getDataGenerator()->create_user();
        $users[] = $this->getDataGenerator()->create_user();

        $courses = [];
        $courses[] = $this->getDataGenerator()->create_course(['shortname' => 'c1', 'fullname' => 'course1', 'summary' => 'first course']);
        $courses[] = $this->getDataGenerator()->create_course(['shortname' => 'c2', 'fullname' => 'course2', 'summary' => 'second course']);
        $courses[] = $this->getDataGenerator()->create_course(['shortname' => 'c3', 'fullname' => 'course3', 'summary' => 'third course', 'visible' => 0]);

        $this->getDataGenerator()->enrol_user($users[0]->id, $courses[0]->id, 'student', 'manual');
        $this->getDataGenerator()->enrol_user($users[1]->id, $courses[0]->id, 'student', 'manual');
        $this->getDataGenerator()->enrol_user($users[1]->id, $courses[1]->id, 'student', 'manual');

        return [$users, $courses];
    }

    /**
     * Check that this only works for learning items.
     */
    public function test_resolve_courses_only() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setAdminUser();

        try {
            // Attempt to resolve an integer.
            $this->resolve('id', 7);
            $this->fail('Only course objects should be accepted');
        } catch (\coding_exception $ex) {
            $this->assertSame(
                'Coding error detected, it must be fixed by a programmer: Only course objects are accepted: integer',
                $ex->getMessage()
            );
        }

        try {
            // Attempt to resolve an array.
            $this->resolve('id', ['id' => 7]);
            $this->fail('Only course instances should be accepted');
        } catch (\coding_exception $ex) {
            $this->assertSame(
                'Coding error detected, it must be fixed by a programmer: Only course objects are accepted: array',
                $ex->getMessage()
            );
        }

        try {
            // Attempt to resolve a user item.
            $this->resolve('id', $users[0]);
            $this->fail('Only course instances should be accepted');
        } catch (\coding_exception $ex) {
            $this->assertSame(
                'Coding error detected, it must be fixed by a programmer: Only valid course objects are accepted',
                $ex->getMessage()
            );
        }

        try {
            // Attempt to resolve an invalid object.
            $faux = new \stdClass();
            $faux->id = -1;
            $this->resolve('id', $faux);
            $this->fail('Only course instances should be accepted');
        } catch (\coding_exception $ex) {
            $this->assertSame(
                'Coding error detected, it must be fixed by a programmer: Only valid course objects are accepted',
                $ex->getMessage()
            );
        }

        // Check that each core instance of learning item gets resolved.
        try {
            $value = $this->resolve('id', $courses[0]);
            $this->assertEquals($courses[0]->id, $value);
        } catch (\coding_exception $ex) {
            $this->fail($ex->getMessage());
        }

        try {
            $value = $this->resolve('id', $courses[1]);
            $this->assertEquals($courses[1]->id, $value);
        } catch (\coding_exception $ex) {
            $this->fail($ex->getMessage());
        }

        try {
            $value = $this->resolve('id', $courses[2]);
            $this->assertEquals($courses[2]->id, $value);
        } catch (\coding_exception $ex) {
            $this->fail($ex->getMessage());
        }
    }

    /**
     * Test the learning item type resolver for the id field
     */
    public function test_resolve_id() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('id', $course);
        $this->assertEquals($course->id, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the idnumber field
     */
    public function test_resolve_idnumber() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('idnumber', $course);
        $this->assertEquals($course->idnumber, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the shortname field
     */
    public function test_resolve_shortname() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);
        $formats = [format::FORMAT_HTML, format::FORMAT_PLAIN];

        try {
            $value = $this->resolve('shortname', $courses[0]);
            $this->fail('Expected failure on null $format');
        } catch (\coding_exception $ex) {
            $this->assertSame(
                'Coding error detected, it must be fixed by a programmer: Invalid format given',
                $ex->getMessage()
            );
        }

        foreach ($formats as $format) {
            $value = $this->resolve('shortname', $courses[0], ['format' => $format]);
            $this->assertEquals($courses[0]->shortname, $value);
            $this->assertTrue(is_string($value));
        }

        // Check the permissions required for format::FORMAT_RAW
        $value = $this->resolve('shortname', $courses[0], ['format' => format::FORMAT_RAW]);
        $this->assertNull($value);

        $this->setAdminUser();
        $value = $this->resolve('shortname', $courses[0], ['format' => format::FORMAT_RAW]);
        $this->assertEquals($courses[0]->shortname, $value);
    }

    /**
     * Test the learning item type resolver for the fullname field
     */
    public function test_resolve_fullname() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);
        $formats = [format::FORMAT_HTML, format::FORMAT_PLAIN];

        try {
            $value = $this->resolve('fullname', $courses[0]);
            $this->fail('Expected failure on null $format');
        } catch (\coding_exception $ex) {
            $this->assertSame(
                'Coding error detected, it must be fixed by a programmer: Invalid format given',
                $ex->getMessage()
            );
        }

        foreach ($formats as $format) {
            $value = $this->resolve('fullname', $courses[0], ['format' => $format]);
            $this->assertEquals($courses[0]->fullname, $value);
            $this->assertTrue(is_string($value));
        }

        // Check the permissions required for format::FORMAT_RAW
        $value = $this->resolve('fullname', $courses[0], ['format' => format::FORMAT_RAW]);
        $this->assertNull($value);

        $this->setAdminUser();
        $value = $this->resolve('fullname', $courses[0], ['format' => format::FORMAT_RAW]);
        $this->assertEquals($courses[0]->fullname, $value);
    }

    /**
     * Test the learning item type resolver for the summary field
     */
    public function test_resolve_summary() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);
        $formats = [format::FORMAT_HTML, format::FORMAT_PLAIN];

        try {
            $value = $this->resolve('summary', $courses[0]);
            $this->fail('Expected failure on null $format');
        } catch (\coding_exception $ex) {
            $this->assertSame(
                'Coding error detected, it must be fixed by a programmer: Invalid format given',
                $ex->getMessage()
            );
        }

        foreach ($formats as $format) {
            $value = $this->resolve('summary', $courses[0], ['format' => $format]);
            if ($format == format::FORMAT_PLAIN) {
                $this->assertEquals($courses[0]->summary, $value);
            }
            if ($format == format::FORMAT_HTML) {
                $this->assertEquals('<div class="text_to_html">'.$courses[0]->summary.'</div>', $value);
            }
            $this->assertTrue(is_string($value));
        }

        // Check the permissions required for format::FORMAT_RAW
        $value = $this->resolve('summary', $courses[0], ['format' => format::FORMAT_RAW]);
        $this->assertNull($value);

        $this->setAdminUser();
        $value = $this->resolve('summary', $courses[0], ['format' => format::FORMAT_RAW]);
        $this->assertEquals($courses[0]->summary, $value);
    }

    /**
     * Test the learning item type resolver for the summaryformat field
     */
    public function test_resolve_summaryformat() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('summaryformat', $courses[0]);
        $this->assertEquals($courses[0]->summaryformat, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the timecreated field
     */
    public function test_resolve_timecreated() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('timecreated', $courses[0], ['format' => \core\date_format::FORMAT_TIMESTAMP]);
        $this->assertEquals($courses[0]->timecreated, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the timemodified field
     */
    public function test_resolve_timemodified() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('timemodified', $courses[0], ['format' => \core\date_format::FORMAT_TIMESTAMP]);
        $this->assertEquals($courses[0]->timemodified, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the timemodified field
     */
    public function test_resolve_startdate() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('startdate', $courses[0], ['format' => \core\date_format::FORMAT_TIMESTAMP]);
        $this->assertEquals($courses[0]->startdate, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the timemodified field
     */
    public function test_resolve_enddate() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('enddate', $courses[0], ['format' => \core\date_format::FORMAT_TIMESTAMP]);
        $this->assertSame(null, $value);

        $courses[0]->enddate = time();
        $value = $this->resolve('enddate', $courses[0], ['format' => \core\date_format::FORMAT_TIMESTAMP]);
        $this->assertEquals($courses[0]->enddate, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the theme field
     */
    public function test_resolve_theme() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('theme', $course);
        $this->assertEquals($course->theme, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the lang field
     */
    public function test_resolve_lang() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('lang', $course);
        $this->assertEquals($course->lang, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the format field
     */
    public function test_resolve_format() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('format', $course);
        $this->assertEquals($course->format, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the coursetype field
     */
    public function test_resolve_coursetype() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('coursetype', $course);
        $this->assertEquals($course->coursetype, $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the icon field
     */
    public function test_resolve_icon() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);

        // Check that each core instance of learning item gets resolved correctly.
        $this->assertSame(null, $course->icon);
        $value = $this->resolve('icon', $course);
        $this->assertEquals('default', $value);
        $this->assertTrue(is_string($value));

        $course->icon = 'law-of-business-entities';
        $value = $this->resolve('icon', $course);
        $this->assertEquals('law-of-business-entities', $value);
        $this->assertTrue(is_string($value));
    }

    /**
     * Test the learning item type resolver for the image (url) field
     */
    public function test_resolve_image() {
        list($users, $courses) = $this->create_faux_courses();
        $this->setUser($users[0]);
        $course = get_course($courses[0]->id);
        $course->image = course_get_image($course);

        // Check that each core instance of learning item gets resolved correctly.
        $value = $this->resolve('image', $course);
        $this->assertEquals('https://www.example.com/moodle/theme/image.php/_s/ventura/core/1/course_defaultimage', $value);
        $this->assertTrue(is_string($value));
    }
}
