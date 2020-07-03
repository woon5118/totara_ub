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
use mod_perform\models\activity\activity as activity_model;
use mod_perform\models\activity\notification as notification_model;
use mod_perform\notification\exceptions\class_key_not_available;
use mod_perform\task\service\subject_instance_dto;
use totara_core\relationship\relationship;

/**
 * factory class
 */
abstract class factory {
    /** @var loader|null */
    protected static $loader;

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
     * Create a cartel instance.
     *
     * @param subject_instance_dto $dto
     * @return cartel
     */
    public static function create_cartel(subject_instance_dto $dto): cartel {
        $activity = activity_model::load_by_id($dto->get_activity_id());
        $user_id = $dto->subject_user_id;
        $job_assignment_id = $dto->job_assignment_id;
        return new cartel($activity, $user_id, $job_assignment_id);
    }

    /**
     * Create a dealer instance.
     *
     * @param notification_model $notification
     * @param integer $user_id
     * @param integer|null $job_assignment_id
     * @return dealer
     */
    public static function create_dealer(notification_model $notification, int $user_id, ?int $job_assignment_id): dealer {
        $activity = $notification->get_activity();
        $recipients = $notification->get_recipients(true);
        $composer = self::create_composer($notification->class_key);
        return new dealer($activity, $recipients, $composer, $user_id, $job_assignment_id);
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
}
