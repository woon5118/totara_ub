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
use mod_perform\models\activity\activity;
use mod_perform\models\activity\notification;
use mod_perform\models\activity\notification_recipient;
use mod_perform\models\activity\section;
use mod_perform\models\activity\section_relationship;
use mod_perform\notification\broker;
use mod_perform\notification\dealer;
use mod_perform\notification\clock;
use mod_perform\notification\condition;
use mod_perform\notification\factory;
use mod_perform\notification\loader;
use mod_perform\notification\trigger;
use totara_core\relationship\relationship;

abstract class mod_perform_notification_testcase extends advanced_testcase {
    /** @var mod_perform_generator */
    protected $perfgen;

    /** @var phpunit_message_sink */
    private $sink;

    /** @var boolean */
    private $loader_mocked = false;

    public function setUp(): void {
        $this->setAdminUser();
        $this->perfgen = $this->getDataGenerator()->get_plugin_generator('mod_perform');
    }

    public function tearDown(): void {
        $this->perfgen = null;
        if ($this->loader_mocked) {
            $this->reset_loader();
        }
    }

    /**
     * Mock factory::loader.
     *
     * @param array|null $notifications notifications definition or set null to use the default mocked definition
     */
    protected function mock_loader(?array $notifications): void {
        if ($notifications === null) {
            $notifications = [
                'mock_one' => [
                    'class' => mod_perform_mock_broker_one::class,
                    'name' => 'mock #1',
                    'trigger_type' => trigger::TYPE_ONCE,
                ],
                'mock_two' => [
                    'class' => mod_perform_mock_broker_two::class,
                    'name' => 'MOCK #2',
                    'trigger_type' => trigger::TYPE_BEFORE,
                    'trigger_label' => ['clear'],
                    'condition' => mod_perform_mock_condition_fail::class,
                ],
                'mock_three' => [
                    'class' => mod_perform_mock_broker_three::class,
                    'name' => 'm0c1< #3',
                    'trigger_type' => trigger::TYPE_AFTER,
                    'trigger_label' => ['learner'],
                    'condition' => mod_perform_mock_condition_fail::class,
                ],
            ];
        }
        $loader = loader::create($notifications);
        $rp = new ReflectionProperty(factory::class, 'loader');
        $rp->setAccessible(true);
        $rp->setValue(null, $loader);
        $this->loader_mocked = true;
    }

    /**
     * Nullify factory::loader.
     */
    protected function reset_loader(): void {
        $rp = new ReflectionProperty(factory::class, 'loader');
        $rp->setAccessible(true);
        $rp->setValue(null, null);
        $this->loader_mocked = false;
    }

    /**
     * Create an activity for testing.
     *
     * @param array $data
     * @return activity
     */
    protected function create_activity(array $data = []): activity {
        return $this->perfgen->create_activity_in_container($data);
    }

    /**
     * Create a section for testing.
     *
     * @param activity $activity
     * @param array $data
     * @return section
     */
    protected function create_section(activity $activity, array $data = []): section {
        return $this->perfgen->create_section($activity, $data);
    }

    /**
     * Create a notification for testing.
     *
     * @param activity $activity
     * @param string $class_key
     * @param boolean $active
     * @return notification
     */
    protected function create_notification(activity $activity, string $class_key, bool $active = false): notification {
        return notification::create($activity, $class_key, $active);
    }

    /**
     * Create section relationships for testing.
     *
     * @param section $section
     * @param string[]|null $relationships relationship class names or null to default three relationships
     * @return relationship[]
     */
    protected function create_section_relationships(section $section, array $relationships = null): array {
        if ($relationships === null) {
            $relationships = [
                constants::RELATIONSHIP_SUBJECT,
                constants::RELATIONSHIP_APPRAISER,
                constants::RELATIONSHIP_MANAGER,
            ];
        }
        return array_map(function ($idnumber) use ($section) {
            $rel_id = $this->perfgen->get_core_relationship($idnumber)->id;
            return section_relationship::create($section->get_id(), $rel_id, true)->core_relationship;
        }, $relationships);
    }

    protected function get_core_relationship(string $relationclass): relationship {
        return $this->perfgen->get_core_relationship($relationclass);
    }

    /**
     * Activate/deactivate the recipients.
     *
     * @param notification $notification
     * @param boolean[] $relationships array of [relationship_class => active]
     */
    protected function toggle_recipients(notification $notification, array $relationships): void {
        $recipients = $notification->get_recipients();
        foreach ($relationships as $class => $active) {
            $relationship = $this->perfgen->get_core_relationship($class);
            $rel_id = $relationship->id;
            $recipient = $recipients->find('relationship_id', $rel_id);
            /** @var notification_recipient $recipient */
            if ($recipient->get_recipient_id()) {
                $recipient->activate($active);
            } else {
                notification_recipient::create($notification, $relationship, $active);
            }
        }
    }

    /**
     * Start message redirection.
     */
    protected function redirect_messages() {
        if ($this->sink) {
            throw new coding_exception('do not call redirect_messages again');
        }
        $this->sink = $this->redirectMessages();
    }

    /**
     * Finish message redirection and return messages received.
     *
     * @return stdClass[]
     */
    protected function get_messages(): array {
        if (!$this->sink) {
            throw new coding_exception('get_messages is called prior to redirect_messages');
        }
        $this->sink->close();
        $messages = $this->sink->get_messages();
        usort($messages, function ($x, $y) {
            return ((int)$x->useridto <=> (int)$y->useridto) ?: strcmp($x->subject, $y->subject);
        });
        $this->sink = null;
        return $messages;
    }
}

class mod_perform_mock_broker implements broker {
    /** @var integer */
    private static $executed_count = [];

    public function get_count(): int {
        $class = get_class($this);
        return self::$executed_count[$class] ?? 0;
    }

    public static function reset(): void {
        self::$executed_count = [];
    }

    public function get_default_triggers(): array {
        return [];
    }

    public function check_trigger_condition(notification $notification, object $record, clock $clock): bool {
        return false;
    }

    public function execute(dealer $dealer, notification $notification): void {
        $class = get_class($this);
        if (!isset(self::$executed_count[$class])) {
            self::$executed_count[$class] = 0;
        }
        self::$executed_count[$class]++;
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

class mod_perform_mock_clock extends clock {
    /** @var integer */
    private $time;

    public function __construct(int $time) {
        $this->time = $time;
    }

    public function get_time(): int {
        return $this->time;
    }
}

class mod_perform_mock_condition extends condition {
    /** @var boolean */
    private $should_pass = false;

    public function __construct(bool $should_pass) {
        $this->should_pass = $should_pass;
    }

    public function pass(int $base_time): bool {
        return $this->should_pass;
    }
}

class mod_perform_mock_condition_fail extends mod_perform_mock_condition {
    public function __construct() {
        parent::__construct(false);
    }
}

class mod_perform_mock_condition_pass extends mod_perform_mock_condition {
    public function __construct() {
        parent::__construct(true);
    }
}
