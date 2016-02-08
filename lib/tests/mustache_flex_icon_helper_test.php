<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2016 onwards Totara Learning Solutions LTD
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
 * @copyright 2016 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Joby Harding <joby.harding@totaralms.com>>
 * @package   core_output
 */

use core\output\flex_icon;
use core\output\mustache_flex_icon_helper;

defined('MOODLE_INTERNAL') || die();

class mustache_flex_icon_helper_testcase extends basic_testcase {

    /**
     * @var core_renderer
     */
    protected static $renderer;

    /**
     * @var Mustache_LambdaHelper
     */
    protected static $lambdahelper;


    public static function setUpBeforeClass() {

        global $CFG;

        require_once("{$CFG->dirroot}/lib/mustache/src/Mustache/Autoloader.php");
        Mustache_Autoloader::register();

        self::$renderer = new \core_renderer(new moodle_page(), '/');
        self::$lambdahelper = new \Mustache_LambdaHelper(new \Mustache_Engine(), new \Mustache_Context());

    }

    /**
     * It should generate the same output as rendering the renderable without customdata.
     *
     * @covers \core\output\mustache_flex_icon_helper::__construct
     * @covers \core\output\mustache_flex_icon_helper::flex_icon
     */
    public function test_flex_icon_output_without_customdata() {

        global $OUTPUT;

        $identifier = 'check-permissions';
        $mustachehelper = new mustache_flex_icon_helper(self::$renderer);

        $expected = $OUTPUT->render(new flex_icon($identifier));
        $actual = $mustachehelper->flex_icon($identifier, self::$lambdahelper);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should generate the same output as rendering the renderable with customdata.
     *
     * @covers \core\output\mustache_flex_icon_helper::flex_icon
     */
    public function test_flex_icon_output_with_customdata() {

        global $OUTPUT;

        $identifier = 'check-permissions';
        $customdata = array(
            'classes' => 'ft-state-success ft-size-700'
        );
        $helperstring = "{$identifier}, " . json_encode($customdata);

        $mustachehelper = new mustache_flex_icon_helper(self::$renderer);

        $expected = $OUTPUT->render(new flex_icon($identifier, $customdata));
        $actual = $mustachehelper->flex_icon($helperstring, self::$lambdahelper);

        $this->assertEquals($expected, $actual);

    }

    /**
     * It should throw an exception if helper JSON cannot be parsed.
     *
     * @covers \core\output\mustache_flex_icon_helper::flex_icon
     */
    public function test_flex_icon_throws() {

        $malformedjson = '{ this # is not valid JSON : 7 \ }';
        $helperstring = "cog, {$malformedjson}";

        $mustachehelper = new mustache_flex_icon_helper(self::$renderer);

        $this->setExpectedException('\coding_exception');
        $mustachehelper->flex_icon($helperstring, self::$lambdahelper);

    }

}
