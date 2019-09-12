<?php
/*
 * This file is part of Totara Learn
 *
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package totara_criteria
 */

namespace totara_criteria;

interface item_evaluator_user_source {

    /**
     * Return the source type
     *@return string
     */
    public function get_source_type(): string;

    /**
     * Return the user_id_source
     * @return mixed Source used for obtaining currently assigned users
     */
    public function get_source();

    /**
     * Does the source hold a full set of user ids?
     * @return bool
     */
    public function is_full_user_set(): bool;

   /**
     * Create item records for all users in the users_source who doesn't have a item_record
     * @param int $criterion                                                                                                                                                              _id
     * @param int $criterion_met Criterion met value to use when creating new item records
     * @param ?int $timeevaluated
     */
   public function create_item_records(int $criterion_id, int $criterion_met = 0, ?int $timeevaluated = null);

    /**
     * Delete item records for all users not in the users_source if the source contains the full list
     * @param int $criterion_id
     */
   public function delete_item_records(int $criterion_id);

    /**
     * Mark users in the users_source whose item_record was updated since the specified time
     * @param int $criterion_id
     * @param int $checkfrom Time to use as start when checking updates of item records
     */
   public function mark_updated_assigned_users(int $criterion_id, int $checkfrom);
}
