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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>>
 * @package   core
 */

use core\output\flex_icon_helper;

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit unit tests for \core\output\flex_icon_helper class.
 */
class totara_core_flex_icon_helper_testcase extends advanced_testcase {
    public function test_get_icons() {
        global $CFG;
        $this->resetAfterTest();

        $icons = flex_icon_helper::get_icons($CFG->theme);
        $this->assertInternalType('array', $icons);

        purge_all_caches();
        $this->assertSame($icons, flex_icon_helper::get_icons($CFG->theme));

        $this->assertSame($icons, flex_icon_helper::get_icons(null));
        $this->assertSame($icons, flex_icon_helper::get_icons(''));
        $this->assertSame($icons, flex_icon_helper::get_icons('xzxzzxzxzx'));
    }

    public function test_get_template_by_identifier() {
        global $CFG;

        $this->assertSame('core/flex_icon', flex_icon_helper::get_template_by_identifier($CFG->theme, 'edit'));
        $this->assertSame('core/flex_icon_stack', flex_icon_helper::get_template_by_identifier($CFG->theme, 'unsubscribe'));

        $missingiconstemplate = flex_icon_helper::get_template_by_identifier($CFG->theme, flex_icon_helper::MISSING_ICON);
        $this->assertSame($missingiconstemplate, flex_icon_helper::get_template_by_identifier($CFG->theme, 'xxxzxxzxzxz'));
    }

    public function test_get_data_by_identifier() {
        global $CFG;

        $expected = array('classes' => 'fa fa-edit');
        $this->assertSame($expected, flex_icon_helper::get_data_by_identifier($CFG->theme, 'edit'));

        $expected = array('classes' => array(
            'stack_first' => 'fa fa-question ft-stack-main',
            'stack_second' => 'fa fa-exclamation ft-stack-suffix'));
        $this->assertSame($expected, flex_icon_helper::get_data_by_identifier($CFG->theme, 'unsubscribe'));

        $missingiconsdata = flex_icon_helper::get_data_by_identifier($CFG->theme, flex_icon_helper::MISSING_ICON);
        $this->assertSame($missingiconsdata, flex_icon_helper::get_data_by_identifier($CFG->theme, 'xxxzxxzxzxz'));
    }

    public function test_template_overrides() {
        // TODO: add tests that verify overriding of translations and maps via fixture theme injected via $CFG->themedir.
    }

    public function test_all_flex_icons_files() {
        // TODO: validate all pix/flex_icons.php files.
    }
}
