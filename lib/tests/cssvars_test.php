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
 * @author Simon Chester <simon.chester@totaralearning.com>
 * @package totara_core
 */

defined('MOODLE_INTERNAL') || die();

class core_cssvars_testcase extends basic_testcase {
    public function test_values_replaced() {
        $cssvars = new \core\cssvars();
        $css = ':root { --val: 5px; } a { width: var(--val); }';
        $transformed = $cssvars->transform($css);
        $this->assertStringContainsString('a { width: 5px; }', $transformed);
    }

    public function test_var_references_resolved() {
        $cssvars = new \core\cssvars();
        // try referencing vars in various orders
        $css = ":root { --w: 5px; --w-2: var(--w); --h-2: var(--h); --h: 10px; /* --h: 20px; */ " .
            "--m: 1px; --m-2: var(--m); --m: 2px; } a { width: var(--w-2); height: var(--h-2); margin: var(--m-2); }";
        $transformed = $cssvars->transform($css);
        $this->assertStringContainsString('a { width: 5px; height: 10px; margin: 2px; }', $transformed);

        // test multiline
        $css = ":root\n{\n--bg:\n#06c\n;\n}\n\n#foo\n{\nbackground-color:\nvar(--bg)\n;\n}\n";
        $transformed = $cssvars->transform($css);
        $this->assertStringContainsString("#foo\n{\nbackground-color:\n#06c\n;\n}\n", $transformed);
    }

    public function test_var_compat() {
        $cssvars = new \core\cssvars();
        $css = ':root{--bg:#06c;/*--bg:red;*/}';
        $transformed = $cssvars->transform($css);
        $this->assertEquals(':root{--bg:#06c;-var--bg:#06c;}', $transformed);

        // test multiline
        $css = ":root\n{\n--bg:\n#06c\n;\n}";
        $transformed = $cssvars->transform($css);
        $this->assertEquals(":root{--bg:\n#06c;-var--bg:\n#06c;}", $transformed);
    }
}
