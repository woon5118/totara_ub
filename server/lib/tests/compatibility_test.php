<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2021 onwards Totara Learning Solutions LTD
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
 * @package core
 */

class core_compatibility_testcase extends advanced_testcase {
    public function test_str_contains() {
        $this->assertTrue(str_contains('abc def XYZ', ''));
        $this->assertTrue(str_contains('abc def XYZ', 'de'));
        $this->assertFalse(str_contains('abc def XYZ', 'A'));
        $this->assertFalse(str_contains('abc def XYZ', 'Á'));
    }

    public function test_str_starts_with() {
        $this->assertTrue(str_starts_with('abc def XYZ', ''));
        $this->assertTrue(str_starts_with('abc def XYZ', 'ab'));
        $this->assertFalse(str_starts_with('abc def XYZ', 'A'));
        $this->assertFalse(str_starts_with('abc def XYZ', 'á'));
    }

    public function test_str_ends_with() {
        $this->assertTrue(str_ends_with('abc def XYZ', ''));
        $this->assertTrue(str_ends_with('abc def XYZ', 'YZ'));
        $this->assertFalse(str_ends_with('abc def XYZ', 'y'));
        $this->assertFalse(str_ends_with('abc def XYZ', 'ÿ'));
    }
}
