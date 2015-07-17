<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2015 onwards Totara Learning Solutions LTD
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
 * @author Petr Skoda <petr.skoda@totaralms.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

class totara_core_random_string_testcase extends advanced_testcase {
    public function test_totara_random_bytes() {
        $result = totara_random_bytes(10);
        $this->assertSame(10, strlen($result));
        $this->assertnotSame($result, totara_random_bytes(10));

        $result = totara_random_bytes(21);
        $this->assertSame(21, strlen($result));
        $this->assertnotSame($result, totara_random_bytes(21));

        $result = totara_random_bytes(666);
        $this->assertSame(666, strlen($result));

        $this->assertDebuggingNotCalled();

        $result = totara_random_bytes(0);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();

        $result = totara_random_bytes(-1);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();
    }

    public function test_random_string() {
        $pool = 'a-zA-Z0-9';

        $result = random_string(10);
        $this->assertSame(10, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);
        $this->assertNotSame($result, random_string(10));

        $result = random_string(21);
        $this->assertSame(21, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);
        $this->assertNotSame($result, random_string(21));

        $result = random_string(666);
        $this->assertSame(666, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);

        $result = random_string();
        $this->assertSame(15, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);

        $this->assertDebuggingNotCalled();

        $result = random_string(0);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();

        $result = random_string(-1);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();
    }

    public function test_complex_random_string() {
        $pool = preg_quote('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789`~!@#%^&*()_+-=[];,./<>?:{} ', '/');

        $result = complex_random_string(10);
        $this->assertSame(10, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);
        $this->assertNotSame($result, complex_random_string(10));

        $result = complex_random_string(21);
        $this->assertSame(21, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);
        $this->assertNotSame($result, complex_random_string(21));

        $result = complex_random_string(666);
        $this->assertSame(666, strlen($result));
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);

        $result = complex_random_string();
        $this->assertEquals(28, strlen($result), '', 4); // Expected length is 24 - 32.
        $this->assertRegExp('/^[' . $pool . ']+$/', $result);

        $this->assertDebuggingNotCalled();

        $result = complex_random_string(0);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();

        $result = complex_random_string(-1);
        $this->assertSame('', $result);
        $this->assertDebuggingCalled();
    }
}
