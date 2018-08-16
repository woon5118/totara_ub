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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests of our upstream hacks and behaviour expected in Totara.
 */
class totara_core_weblib_testcase extends advanced_testcase {
    public function test_clean_text() {
        // Make sure that data-core-autoinitialise and data-core-autoinitialise-amd are
        // stripped from from HTML markup added by regular users.
        $html = '<div class="someclass" data-core-autoinitialise="true" data-core-autoinitialise-amd="mod_mymod/myelement" data-x-yyy="2">sometext</div>';
        $expected = '<div class="someclass">sometext</div>';
        $this->assertSame($expected, clean_text($html, FORMAT_HTML));
    }
}
