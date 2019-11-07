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
 * @package totara_assignment
 */

namespace totara_assignment\entities;


use core\orm\entity\filter\in;
use totara_assignment\filter\basket;
use totara_assignment\filter\user_name;
use core\orm\entity\repository;

class user_repository extends repository {

    protected function get_default_filters(): array {
        return [
            'basket' => new basket(),
            'text' => new user_name(),
            'ids' => new in('id')
        ];
    }

    /**
     * Filter only users not marked as deleted
     *
     * @return $this
     */
    public function filter_by_not_deleted() {
        $this->where('deleted', 0);

        return $this;
    }

    /**
     * Only real users
     *
     * @return $this
     */
    public function filter_by_not_guest() {
        $this->where('username', '!=', 'guest');

        return $this;
    }

    /**
     * Select only display name of the user for display purposes
     *
     * @return $this
     */
    public function select_full_name_fields_only() {
        $this->add_select('id')
            ->add_select_raw(totara_get_all_user_name_fields(true, null, null, null, true));

        return $this;
    }

}
