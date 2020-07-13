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
use mod_perform\notification\exceptions\class_key_not_available;

/**
 * The loader class.
 */
class loader {
    /** @var array */
    private $notifications;

    /** @var array */
    private static $mandatory_fields = [
        'class',
        'name',
    ];

    /**
     * Private constructor to enforce the factory pattern.
     *
     * @param array $notifications
     */
    private function __construct(array $notifications) {
        self::validate($notifications);
        $this->notifications = $notifications;
    }

    /**
     * Validate the notifications array.
     *
     * @param array $notifications
     */
    private static function validate(array $notifications): void {
        if (empty($notifications)) {
            throw new coding_exception('notification data is empty');
        }
        foreach ($notifications as $class_key => $notif) {
            foreach (self::$mandatory_fields as $mandatory_field) {
                if (!isset($notif[$mandatory_field])) {
                    throw new coding_exception("{$mandatory_field} is missing for {$class_key}");
                }
            }
        }
    }

    /**
     * Create a loader instance.
     *
     * @param array|null $notifications array or null to load from notifications.php
     * @return self
     */
    public static function create(?array $notifications = null): self {
        global $CFG;
        if ($notifications === null) {
            require($CFG->dirroot . '/mod/perform/db/notifications.php');
        }
        return new self($notifications);
    }

    /**
     * Get all the broker class names.
     *
     * @return array of [class_key => fully_qualified_class_path]
     */
    public function get_classes(): array {
        return array_map(function ($notif) {
            return $notif['class'];
        }, $this->notifications);
    }

    /**
     * Get the fully-qualified class name of the broker.
     *
     * @param string $class_key
     * @return string
     * @throws coding_exception
     */
    public function get_class_of(string $class_key): string {
        $info = $this->get_information($class_key);
        return $info['class'];
    }

    /**
     * Get the localised name of the broker.
     *
     * @param string $class_key
     * @return string
     * @throws coding_exception
     */
    public function get_name_of(string $class_key): string {
        $info = $this->get_information($class_key);
        return get_string($info['name'][0], $info['name'][1] ?? '');
    }

    /**
     * Get the type of event triggers.
     *
     * @param string $class_key
     * @return integer one of trigger constants
     */
    public function get_trigger_type_of(string $class_key): int {
        $info = $this->get_information($class_key);
        return $info['trigger_type'] ?? trigger::TYPE_UNSUPPORTED;
    }

    /**
     * Return whether a broker can provide trigger events.
     *
     * @param string $class_key
     * @return boolean
     */
    public function support_triggers(string $class_key): bool {
        return $this->get_trigger_type_of($class_key) !== trigger::TYPE_UNSUPPORTED;
    }

    /**
     * Throw an exception if the class key is not available.
     *
     * @param string $class_key
     * @throws class_key_not_available
     */
    public function ensure_class_key_exists(string $class_key): void {
        if (!isset($this->notifications[$class_key])) {
            throw new class_key_not_available($class_key);
        }
    }

    /**
     * @param string $class_key
     * @return array
     * @throws class_key_not_available
     */
    private function get_information(string $class_key): array {
        $this->ensure_class_key_exists($class_key);
        return $this->notifications[$class_key];
    }
}
