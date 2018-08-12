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
 * @author Michael Dunstan <michael.dunstan@androgogic.com>
 * @package totara_contentmarketplace
 */

use totara_contentmarketplace\local;

defined('MOODLE_INTERNAL') || die();

class local_testcase extends basic_testcase {

    /**
     * @dataProvider money_provider
     */
    public function test_format_money($locale, $value, $currency, $expected) {
        $price= local::format_money($value, $currency, $locale);
        $this->assertSame($expected, $price);
    }

    public function money_provider() {
        return [
            ['en_AU.UTF-8', 0, "AUD", 'A$0.00'],
            ['en_AU.UTF-8', 1, "AUD", 'A$1.00'],
            ['en_AU.UTF-8', 1.5, "AUD", 'A$1.50'],
            ['en_AU.UTF-8', 1234.5, "AUD", 'A$1,234.50'],
            ['en_AU.UTF-8', 1, "JPY", '¥1'],
            // NumberFormatter returns no-break space unicode chars (U+00A0)
            // in some cases.
            ['fr_FR.UTF-8', 1234.5, "AUD",  "1\u{00a0}234,50\u{00a0}\$AU"],
            ['de_DE.UTF-8', 1234.5, "USD", "1.234,50\u{00a0}\$"],

        ];
    }




}
