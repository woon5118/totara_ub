<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 *
 *
 * @package
 * @copyright  2016 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class progress_display_test extends \advanced_testcase {

    protected $reflectionclass;

    protected $instance;

    public function setUp() {
         $this->reflectionclass = new ReflectionClass('\core\progress\display');
    }

    protected function make_property_accessible($propname) {
        $reflectionprop = $this->reflectionclass->getProperty($propname);
        $reflectionprop->setAccessible(true);
        return $reflectionprop;
    }

    protected function get_inaccessible_property($instance, $propname) {
        return $this->make_property_accessible($propname)->getValue($instance);
    }

    protected function set_inaccessible_property($instance, $propname, $value) {
        return $this->make_property_accessible($propname)->setValue($instance, $value);
    }

    /**
     * Test basic function of progress_display, updating status and outputting wibbler.
     */
    public function test_progress_display_update() {
        ob_start();
        $progress = new \core\progress\display();
        $progress->start_progress('');
        $this->assertEquals(1, $this->get_inaccessible_property($progress, 'currentstate'));
        $this->assertEquals(1, $this->get_inaccessible_property($progress, 'direction'));
        $this->assertTimeCurrent($this->get_inaccessible_property($progress, 'lastwibble'));
        // Wait 1 second to ensure that all code in update_progress is run.
        $this->waitForSecond();
        $progress->update_progress();
        $this->assertEquals(2, $this->get_inaccessible_property($progress, 'currentstate'));
        $this->assertEquals(1, $this->get_inaccessible_property($progress, 'direction'));
        $this->assertTimeCurrent($this->get_inaccessible_property($progress, 'lastwibble'));
        $output = ob_get_clean();
        $this->assertContains('wibbler', $output);
        $this->assertContains('wibble state0', $output);
        $this->assertContains('wibble state1', $output);
    }

    /**
     * Test wibbler states. Wibbler should reverse direction at the start and end of its sequence.
     */
    public function test_progress_display_wibbler() {
        ob_start();
        $progress = new \core\progress\display();
        $progress->start_progress('');
        $this->assertEquals(1, $this->get_inaccessible_property($progress, 'direction'));

        // Set wibbler to final state and progress to check that it reverses direction.
        $this->set_inaccessible_property($progress, 'currentstate', \core\progress\display::WIBBLE_STATES);
        $this->waitForSecond();
        $progress->update_progress();
        $this->assertEquals(\core\progress\display::WIBBLE_STATES - 1, $this->get_inaccessible_property($progress, 'currentstate'));
        $this->assertEquals(-1, $this->get_inaccessible_property($progress, 'direction'));

        // Set wibbler to beginning and progress to check that it reverses direction.
        $this->set_inaccessible_property($progress, 'currentstate', 0);
        $this->waitForSecond();
        $progress->update_progress();
        $this->assertEquals(1, $this->get_inaccessible_property($progress, 'currentstate'));
        $this->assertEquals(1, $this->get_inaccessible_property($progress, 'direction'));
        $output = ob_get_clean();
        $this->assertContains('wibbler', $output);
        $this->assertContains('wibble state0', $output);
        $this->assertContains('wibble state13', $output);

    }

}
