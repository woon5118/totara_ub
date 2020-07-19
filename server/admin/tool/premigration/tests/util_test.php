<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2020 onwards Totara Learning Solutions LTD
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
 * @package tool_premigration
 */

use tool_premigration\local\util;

/**
 * Test class tool_premigration_util_testcase.
 */
class tool_premigration_util_testcase extends advanced_testcase {
    public function test_get_backported_plugins() {
        $backported = util::get_backported_plugins();
        $this->assertIsArray($backported);
        foreach ($backported as $plugin => $version) {
            $this->assertIsNumeric($version, "Backported plugin '$plugin' version '$version' is not numeric");
        }
    }

    public function test_load_release_versions() {
        $versions = util::load_release_versions('v3.4.9');

        $this->assertIsArray($versions);

        $this->assertSame('v3.4.9', $versions['tag']);
        $this->assertSame(2017111309.0, $versions['version']);
        $this->assertSame('3.4.9 (Build: 20190513)', $versions['release']);

        $this->assertIsArray($versions['plugins']);
        $this->assertSame(2017111300, $versions['plugins']['mod_book']['version']);
        $this->assertSame('/mod/book', $versions['plugins']['mod_book']['relative_path']);
    }

    public function test_get_supported_releases() {
        $releases = util::get_supported_releases();

        $this->assertArrayNotHasKey((string)2017111309.0, $releases);

        foreach ($releases as $key => $versions) {
            $this->assertSame((string)$key, (string)$versions['version']);
            $this->assertArrayHasKey('tag', $versions);
            $this->assertArrayHasKey('release', $versions);
            $this->assertArrayHasKey('plugins', $versions);
        }
    }
}
