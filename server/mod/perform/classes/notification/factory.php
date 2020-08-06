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

namespace mod_perform\notification;

use coding_exception;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\entities\activity\participant_instance as participant_instance_entity;
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\models\activity\participant_instance;
use mod_perform\task\service\participant_instance_dto;
use mod_perform\task\service\subject_instance_dto;
use stdClass;

/**
 * factory class
 */
abstract class factory {
    /** @var loader|null */
    protected static $loader;

    /** @var clock|null */
    protected static $clock;

    /**
     * Create a broker instance.
     *
     * @param string $class_key
     * @return broker
     * @throws coding_exception
     */
    public static function create_broker(string $class_key): broker {
        $class = self::create_loader()->get_class_of($class_key);
        $inst = new $class();
        return $inst;
    }

    /**
     * Create a trigger instance.
     *
     * @param notification_model $notification
     * @return trigger
     */
    public static function create_trigger(notification_model $notification): trigger {
        return new trigger($notification->get_class_key());
    }

    /**
     * Create a condition instance.
     *
     * @param notification_model $notification
     * @return condition
     */
    public static function create_condition(notification_model $notification): condition {
        $class_key = $notification->get_class_key();
        $condition_class = self::create_loader()->get_condition_class_of($class_key);
        return new $condition_class(self::create_clock(), $notification->get_triggers_in_seconds(), $notification->last_run_at);
    }

    /**
     * Create a cartel instance.
     *
     * @param integer|subject_instance_dto $subject_instance
     * @return cartel
     */
    public static function create_cartel_on_subject_instance($subject_instance): cartel {
        if ($subject_instance instanceof subject_instance_dto) {
            $subject_instance = $subject_instance->id;
        }
        $participant_instances = participant_instance_entity::repository()->where('subject_instance_id', $subject_instance)->get()->all();
        return self::create_cartel_on_participant_instances($participant_instances);
    }

    /**
     * Create a cartel instance.
     *
     * @param (integer|participant_instance_dto|participant_instance|participant_instance_entity|stdClass)[] $participant_instances
     * @return cartel
     */
    public static function create_cartel_on_participant_instances(array $participant_instances): cartel {
        $ids = array_map(function ($e, $i) {
            if ($e instanceof participant_instance_dto) {
                return $e->get_id();
            }
            if ($e instanceof participant_instance) {
                return $e->get_id();
            }
            if ($e instanceof participant_instance_entity) {
                return $e->id;
            }
            if ($e instanceof stdClass) {
                return $e->id;
            }
            if (is_int($e)) {
                return $e;
            }
            throw new coding_exception('unknown element at ' . $i);
        }, $participant_instances, array_keys($participant_instances));
        /** @var integer[] $ids */
        return new cartel($ids);
    }

    /**
     * Create a dealer instance.
     *
     * @param notification_model $notification
     * @return dealer|null The dealer instance or null if the notification cannot be sent
     */
    public static function create_dealer_on_notification(notification_model $notification): ?dealer {
        if ($notification->active) {
            $dealer = new dealer($notification);
            // Optimise out when no recipients are enabled.
            if ($dealer->has_recipients()) {
                return $dealer;
            }
        }
        return null;
    }

    /**
     * Create a composer instance.
     *
     * @param string $class_key
     * @return composer
     */
    public static function create_composer(string $class_key): composer {
        self::create_loader()->ensure_class_key_exists($class_key);
        return new composer($class_key);
    }

    /**
     * Return the loader instance.
     *
     * @return loader
     * @throws coding_exception
     */
    public static function create_loader(): loader {
        if (self::$loader === null) {
            self::$loader = loader::create();
        }
        return self::$loader;
    }

    /**
     * Return the master clock.
     *
     * @return clock
     */
    public static function create_clock(): clock {
        if (self::$clock === null) {
            self::$clock = new clock();
        }
        return self::$clock;
    }

    /**
     * Return the master clock for testing.
     *
     * @param integer $bias time offset in seconds; (NOTE: bias is cumulative)
     * @return clock
     */
    public static function create_clock_with_time_offset(int $bias): clock {
        $current_bias = get_config('mod_perform', 'notification_time_travel') ?: 0;
        $new_bias = $current_bias + $bias;
        set_config('notification_time_travel', $new_bias, 'mod_perform');
        // Always override the singleton instance.
        self::$clock = new clock();
        return self::$clock;
    }
}
