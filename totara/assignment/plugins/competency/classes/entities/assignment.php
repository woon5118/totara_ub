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

namespace tassign_competency\entities;


use core\orm\entity\entity;
use core\orm\entity\relations\belongs_to;

/**
 * Assignment entity
 *
 * @property string $type assignment type
 * @property int $competency_id Competency ID
 * @property string $user_group_type Type of a linked user group
 * @property int $user_group_id ID of a linked user group
 * @property bool $optional Optional flag
 * @property int $status Assignment status eg (0 - draft, 1 - published, etc)
 * @property-read string progress_name Assignment name
 * @property-read string human_status Human readable assignment status
 * @property int $created_by ID of the created user
 * @property int $created_at Created at timestamp
 * @property int $updated_at Updated at timestamp
 * @property int $archived_at Archived at timestamp
 *
 * @property-read string $status_name Textual representation of status int
 *
 * @method static assignment_repository repository()
 *
 * @package tassign_competency\entities
 */
class assignment extends entity {

    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ARCHIVED = 2;

    const STATUS_NAME_DRAFT = 'draft';
    const STATUS_NAME_ACTIVE = 'active';
    const STATUS_NAME_ARCHIVED = 'archived';

    // assigned by admin users via the interface
    const TYPE_ADMIN = 'admin';
    // assigned by the user themselves
    const TYPE_SELF = 'self';
    // assigend by other users, like managers, for a user
    const TYPE_OTHER = 'other';
    // assigned automatically by the system due to the continuous tracking functionality
    const TYPE_SYSTEM = 'system';

    // Assignments to account for pre-perform achievements, archived when created...
    const TYPE_LEGACY = 'legacy';

    public const TABLE = 'totara_assignment_competencies';

    protected $extra_attributes = [
        'status_name',
    ];

    public const CREATED_TIMESTAMP = 'created_at';
    public const UPDATED_TIMESTAMP = 'updated_at';
    public const SET_UPDATED_WHEN_CREATED = true;

    /**
     * @return array
     */
    public static function get_available_types() {
        return [
            self::TYPE_ADMIN,
            self::TYPE_SELF,
            self::TYPE_OTHER,
            self::TYPE_SYSTEM
        ];
    }

    public function competency(): belongs_to {
        return $this->belongs_to(\totara_competency\entities\competency::class, 'competency_id');
    }

    /**
     * Get status attribute
     *
     * @param int $status
     * @return int
     */
    public function get_status_attribute($status = 0): int {
        return (int) $status;
    }

    /**
     * Get status name attribute
     *
     * @return string
     */
    public function get_status_name_attribute(): string {
        switch ($this->status) {
            case assignment::STATUS_DRAFT:
                $name = self::STATUS_NAME_DRAFT;
                break;
            case assignment::STATUS_ACTIVE:
                $name = self::STATUS_NAME_ACTIVE;
                break;
            case assignment::STATUS_ARCHIVED:
                $name = self::STATUS_NAME_ARCHIVED;
                break;
            default:
                throw new \coding_exception("Unknown assignment status '{$this->status}'");
                break;
        }
        return $name;
    }

    // TODO This is part of the model, existing code should be changed
    protected function get_human_status_attribute() {
        switch ($this->status) {
            case assignment::STATUS_ACTIVE:
                return get_string('status:active-alt', 'tassign_competency');
            case assignment::STATUS_ARCHIVED:
                return get_string('status:archived-alt', 'tassign_competency');
            case assignment::STATUS_DRAFT:
                return get_string('status:draft', 'tassign_competency');
            default:
                debugging('Unknown assignment status: ' . $this->status, DEBUG_DEVELOPER);
                return 'Unknown';
        }
    }

}
