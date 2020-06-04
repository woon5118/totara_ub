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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jaron Steenson <jaron.steenson@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\dates;

use DateTimeZone;

class relative_date_adjuster {

    /**
     * Get the adjusted from or start boundary as an epoch. Any time is zeroed out to midnight (utc).
     *
     * @param int $count The number of units to adjust.
     * @param string $direction The direction/edge boundary to apply the adjustment towards.
     *                          Must be a direction const from schedule_constants.
     * @param string $unit The unit to apply the adjustment in. Must be a unit const from schedule_constants.
     * @param int $reference_date The unix timestamp of the reference date (date to shift before or after).
     *
     * @return int Unix timestamp
     */
    public function adjust(int $count, string $unit, string $direction, int $reference_date): int {
        schedule_constants::validate_unit($unit);
        schedule_constants::validate_direction($direction);

        $reference_date_time = (new \DateTimeImmutable(
            '@' . $reference_date,
            new DateTimeZone('utc')
        ));

        $modifier = $direction === schedule_constants::BEFORE ? '-' : '+';

        $adjusted = $reference_date_time->modify("{$modifier} {$count} {$unit}");

        return $adjusted->getTimestamp();
    }

}