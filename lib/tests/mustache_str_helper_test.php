<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
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
 * @copyright 2017 onwards Totara Learning Solutions LTD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Brian Barnes <brian.barnes@totaralearning.com>
 * @package   core_output
 */

use core\output\mustache_string_helper;

defined('MOODLE_INTERNAL') || die();

class mustache_string_helper_testcase extends basic_testcase {

    /**
     * @var core_renderer
     */
    protected static $renderer;

    /**
     * @var \Mustache_Engine
     */
    protected static $engine;


    public static function setUpBeforeClass() {

        global $CFG;

        require_once("{$CFG->dirroot}/lib/mustache/src/Mustache/Autoloader.php");
        Mustache_Autoloader::register();

        self::$renderer = new \core_renderer(new moodle_page(), '/');
        // Get the engine from the renderer. We do this once cause its mad.
        $class = new ReflectionClass(self::$renderer);
        $method = $class->getMethod('get_mustache');
        $method->setAccessible(true);
        self::$engine = $method->invoke(self::$renderer);
    }

    /**
     * Returns a LambdaHelper populated with the given contextdata.
     *
     * @param array|stdClass $contextdata
     * @return Mustache_LambdaHelper
     */
    protected function get_lambda_helper($contextdata = []) {
        return new \Mustache_LambdaHelper(self::$engine, new \Mustache_Context($contextdata));
    }

    /**
     * It should generate the same output as rendering the renderable without customdata.
     *
     * @covers \core\output\mustache_string_helper::__construct
     * @covers \core\output\mustache_string_helper::string
     */
    public function test_string_helper() {
        $mustachehelper = new mustache_string_helper(self::$renderer);
        $string = 'viewallcourses'; // Some random string

        $expected = get_string($string);
        $actual = $mustachehelper->str($string, $this->get_lambda_helper());

        $this->assertEquals($expected, $actual);
    }

    /**
     * It should generate the same output as rendering the renderable without customdata.
     *
     * @covers \core\output\mustache_string_helper::__construct
     * @covers \core\output\mustache_string_helper::string
     */
    public function test_string_variable_helper() {
        $mustachehelper = new mustache_string_helper(self::$renderer);

        $actualidentifier = 'viewallcourses';
        $variableidentifier = '{{test_string}}';

        $expected = get_string($actualidentifier);

        $lambdahelper = $this->get_lambda_helper(['test_string' => $actualidentifier]);
        $mustachehelper = new mustache_string_helper(self::$renderer);
        $actual = $mustachehelper->str($variableidentifier, $lambdahelper);

        $this->assertEquals($expected, $actual);
    }
}
