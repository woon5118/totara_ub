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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package core
 */

namespace core\entities;

use core\orm\entity\repository;
use user_picture;

class user_repository extends repository {

    /**
     * Filter only users not marked as deleted
     *
     * @return $this
     */
    public function filter_by_not_deleted(): self {
        $this->where('deleted', 0);

        return $this;
    }

    /**
     * Only real users
     *
     * @return $this
     */
    public function filter_by_not_guest(): self {
        $this->where('username', '!=', 'guest');

        return $this;
    }

    /**
     * Remove the current logged in user from the query.
     *
     * @return $this
     */
    public function filter_by_not_current_user(): self {
        $current_user = user::logged_in();

        if (!$current_user) {
            throw new \coding_exception('There must be a user logged in otherwise you can not use ' . __FUNCTION__ . '()!');
        }

        $this->where('id', '!=', $current_user->id);

        return $this;
    }

    /**
     * Search for users who's full names include a given string.
     * Note: This excludes guest users and deleted users.
     *
     * @param string $search_for Part of a user's name we are wanting to search for.
     *
     * @return $this
     */
    public function filter_by_full_name(string $search_for): self {
        $this->where_raw(...users_search_sql($search_for, $this->get_alias_sql()));

        return $this;
    }

    /**
     * Select only display name of the user for display purposes
     *
     * @return $this
     */
    public function select_full_name_fields_only(): self {
        $fields = $this->get_user_full_name_fields();

        $this
            ->add_select('id')
            ->add_select($fields)
            ->group_by(array_merge(['id'], $fields));

        return $this;
    }

    /**
     * Select fields required to display each user's profile picture.
     *
     * @return $this
     */
    public function select_user_picture_fields(): self {
        $fields = user_picture::fields($this->get_alias_sql());

        $this
            ->add_select_raw($fields)
            ->group_by_raw($fields);

        return $this;
    }

    /**
     * Order by the full name of the users.
     *
     * @return $this
     */
    public function order_by_full_name(): self {
        foreach ($this->get_user_full_name_fields() as $field) {
            $this->order_by($field);
        }

        return $this;
    }

    /**
     * Get the fields required for a user's full name.
     *
     * @return string[]
     */
    private function get_user_full_name_fields(): array {
        return totara_get_all_user_name_fields(false, $this->get_alias_sql(), null, null, true);
    }

}
