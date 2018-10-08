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
 * @author Michael Dunstan <michael.dunstan@androgogic.com>
 * @package contentmarketplace_goone
 */

namespace contentmarketplace_goone;

defined('MOODLE_INTERNAL') || die();

final class subscription {

    /**
     * @var array
     */
    private $ids;

    /**
     * Determine if given learning object is a member of the subscription for the current GO1 account.
     *
     * @param int $id of the learning object
     * @return bool
     */
    public function does_include_learning_object($id) {
        if (!isset($this->ids)) {
            $api = new api();
            $this->ids = $api->list_ids_for_all_learning_objects(['subscribed' => "true"]);
        }
        return in_array($id, $this->ids);
    }

}
