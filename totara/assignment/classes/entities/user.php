<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2018 onwards Totara Learning Solutions LTD
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
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @package tassign_competency
 */

namespace totara_assignment\entities;


use core\orm\entity\entity;

/**
 * User entity
 *
 * @property-read int $id ID
 * @property string $auth
 * @property bool $confirmed
 * @property bool $policyagreed
 * @property bool $deleted
 * @property bool $suspended
 * @property int $mnethostid
 * @property string $username
 * @property string $password
 * @property string $idnumber
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property bool $emailstop
 * @property string $icq
 * @property string $skype
 * @property string $yahoo
 * @property string $aim
 * @property string $msn
 * @property string $phone1
 * @property string $phone2
 * @property string $institution
 * @property string $department
 * @property string $address
 * @property string $city
 * @property string $country
 * @property string $lang
 * @property string $calendartype
 * @property string $theme
 * @property string $timezone
 * @property int $firstaccess
 * @property int $lastaccess
 * @property int $lastlogin
 * @property int $currentlogin
 * @property string $lastip
 * @property string $secret
 * @property int $picture
 * @property string $url
 * @property string $description
 * @property int $descriptionformat
 * @property int $mailformat
 * @property int $maildigest
 * @property bool $maildisplay
 * @property bool $autosubscribe
 * @property bool $trackforums
 * @property int $timecreated
 * @property int $timemodified
 * @property int $trustbitmask
 * @property string $imagealt
 * @property string $lastnamephonetic
 * @property string $firstnamephonetic
 * @property string $middlename
 * @property string $alternatename
 * @property bool $totarasync
 *
 * @method static user_repository repository()
 *
 * @package tassign_competency\entities
 */
class user extends entity {

    public const TABLE = 'user';

    /**
     * Get the logged in user
     *
     * @return user|null
     */
    public static function logged_in() {
        global $USER;

        // User with id = 0 means not logged in as well
        if (!$USER || $USER->id === 0) {
            return null;
        }

        return new static($USER, false);
    }

    public function get_fullname_attribute() {
        return "{$this->firstname} {$this->lastname}";
    }
}
