<?php
/*
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
 * @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\dates\resolvers\dynamic;

use mod_perform\dates\date_offset;
use mod_perform\dates\resolvers\date_resolver;
use core\collection;

interface dynamic_date_resolver extends date_resolver {

    /**
     * Set the parameters used to resolve dates for a set of users.
     *
     * Individual users dates are retrieved by calling get_start_for and get_end_for.
     * Generally all users from $reference_user_ids will be resolved lazily from a
     * get_start/end_for call.
     *
     * @param date_offset $from
     * @param date_offset|null $to
     * @param array $reference_user_ids The user ids to resolve dates for.
     * @param string $option_key
     * @return static
     * @see get_start_for
     * @see get_end_for
 ***/
    public function set_parameters(
        date_offset $from,
        ?date_offset $to,
        string $option_key,
        array $reference_user_ids
    ): self;

    /**
     * Get all source date options for this resolver.
     *
     * @return collection|dynamic_source[]
     */
    public function get_options(): collection;

    /**
     * Is a particular option key available.
     *
     * @param string $option_key
     * @return bool
     */
    public function option_is_available(string $option_key): bool;

}