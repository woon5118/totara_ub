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
 * @author  Chris Snyder <chris.snyder@totaralearning.com>
 * @package core_virtualmeeting
 */

namespace totara_core\virtualmeeting\dto;

use coding_exception;
use core_date;
use DateTime;
use totara_core\entity\virtual_meeting;
use totara_core\virtualmeeting\user_auth;

/**
 * A simple readonly object to transfer data from one service to another.
 *
 * This avoids a full entity to be passed down via hooks to watchers we might not
 * have control over.
 *
 * @property-read int $id virtualmeeting id
 * @property-read int $userid totara user id
 * @property-read string $name meeting name
 * @property-read DateTime $timestart meeting start time with timezone
 * @property-read DateTime $timefinish meeting end time with timezone
 * @property-read user_auth $user user authentication
 *
 * @internal Do *NOT* instantiate this class
 */
class meeting_edit_dto extends meeting_dto {

    /** @var string */
    protected $name;

    /** @var DateTime */
    protected $timestart;

    /** @var DateTime */
    protected $timefinish;

    /**
     * Constructor.
     *
     * @param virtual_meeting $entity
     * @param string $name meeting name or summary
     * @param DateTime $timestart meeting start time
     * @param DateTime $timefinish meeting end time
     */
    public function __construct(virtual_meeting $entity, string $name, DateTime $timestart, DateTime $timefinish) {
        parent::__construct($entity);
        if ($timestart >= $timefinish){
            throw new coding_exception('timefinish cannot precede timestart');
        }
        $this->name = $name;
        $this->timestart = $timestart;
        $this->timefinish = $timefinish;
    }

    /**
     * Create an instance with the current user and their time zone.
     *
     * @param virtual_meeting $entity
     * @param string $name meeting name or summary
     * @param integer $timestart meeting start time
     * @param integer $timefinish meeting end time
     * @param mixed $timezone meeting time zone in Totara format
     * @return self
     * @todo Do not use this function until TL-29074 lands
     */
    public static function create_with_user_timezone(virtual_meeting $entity, string $name, int $timestart, int $timefinish, $timezone): self {
        if ($timezone === null || $timezone == 99) {
            if (isset($entity->user->timezone)) {
                $timezone = $entity->user->timezone;
            } else {
                $timezone = core_date::get_server_timezone_object();
            }
        }
        $tz = core_date::get_user_timezone_object($timezone);
        return new self($entity, $name, new DateTime('@'.$timestart, $tz), new DateTime('@'.$timefinish, $tz));
    }

    /**
     * Get meeting name
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get meeting start time
     *
     * @return DateTime
     */
    public function get_timestart(): DateTime {
        return $this->timestart;
    }

    /**
     * Get meeting end time
     *
     * @return DateTime
     */
    public function get_timefinish(): DateTime {
        return $this->timefinish;
    }
}
