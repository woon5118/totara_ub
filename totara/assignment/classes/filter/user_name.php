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
 * @package totara_assignment
 */

namespace totara_assignment\filter;

use core\orm\query\builder;
use core\orm\query\field;
use core\orm\entity\filter\like;
use core\orm\query\raw_field;

/**
 * Filters for some user name information
 *
 * @package totara_competency\entities\filters
 */
class user_name extends like {

    public function __construct() {
        parent::__construct([
            new raw_field(builder::concat(
                new field('firstname', $this->builder),
                "' '",
                new field('lastname', $this->builder)
            )),
            new raw_field(builder::concat(
                new field('lastname', $this->builder),
                "' '",
                new field('firstname', $this->builder)
            )),
        ]);
    }

}