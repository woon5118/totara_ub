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
 * @author Johannes Cilliers <johannes.cilliers@totaralearning.com>
 * @package totara_engage
 */

namespace core_user\totara_engage\share\recipient;

use totara_engage\access\access;
use totara_engage\entity\share as share_entity;
use totara_engage\repository\share_repository;
use totara_engage\share\recipient\helper as recipient_helper;
use totara_engage\share\recipient\recipient;
use totara_engage\share\shareable;
use totara_job\job_assignment;

class user extends recipient {

    /**
     * Area identifying this recipient.
     */
    public const AREA = 'USER';

    /**
     * @inheritDoc
     */
    public function validate(): void {
        // Make sure that this is a valid user.
        if (!\core_user::is_real_user($this->instanceid, true)) {
            throw new \coding_exception("Invalid user with ID {$this->instanceid}");
        }
    }

    /**
     * @inheritDoc
     */
    public function get_label(): string {
        return get_string('users', 'moodle');
    }

    /**
     * @inheritDoc
     */
    public function get_summary(): string {
        $ja = job_assignment::get_first($this->instanceid, false);
        return $ja ? $ja->fullname : '';
    }

    /**
     * @inheritDoc
     */
    public function get_data() {
        return \core_user::get_user($this->instanceid);
    }

    /**
     * @inheritDoc
     */
    public function get_minimum_access(): int {
        return access::RESTRICTED;
    }

    /**
     * @inheritDoc
     */
    public static function search(string $search, ?shareable $instance): array {
        global $USER;
        [$contacts, $courses, $noncontacts] = \core_message\api::search_users($USER->id, $search, 20);
        $users = array_merge($contacts, $noncontacts);

        $recipients = [];
        foreach ($users as $user) {
            // Exclude the shareable item owner.
            if (!empty($instance)) {
                if ((int)$user->userid === $instance->get_userid()) {
                    continue;
                }
            }

            $recipients[] = new self($user->userid);
        }

        return $recipients;
    }

    /**
     * @inheritDoc
     */
    public static function is_user_permitted(shareable $instance, int $user_id): bool {
        // If this user is a recipient of this share then the user should be permitted.
        /** @var share_repository $repo */
        $repo = share_entity::repository();
        return $repo->is_recipient(
            $instance->get_id(),
            $instance::get_resource_type(),
            $user_id,
            self::AREA,
            recipient_helper::get_component(static::class)
        );
    }

}