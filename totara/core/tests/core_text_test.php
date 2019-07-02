<?php
/*
 * This file is part of Totara Learn
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
 * @author Petr Skoda <petr.skoda@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests changes and additions in core_text class
 */
class totara_core_core_text_testcase extends advanced_testcase {
    public function test_entities_to_utf8() {
        $entities = file_get_contents(__DIR__ . '/fixtures/all_html_entities.txt');

        $utf8text = html_entity_decode($entities, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $this->assertSame(1, substr_count($utf8text, '&'));
        $this->assertSame($utf8text, fix_utf8($utf8text));

        $this->assertSame($utf8text, core_text::entities_to_utf8($entities));
        $this->assertSame($utf8text, core_text::entities_to_utf8($entities, true));
        $this->assertSame($entities, core_text::entities_to_utf8($entities, false));

        $numeric = core_text::utf8_to_entities($utf8text);
        $this->assertSame($utf8text, core_text::entities_to_utf8($numeric));
        $this->assertSame($utf8text, core_text::entities_to_utf8($numeric, true));
        $this->assertSame($utf8text, core_text::entities_to_utf8($numeric, false));

        $this->assertSame('', core_text::entities_to_utf8(null));
    }
}
