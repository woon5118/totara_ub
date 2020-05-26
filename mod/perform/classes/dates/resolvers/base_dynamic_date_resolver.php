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

namespace mod_perform\dates\resolvers;

use mod_perform\dates\schedule_constants;
use mod_perform\dates\relative_date_adjuster;

abstract class base_dynamic_date_resolver implements date_resolver {

    /**
     * @var array|null
     */
    protected $date_map;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var int[]
     */
    protected $reference_user_ids;

    /**
     * @var string
     */
    protected $unit;

    /**
     * @var int
     */
    protected $from_count;

    /**
     * @var int|null
     */
    protected $to_count;

    /**
     * @var relative_date_adjuster
     */
    protected $date_adjuster;

    /**
     * @param int $from_count
     * @param int|null $to_count
     * @param string $unit
     * @param string $direction
     * @param array $reference_user_ids
     */
    public function __construct(int $from_count, ?int $to_count, string $unit, string $direction, array $reference_user_ids) {
        schedule_constants::validate_direction($direction);
        schedule_constants::validate_unit($unit);

        $this->direction = $direction;
        $this->unit = $unit;
        $this->from_count = $from_count;
        $this->to_count = $to_count;
        $this->reference_user_ids = $reference_user_ids;

        $this->date_adjuster = new relative_date_adjuster();
    }

    /**
     * Should bulk fetch reference date for the supplied user ids.
     * Most likely populating $date_map with user ids as keys and reference dates as entries.
     *
     * Is called lazily by get_start_for/get_end_for.
     */
    abstract protected function resolve(): void;

    /**
     * @inheritDoc
     */
    public function get_start_for(int $user_id): int {
        if ($this->date_map === null) {
            $this->resolve();
        }

        $reference_date = $this->date_map[$user_id];

        return $this->adjust_date($this->from_count, $reference_date);
    }

    /**
     * @inheritDoc
     */
    public function get_end_for(int $user_id): ?int {
        if ($this->date_map === null) {
            $this->resolve();
        }

        if ($this->to_count === null) {
            return null;
        }

        $reference_date = $this->date_map[$user_id];

        return $this->adjust_date($this->to_count, $reference_date);
    }

    protected function adjust_date(int $count, int $reference_date): int {
        return $this->date_adjuster->adjust(
            $count,
            $this->unit,
            $this->direction,
            $reference_date
        );
    }

}