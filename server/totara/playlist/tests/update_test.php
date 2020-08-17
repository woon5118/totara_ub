<?php
/**
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
 * @author Qingyang Liu <qingyang.liu@totaralearning.com>
 * @package totara_playlist
 */

defined('MOODLE_INTERNAL') || die();
use totara_playlist\playlist;

class totara_playlist_update_testcase extends advanced_testcase {

    /**
     * @return void
     */
    public function test_update_playlist(): void {
        global $DB;

        $gen = $this->getDataGenerator();
        $user = $gen->create_user();

        // Login as owner.
        $this->setUser($user);
        $playlist = playlist::create('Hello world');

        $this->assertTrue($DB->record_exists('playlist', ['id' => $playlist->get_id()]));
        $this->assertEquals((int)$user->id, $playlist->get_userid());

        $playlist->set_name('change by owner');
        $playlist->update();
        $this->assertEquals('change by owner', $playlist->get_name());

        // Login as admin.
        $this->setAdminUser();
        $playlist->set_name('change by admin');
        $playlist->update();
        $this->assertEquals('change by admin', $playlist->get_name());
    }
}