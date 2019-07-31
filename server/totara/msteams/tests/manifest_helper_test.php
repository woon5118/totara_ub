<?php
/**
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
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package totara_msteams
 */

use totara_msteams\manifest_helper;

defined('MOODLE_INTERNAL') || die;

/**
 * Test manifest_helper class.
 */
class totara_msteams_manifest_helper_testcase extends advanced_testcase {
    public function test_is_valid_guid() {
        $cases = [
            [ false, false ],
            [ 1, false ],
            [ '', false ],
            [ 'kia ora', false ],
            [ '00000000-0000-0000-0000-000000000000', false ],
            [ '0000000n-0000-0000-0000-000000000000', false ],
            [ '00000000-0z00-0000-0000-000000000000', false ],
            [ '00000000-0000-00N0-0000-000000000000', false ],
            [ '00000000-0000-0000-000Z-000000000000', false ],
            [ '00000000-0000-0000-0000-00000000000!', false ],
            [ '{31415926-5358-9793-2384-626433832795}', false ],
            [ '31415926535897932384626433832795', false ],
            [ '31415926-5358-97932384-6264-33832795', false ],
            [ ' 31415926-5358-9793-2384-626433832795', false ],
            [ '31415926-5358-9793-2384-626433832795 ', false ],
            [ '31415926-5358-9793-2384-626433832795', true ],
            [ '82baDFa5-7f00-DfEe-151C-C0600dc0fFEe', true ],
        ];

        foreach ($cases as [$input, $expected]) {
            $this->assertEquals($expected, manifest_helper::is_valid_guid($input));
        }
    }
}
