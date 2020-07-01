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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package totara_core
 */

namespace totara_core\dates;

use core_date;
use DateTimeZone;

/**
 * This class represents a date time setting which may or may not include a timezone.
 * If no timezone is supplied the user timezone is a assumed.
 *
 * @package totara_core\dates
 */
class date_time_setting {

    public const ISO_NO_OFFSET = 'Y-m-d\TH:i:s';

    /**
     * @var int|null
     */
    protected $timestamp;

    /**
     * @var DateTimeZone|null
     */
    protected $timezone;

    /**
     * date_time_setting constructor.
     *
     * @param int|null $timestamp Unix timestamp
     * @param string|null $timezone Timezone string
     */
    public function __construct(?int $timestamp = null, ?string $timezone = null) {
        $this->timestamp = $timestamp;
        $this->timezone = $timezone ?? core_date::get_user_timezone();
    }

    /**
     * Get now, with the server timezone.
     *
     * @return static
     */
    public static function now(): self {
        return new static(time(), core_date::get_server_timezone());
    }

    /**
     * Factory function to create from graphql input parameter.
     *
     * @param array|null $data
     * @return static
     */
    public static function create_from_array(?array $data): self {
        if ($data === null) {
            return new static();
        }

        if (!isset($data['iso'])) {
            throw new \coding_exception('iso must be supplied');
        }

        $timezone = $data['timezone'] ?? core_date::get_server_timezone();

        static::check_timezone($timezone);

        $date_timezone = new DateTimeZone($timezone);
        $date_time = (new \DateTimeImmutable($data['iso'], $date_timezone));

        return new static($date_time->getTimestamp(), $timezone);
    }

    private static function check_timezone(?string $timezone): void {
        try {
            new DateTimeZone($timezone);
        } catch (\Exception $e) {
            throw new \coding_exception('Invalid timezone supplied');
        }
    }

    /**
     * @return int Unix timestamp representation of the setting.
     */
    public function get_timestamp(): int {
        return $this->timestamp;
    }

    /**
     * @return string An ISO 8601 formatted date string without the offset, in the supplied timezone.
     */
    public function get_iso(): string {
        $date = new \DateTime("@{$this->timestamp}", new \DateTimeZone('UTC'));
        $date->setTimezone(new \DateTimeZone($this->timezone));

        return $date->format(self::ISO_NO_OFFSET);
    }

    /**
     * @return string
     */
    public function get_timezone(): string {
        return $this->timezone;
    }

}