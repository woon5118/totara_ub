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

use mod_perform\models\activity\activity;
use mod_perform\models\activity\notification;
use mod_perform\models\activity\notification_recipient;
use mod_perform\models\notification\broker;
use mod_perform\models\notification\brokers\instance_created;

class mod_perform_notification_recipient_model_testcase extends advanced_testcase {
    /** @var mod_perform_generator */
    private $perfgen;

    public function setUp(): void {
        $this->setAdminUser();
        $this->perfgen = $this->getDataGenerator()->get_plugin_generator('mod_perform');
    }

    public function tearDown(): void {
        $this->perfgen = null;
    }

    /**
     * Create an activity for testing.
     *
     * @param array $data
     * @return activity
     */
    public function create_test_activity(array $data = []): activity {
        return $this->perfgen->create_activity_in_container($data);
    }

    public function test_create() {
        $activity = $this->create_test_activity();
        $notification = notification::create($activity, 'instance_created');
        $this->markTestIncomplete('do it later');
        // TODO: add tests
        // $notification->recipients;
    }
}
