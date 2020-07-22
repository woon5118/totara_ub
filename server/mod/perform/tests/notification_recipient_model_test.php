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

use mod_perform\constants;
use mod_perform\models\activity\notification;
use mod_perform\models\activity\notification_recipient;

require_once(__DIR__ . '/notification_testcase.php');

class mod_perform_notification_recipient_model_testcase extends mod_perform_notification_testcase {
    public function test_create() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = notification::create($activity, 'instance_created');
        $relationships = $this->create_section_relationships($section);
        notification_recipient::create($notification, $relationships[0], false);
        notification_recipient::create($notification, $relationships[1], true);
        $this->assertFalse($notification->recipients->find('relationship_id', $relationships[0]->id)->active);
        $this->assertTrue($notification->recipients->find('relationship_id', $relationships[1]->id)->active);
        $this->assertFalse($notification->recipients->find('relationship_id', $relationships[2]->id)->active);
    }

    public function test_load_by_notification_full() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = notification::create($activity, 'instance_created');
        $relationships = $this->create_section_relationships($section);
        notification_recipient::create($notification, $relationships[0], false);
        notification_recipient::create($notification, $relationships[1], true);

        $this->assertCount(3, notification_recipient::load_by_notification($notification, false));
        $this->assertCount(1, notification_recipient::load_by_notification($notification, true));
    }

    public function test_load_by_notification_partial() {
        $activity = $this->create_activity();
        $section = $this->create_section($activity);
        $notification = notification::create($activity, 'instance_created');
        $relationships = $this->create_section_relationships(
            $section,
            [constants::RELATIONSHIP_SUBJECT, constants::RELATIONSHIP_APPRAISER]
        );
        notification_recipient::create($notification, $relationships[0], false);

        $this->assertCount(2, notification_recipient::load_by_notification($notification, false));
        $this->assertCount(0, notification_recipient::load_by_notification($notification, true));
    }
}
