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
 * @author Fabian Derschatta <fabian.derschatta@totaralearning.com>
 * @package core
 */

namespace core\entities;

use core\orm\query\builder;

/**
 * @property string $expand_table
 * @property string $expand_query_column
 * @property string $expand_select_column
 */
trait expand {

    /**
     * Get all records for the given id which could be only one entry or multiple ones depending on the source or target.
     * For example: For users it could be all members of a cohort/position/organisation or it could be an individual user.
     *
     * @return array
     */
    public function expand(): array {
        if (empty($this->id)) {
            throw new \coding_exception('To be able to expand an id has to be set');
        }

        if (empty($this->expand_table) || empty($this->expand_query_column) || empty($this->expand_select_column)) {
            throw new \coding_exception('Please define expand_table, expand_query_column and expand_select_column');
        }

        $expanded = builder::table($this->expand_table)
            ->select($this->expand_select_column)
            ->where($this->expand_query_column, $this->id)
            ->get();

        return $expanded->pluck($this->expand_select_column);
    }

}