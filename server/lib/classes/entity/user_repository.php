<?php
/**
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Aleksandr Baishev <aleksandr.baishev@totaralearning.com>
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package core
 */

namespace core\entity;

use context;
use core\orm\collection;
use core\orm\entity\filter\basket;
use core\orm\entity\filter\in;
use core\orm\entity\filter\user_name;
use core\orm\entity\repository;
use core\orm\query\builder;
use core\orm\query\field;
use core\tenant_orm_helper;
use core_user\profile\display_setting;
use user_picture;

class user_repository extends repository {

    /**
     * @return array
     */
    protected function get_default_filters(): array {
        return [
            'basket' => new basket(),
            'text' => new user_name(),
            'ids' => new in('id')
        ];
    }

    /**
     * Filter only users who are confirmed
     *
     * @return $this
     */
    public function filter_by_confirmed(): self {
        $this->where('confirmed', 1);

        return $this;
    }

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
     * Filter only users not marked as suspended
     *
     * @return $this
     */
    public function filter_by_not_suspended(): self {
        $this->where('suspended', 0);

        return $this;
    }

    /**
     * Only real users
     *
     * @return $this
     */
    public function filter_by_not_guest(): self {
        global $CFG;
        $guest_id = $CFG->siteguest;
        $this->where('id', '!=', $guest_id);

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
        $this->where(function (builder $builder) use ($search_for) {
            $db = builder::get_db();
            $alias = $builder->get_alias_sql();
            $sql_fullname = $db->sql_fullname("$alias.firstname", "$alias.lastname");
            $like_sql = $db->sql_like($sql_fullname, ':fullnamesearch', false, false);
            $like_params = ['fullnamesearch' => '%' . $db->sql_like_escape($search_for) . '%'];
            $builder->or_where_raw($like_sql, $like_params)
                ->or_where('lastname', 'ilike', $search_for);
        });

        return $this;
    }

    /**
     * Select fields required for displaying name of the user.
     *
     * @return $this
     */
    public function select_full_name_fields(): self {
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
     * Selects the fields required to display a user profile summary card.
     *
     * @see {/server/user/profile_summary_card_edit.php} Page for setting what fields are displayed on a site-level
     *
     * @param bool $include_profile_image
     * @return $this
     */
    public function select_profile_summary_card_fields(bool $include_profile_image = true): self {
        if ($include_profile_image && display_setting::display_user_picture()) {
            $this->select_user_picture_fields();
        }

        foreach (display_setting::get_display_fields() as $field_key => $field_name) {
            if ($field_name === null) {
                continue;
            }

            if ($field_name === 'fullname') {
                // 'fullname' is computed so we can't directly select it.
                $this->select_full_name_fields();
                continue;
            }

            $this
                ->add_select($field_name)
                ->group_by($field_name);
        }

        // Always include the ID.
        $this->add_select('id')->group_by('id');

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
        return totara_get_all_user_name_fields(false, $this->get_alias_sql(), null, null);
    }

    /**
     * Search for a user by search pattern
     *
     * @param context $context pass the context the search should be in relation to, usually the user context of current user
     * @param string $search_string a string to search for, currently this supports searching the fullname only
     * @param int $limit an optional limit
     * @param bool $include_guest optionally include the guest users, false by default
     * @return collection
     */
    public static function search(
        context $context,
        string $search_string = '',
        int $limit = 0,
        bool $include_guest = false
    ): collection {
        return user::repository()
            ->when(true, function (self $repository) use ($context) {
                tenant_orm_helper::restrict_users(
                    $repository,
                    new field('id', $repository->get_builder()),
                    $context
                );
            })
            ->filter_by_not_deleted()
            ->filter_by_confirmed()
            ->when(!$include_guest, function (self $repository) use ($context) {
                $repository->filter_by_not_guest();
            })
            ->when(strlen($search_string) > 0, function (self $repository) use ($search_string) {
                $repository->filter_by_full_name($search_string);
            })
            ->order_by_full_name()
            ->when($limit > 0, function (repository $repository) use ($limit) {
                $repository->limit($limit);
            })
            ->get();
    }

}
