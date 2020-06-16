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

namespace mod_perform\dates\resolvers\dynamic;

use core\collection;
use core_component;
use mod_perform\dates\schedule_constants;
use mod_perform\dates\relative_date_adjuster;

abstract class base_dynamic_date_resolver implements dynamic_date_resolver {

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
     * @var bool
     */
    private $ready_to_resolve;

    /**
     * @var string
     */
    protected $option_key;

    public function __construct() {
        $this->date_adjuster = new relative_date_adjuster();
    }

    /**
     * Get one instance of each dynamic date resolver.
     *
     * @return collection
     */
    public static function get_all_classes(): collection {
        $from_this_plugin = core_component::get_namespace_classes(
            'dates\resolvers\dynamic',
            dynamic_date_resolver::class
        );

        $from_other_plugins = core_component::get_namespace_classes(
            'dates_resolvers',
            dynamic_date_resolver::class
        );

        return collection::new(array_merge($from_this_plugin, $from_other_plugins));
    }

    /**
     * @param int $from_count
     * @param int|null $to_count
     * @param string $unit
     * @param string $direction
     * @param array $reference_user_ids
     * @param string $option_key
     * @return dynamic_date_resolver
     */
    public function set_parameters(
        int $from_count,
        ?int $to_count,
        string $unit,
        string $direction,
        string $option_key,
        array $reference_user_ids
    ): dynamic_date_resolver {
        schedule_constants::validate_direction($direction);
        schedule_constants::validate_unit($unit);

        if (!$this->option_is_available($option_key)) {
            throw new \coding_exception(sprintf('Invalid option key %s', $option_key));
        }

        $this->direction = $direction;
        $this->unit = $unit;
        $this->from_count = $from_count;
        $this->to_count = $to_count;
        $this->reference_user_ids = $reference_user_ids;
        $this->option_key = $option_key;

        $this->ready_to_resolve = true;

        return $this;
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
    public function get_start_for(int $user_id): ?int {
        $this->check_ready_to_resolve();

        if ($this->date_map === null) {
            $this->resolve();
        }

        if (!isset($this->date_map[$user_id])) {
            return null;
        }

        $reference_date = $this->date_map[$user_id];

        return $this->adjust_date($this->from_count, $reference_date);
    }

    /**
     * @inheritDoc
     */
    public function get_end_for(int $user_id): ?int {
        $this->check_ready_to_resolve();

        if ($this->date_map === null) {
            $this->resolve();
        }

        if ($this->to_count === null) {
            return null;
        }

        $reference_date = $this->date_map[$user_id];

        return $this->adjust_date($this->to_count, $reference_date);
    }

    protected function check_ready_to_resolve(): void {
        if (!$this->ready_to_resolve) {
            throw new \coding_exception('Can not call resolve before setting parameters');
        }
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