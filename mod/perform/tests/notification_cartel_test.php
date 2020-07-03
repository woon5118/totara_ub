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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Tatsuhiro Kirihara <tatsuhiro.kirihara@totaralearning.com>
 * @package mod_perform
 * @category test
 */

use mod_perform\notification\exceptions\class_key_not_available;
use mod_perform\notification\cartel;

require_once(__DIR__ . '/notification_testcase.php');

class mod_perform_notification_cartel_testcase extends mod_perform_notification_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->mock_loader([
            'mock_one' => [
                'class' => mod_perform_mock_broker_one::class,
                'name' => 'mock #1',
            ],
            'mock_two' => [
                'class' => mod_perform_mock_broker_two::class,
                'name' => 'MOCK #2',
            ],
            'mock_three' => [
                'class' => mod_perform_mock_broker_three::class,
                'name' => 'm0c1< #3',
            ],
        ]);
    }

    public function tearDown(): void {
        $this->reset_loader();
        parent::tearDown();
    }

    public function test_dispatch() {
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $this->create_section_relationships($section);
        $this->create_notification($activity, 'mock_one', false);
        $this->create_notification($activity, 'mock_two', true);

        $cartel = new cartel($activity, $user->id, null);

        mod_perform_mock_broker::reset();
        $cartel->dispatch('mock_one');
        $this->assertEquals(0, (new mod_perform_mock_broker_one())->get_count());
        $this->assertEquals(0, (new mod_perform_mock_broker_two())->get_count());
        $this->assertEquals(0, (new mod_perform_mock_broker_three())->get_count());

        mod_perform_mock_broker::reset();
        $cartel->dispatch('mock_two');
        $this->assertEquals(0, (new mod_perform_mock_broker_one())->get_count());
        $this->assertEquals(1, (new mod_perform_mock_broker_two())->get_count());
        $this->assertEquals(0, (new mod_perform_mock_broker_three())->get_count());

        mod_perform_mock_broker::reset();
        $cartel->dispatch('mock_three');
        $this->assertEquals(0, (new mod_perform_mock_broker_one())->get_count());
        $this->assertEquals(0, (new mod_perform_mock_broker_two())->get_count());
        $this->assertEquals(0, (new mod_perform_mock_broker_three())->get_count());

        try {
            $cartel->dispatch('mock_zero');
            $this->fail('class_key_not_available expected');
        } catch (class_key_not_available $ex) {
        }
    }
}

class mod_perform_mock_broker_one extends mod_perform_mock_broker {
    // nothing to extend
}

class mod_perform_mock_broker_two extends mod_perform_mock_broker {
    // nothing to extend
}

class mod_perform_mock_broker_three extends mod_perform_mock_broker {
    // nothing to extend
}
