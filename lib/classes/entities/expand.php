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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package core
 */

namespace core\entities;

use core\orm\query\builder;

trait expand {

    /**
     * Get all records for the given ids which could be only one entry or multiple ones depending on the source or target.
     * For example: For users it could be all members of a cohort/position/organisation or it could be an individual user.
     *
     * @param int[] $ids
     * @return array
     */
    public static function expand_multiple(array $ids): array {
        if (empty($ids)) {
            throw new \coding_exception('To be able to expand at least one id must be specified');
        }

        if (empty(static::EXPAND_TABLE) || empty(static::EXPAND_QUERY_COLUMN) || empty(static::EXPAND_SELECT_COLUMN)) {
            throw new \coding_exception('Please define the EXPAND_TABLE, EXPAND_QUERY_COLUMN and EXPAND_SELECT_COLUMN constants');
        }

        return builder::table(static::EXPAND_TABLE)
            ->select(static::EXPAND_SELECT_COLUMN)
            ->where_in(static::EXPAND_QUERY_COLUMN, $ids)
            ->group_by(static::EXPAND_SELECT_COLUMN)
            ->get()
            ->pluck(static::EXPAND_SELECT_COLUMN);
    }

    /**
     * Get all records for the given id which could be only one entry or multiple ones depending on the source or target.
     * For example: For users it could be all members of a cohort/position/organisation or it could be an individual user.
     *
     * @return array
     */
    public function expand(): array {
        return self::expand_multiple([$this->id]);
    }

}