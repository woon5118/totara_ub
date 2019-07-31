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
 * @author Kian Nguyen <kian.nguyen@totaralearning.com>
 * @package container_workspace
 */
namespace container_workspace\tracker;

use container_workspace\workspace;
use core\entity\user_last_access;
use core\orm\query\builder;
use core\orm\query\order;
use core_container\factory;

/**
 * A class to keep track the last workspace that a user visited
 */
final class tracker {
    /**
     * @var int
     */
    private $user_id;

    /**
     * tracker constructor.
     * @param int|null $user_id
     */
    public function __construct(?int $user_id = null) {
        global $USER;

        if (null === $user_id || 0 === $user_id) {
            $user_id = $USER->id;
        }

        $this->user_id = $user_id;
    }

    /**
     * @return void
     */
    public static function clear_all(): void {
        global $DB;
        $sql = '
            SELECT DISTINCT c.id FROM "ttr_user_lastaccess" ul
            INNER JOIN "ttr_course" c ON c.id = ul.courseid AND c.containertype = :workspace_type
        ';

        $ids = $DB->get_fieldset_sql($sql, ['workspace_type' => workspace::get_type()]);
        foreach ($ids as $id) {
            $DB->delete_records(user_last_access::TABLE, ['id' => $id]);
        }
    }

    /**
     * @param int $workspace_id
     * @return void
     */
    public function clear(int $workspace_id): void {
        global $DB;
        $DB->delete_records(
            user_last_access::TABLE,
            [
                'courseid' => $workspace_id,
                'userid' => $this->user_id
            ]
        );
    }

    /**
     * @param workspace $workspace
     * @return void
     */
    public function visit_workspace(workspace $workspace): void {
        $workspace_id = $workspace->get_id();
        $builder = builder::table(user_last_access::TABLE);

        $builder->where('userid', $this->user_id);
        $builder->where('courseid', $workspace_id);
        $builder->map_to(user_last_access::class);

        /** @var user_last_access $last_access */
        $last_access = $builder->one();

        if (null !== $last_access) {
            $last_access->timeaccess = time();
            $last_access->save();

            return;
        }

        $last_access = new user_last_access();
        $last_access->userid = $this->user_id;
        $last_access->courseid = $workspace_id;
        $last_access->timeaccess = time();

        $last_access->save();
    }

    /**
     * @return int|null
     */
    public function get_last_visit_workspace(): ?int {
        global $CFG;

        $builder = builder::table(user_last_access::TABLE, 'ul');
        $builder->join(
            ['course', 'c'],
            function (builder $builder): void {
                $builder->where_field('c.id', 'ul.courseid');
                $builder->where('c.containertype', workspace::get_type());
            }
        );

        $builder->where('ul.userid', $this->user_id);

        $builder->order_by('ul.timeaccess', order::DIRECTION_DESC);
        $builder->map_to(user_last_access::class);

        if (!empty($CFG->tenantsenabled)) {
            require_once("{$CFG->dirroot}/totara/coursecatalog/lib.php");

            [$where_sql, $where_parameters] = totara_visibility_where(
                $this->user_id,
                'c.id',
                'c.visible',
                'c.audiencevisible',
                'c'
            );

            $builder->where_raw($where_sql, $where_parameters);
        }

        /** @var user_last_access $last_access */
        $last_access = $builder->first();
        if (null === $last_access) {
            return null;
        }

        return $last_access->courseid;
    }

    /**
     * @param int $workspace_id
     * @return void
     */
    public static function clear_all_for_workspace(int $workspace_id): void {
        $workspace = factory::from_id($workspace_id);
        if (!$workspace->is_typeof(workspace::get_type())) {
            // Prevent from the usage of this function on different course.
            throw new \coding_exception("Cannot find the workspace with the given id");
        }

        $builder = builder::table(user_last_access::TABLE);
        $builder->where('courseid', $workspace_id);

        $builder->delete();
    }
}