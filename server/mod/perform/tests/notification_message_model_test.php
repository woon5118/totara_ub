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
use mod_perform\entities\activity\notification_message as notification_message_entity;
use mod_perform\models\activity\activity;
use mod_perform\models\activity\notification;
use mod_perform\models\activity\notification_message;
use mod_perform\models\activity\notification_recipient;
use mod_perform\models\activity\section;
use totara_core\relationship\relationship;

require_once(__DIR__ . '/notification_testcase.php');

class mod_perform_notification_message_model_testcase extends mod_perform_notification_testcase {
    /** @var activity */
    private $activity;

    /** @var notification */
    private $notification;

    /** @var section */
    private $section1;

    /** @var section */
    private $section2;

    /** @var relationship[] */
    private $relationships1;

    /** @var relationship[] */
    private $relationships2;

    /** @var notification_recipient */
    private $recipient_subject;

    /** @var notification_recipient */
    private $recipient_appraiser;

    public function setUp(): void {
        parent::setUp();
        $this->activity = $this->create_activity();
        $this->section = $this->create_section($this->activity);
        $this->notification = $this->create_notification($this->activity, 'instance_created', true);
        $this->relationships1 = $this->create_section_relationships(
            $this->section,
            [constants::RELATIONSHIP_SUBJECT, constants::RELATIONSHIP_APPRAISER]
        );
        $this->relationships2 = $this->create_section_relationships(
            $this->section,
            [constants::RELATIONSHIP_SUBJECT]
        );
        notification_recipient::create($this->notification, $this->relationships1[0], false);

        $recipients = $this->notification->get_recipients();
        $this->assertCount(2, $recipients);
        $this->recipient_subject = $recipients->find('relationship_id', $this->relationships1[0]->id);
        $this->recipient_appraiser = $recipients->find('relationship_id', $this->relationships1[1]->id);
    }

    public function tearDown(): void {
        $this->activity = $this->notification = $this->section1 = $this->section2 = $this->relationships1
            = $this->relationships2 = $this->recipient_subject = $this->recipient_appraiser = null;
        parent::tearDown();
    }

    public function test_create() {
        notification_message::create($this->recipient_subject, 1111);
        notification_message::create($this->recipient_appraiser, 2222);
        notification_message::create($this->recipient_subject, 3333);
        notification_message::create($this->recipient_appraiser, 4444);
        notification_message::create($this->recipient_subject, 5555);

        $entities = notification_message_entity::repository()->order_by('sent_at')->get()->all();
        $this->assertCount(5, $entities);
        $this->assertEquals(1111, $entities[0]->sent_at);
        $this->assertEquals(2222, $entities[1]->sent_at);
        $this->assertEquals(3333, $entities[2]->sent_at);
        $this->assertEquals(4444, $entities[3]->sent_at);
        $this->assertEquals(5555, $entities[4]->sent_at);
    }
}
