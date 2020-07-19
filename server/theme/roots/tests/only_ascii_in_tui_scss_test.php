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
 * @package theme_roots
 */

global $CFG;
require_once($CFG->dirroot . '/totara/tui/tests/only_ascii_in_tui_scss_base_testcase.php');

class theme_roots_only_ascii_in_tui_scss_testcase extends core_only_ascii_in_tui_scss_base_testcase {
    public function test_no_unicode_in_scss() {
        $this->check_tui_scss_for_non_ascii('theme_roots');
    }
}
