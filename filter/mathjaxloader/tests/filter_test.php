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
 * @author Sam Hemelryk <sam.hemelryk@totaralearning.com>
 * @package filter_mathjaxloader
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class filter_mathjaxloader_filter_testcase
 */
class filter_mathjaxloader_filter_testcase extends advanced_testcase {

    public static function setUpBeforeClass() {
        global $CFG;
        require_once($CFG->dirroot . '/filter/mathjaxloader/filter.php');
        parent::setUpBeforeClass();
    }

    public function test_filter() {
        global $CFG;

        $this->resetAfterTest();

        // Prep the required structure.
        $learner = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($learner->id, $course->id);
        $coursecontext = context_course::instance($course->id);

        // Turn on legacy mode. We'll test that.
        $CFG->forced_plugin_settings['filter_mathjaxloader']['texfiltercompatibility'] = true;

        // Prep some data.
        $text1 = "$$ x^n + y^n = z^n $$";
        $text2 = "[tex] x^n + y^n = z^n [/tex]";
        $text3 = "<tex> x^n + y^n = z^n </tex>";
        $text4 = "\( x^n + y^n = z^n \)";

        // They should all end up looking the same.
        $filtered = '<span class="filter_mathjaxloader_equation"><span class="nolink">\( x^n + y^n = z^n \)</span></span>';

        // Access it like we're the learner.
        $this->setUser($learner);

        // Check it filters at the course context level.
        $filter = new filter_mathjaxloader($coursecontext, []);
        self::assertSame($filtered, $filter->filter($text1));

        $filtered = $filter->filter($text2);
        self::assertSame($filtered, $filter->filter($text2));

        $filtered = $filter->filter($text3);
        self::assertSame($filtered, $filter->filter($text3));

        $filtered = $filter->filter($text4);
        self::assertSame($filtered, $filter->filter($text4));

        self::assertSame($filtered, clean_text($filtered, FORMAT_HTML));
    }

    public function test_is_compatible_with_clean_text() {

        $method = new ReflectionMethod('filter_mathjaxloader', 'is_compatible_with_clean_text');
        $method->setAccessible(true);
        self::assertTrue($method->invoke(null));

    }

}