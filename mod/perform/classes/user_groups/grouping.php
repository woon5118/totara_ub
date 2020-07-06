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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\user_groups;

use core\entities\cohort;
use core\entities\user;
use core\orm\entity\entity;
use hierarchy_organisation\entities\organisation;
use hierarchy_position\entities\position;

/**
 * Convenience class to handle user grouping operations.
 *
 * TODO: this should be combined with totara_competency/user_groups and put into
 * totara core somewhere.
 */
final class grouping {
    // Convenience enums.
    public const COHORT = 1;
    public const ORG = 2;
    public const POS = 3;
    public const USER = 4;

    /**
     * @var int group id.
     */
    private $id = 0;

    /**
     * @var int group type. One of the enums above.
     */
    private $type = null;

    /**
     * @var string grouping type label for display purposes.
     */
    private $type_label = null;

    /**
     * @var string group name.
     */
    private $name = null;

    /**
     * @var int no of members in the grouping.
     */
    private $size = null;

    /**
     * Get all allowed groupings.
     *
     * @return string[] the allowed groupings.
     */
    public static function get_allowed(): array {
        return [
            self::COHORT,
            self::ORG,
            self::POS,
            self::USER
        ];
    }

    /**
     * Returns the grouping for a given type.
     *
     * @param int $type the group type.
     * @param int $id the group id.
     *
     * @return grouping the grouping.
     */
    public static function by_type(int $type, int $id): grouping {
        switch ($type) {
            case self::COHORT:
                return self::cohort($id);

            case self::ORG:
                return self::org($id);

            case self::POS:
                return self::pos($id);

            case self::USER:
                return self::user($id);
        }

        throw new \coding_exception("Unknown grouping type: '$type'");
    }

    /**
     * Returns the grouping for a cohort.
     *
     * @param int $id the cohort id.
     *
     * @return grouping the grouping.
     */
    public static function cohort(int $id): grouping {
        return new grouping($id, self::COHORT);
    }

    /**
     * Returns the grouping for an organisation.
     *
     * @param int $id the organisation id.
     *
     * @return grouping the grouping.
     */
    public static function org(int $id): grouping {
        return new grouping($id, self::ORG);
    }

    /**
     * Returns the grouping for a position.
     *
     * @param int $id the position id.
     *
     * @return grouping the grouping.
     */
    public static function pos(int $id): grouping {
        return new grouping($id, self::POS);
    }

    /**
     * Returns the grouping for an individual user.
     *
     * @param int $id the user.
     *
     * @return grouping the grouping.
     */
    public static function user(int $id): grouping {
        return new grouping($id, self::USER);
    }

    /**
     * Returns a cohort name.
     *
     * @param int $id the cohort id.
     *
     * @return string the name.
     */
    private static function get_cohort_name(int $id): string {
        $group = cohort::repository()->find($id);
        return $group && $group->display_name ? $group->display_name : '';
    }

    /**
     * Returns an organization name.
     *
     * @param int $id the organization id.
     *
     * @return string the name.
     */
    private static function get_org_name(int $id): string {
        $group = organisation::repository()->find($id);
        return $group && $group->fullname ? $group->fullname : $group->shortname;
    }

    /**
     * Returns a position name.
     *
     * @param int $id the position id.
     *
     * @return string the name.
     */
    private static function get_pos_name(int $id): string {
        $group = position::repository()->find($id);
        return $group && $group->fullname ? $group->fullname : $group->shortname;
    }

    /**
     * Returns a user name.
     *
     * @param int $id the user id.
     *
     * @return string the name.
     */
    private static function get_user_name(int $id): string {
        $group = user::repository()->find($id);
        return $group ? $group->fullname : '';
    }

    /**
     * Default constructor.
     *
     * @param int $id group id.
     * @param int $type grouping type.
     */
    private function __construct(int $id, int $type) {
        $this->id = $id;
        $this->type = $type;

        $lang_string_key = null;
        switch ($type) {
            case self::COHORT:
                $lang_string_key = 'user_group_assignment_group_cohort';
                break;

            case self::ORG:
                $lang_string_key = 'user_group_assignment_group_org';
                break;

            case self::POS:
                $lang_string_key = 'user_group_assignment_group_pos';
                break;

            case self::USER:
                $lang_string_key = 'user_group_assignment_group_user';
                break;
        }

        $this->type_label = get_string($lang_string_key, 'mod_perform');
    }

    /**
     * Returns the group id.
     *
     * @return int the id.
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Returns the group type.
     *
     * @return int the type.
     */
    public function get_type(): int {
        return $this->type;
    }

    /**
     * Returns the group type display label.
     *
     * @return string the label.
     */
    public function get_type_label(): string {
        return $this->type_label;
    }

    /**
     * Returns the group's name.
     *
     * @return string the short name.
     */
    public function get_name(): string {
        if (is_null($this->name)) {
            if ($this->type === self::COHORT) {
                $this->name = self::get_cohort_name($this->id);
            }

            if ($this->type === self::ORG) {
                $this->name = self::get_org_name($this->id);
            }

            if ($this->type === self::POS) {
                $this->name = self::get_pos_name($this->id);
            }

            if ($this->type === self::USER) {
                $this->name = self::get_user_name($this->id);
            }
        }

        return $this->name;
    }

    /**
     * Returns the number of members in this group.
     *
     * @return int the group size.
     */
    public function get_size(): int {
        if (is_null($this->size)) {
            if ($this->type === self::USER) {
                $this->size = 1;
            } else {
                $entity = self::get_entity_class_by_user_group_type($this->type);
                $this->size = count($entity::expand_multiple([$this->id]));
            }
        }

        return $this->size;
    }

    /**
     * Get entity class by user group type
     *
     * @param string $type
     * @return string|entity
     */
    public static function get_entity_class_by_user_group_type(string $type): string {
        switch ($type) {
            case self::USER:
                $class_name = user::class;
                break;
            case self::COHORT:
                $class_name = cohort::class;
                break;
            case self::POS:
                $class_name = position::class;
                break;
            case self::ORG:
                $class_name = organisation::class;
                break;
            default:
                $class_name = null;
                break;
        }

        if (!class_exists($class_name)) {
            throw new \coding_exception('Invalid entity found!');
        }

        return $class_name;
    }
}
