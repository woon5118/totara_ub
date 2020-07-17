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
 */

defined('MOODLE_INTERNAL') || die();

// Please do not include this file and parse the contents!
// Instead, use the loader instance returned by mod_perform\notification\factory::create_loader().

$notifications = [
    'instance_created' => [
        'name' => ['notification_broker_instance_created', 'mod_perform'],
        'class' => \mod_perform\notification\brokers\instance_created::class,
        'trigger_type' => \mod_perform\notification\trigger::TYPE_UNSUPPORTED,
    ],
    'instance_created_reminder' => [
        'name' => ['notification_broker_instance_created_reminder', 'mod_perform'],
        'class' => \mod_perform\notification\brokers\instance_created_reminder::class,
        'trigger_type' => \mod_perform\notification\trigger::TYPE_AFTER,
        'trigger_label' => ['notification_trigger_instance_creation', 'mod_perform'],
    ],
    'due_date_reminder' => [
        'name' => ['notification_broker_due_date_reminder', 'mod_perform'],
        'class' => \mod_perform\notification\brokers\due_date_reminder::class,
        'trigger_type' => \mod_perform\notification\trigger::TYPE_BEFORE,
        'trigger_label' => ['notification_trigger_duedate', 'mod_perform'],
    ],
    'due_date' => [
        'name' => ['notification_broker_due_date', 'mod_perform'],
        'class' => \mod_perform\notification\brokers\due_date::class,
        'trigger_type' => \mod_perform\notification\trigger::TYPE_UNSUPPORTED,
    ],
    'overdue_reminder' => [
        'name' => ['notification_broker_overdue_reminder', 'mod_perform'],
        'class' => \mod_perform\notification\brokers\overdue_reminder::class,
        'trigger_type' => \mod_perform\notification\trigger::TYPE_AFTER,
        'trigger_label' => ['notification_trigger_duedate', 'mod_perform'],
    ],
    'completion' => [
        'name' => ['notification_broker_completion', 'mod_perform'],
        'class' => \mod_perform\notification\brokers\overdue_reminder::class,
        'trigger_type' => \mod_perform\notification\trigger::TYPE_UNSUPPORTED,
    ],
];
