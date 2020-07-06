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

use coding_exception;
use core\collection;
use core_component;
use mod_perform\dates\date_offset;

abstract class base_dynamic_date_resolver implements dynamic_date_resolver {

    /**
     * @var array|null
     */
    protected $date_map;

    /**
     * @var int[]
     */
    protected $reference_user_ids;

    /**
     * @var int[]
     */
    protected $reference_job_assignment_ids;

    /**
     * @var date_offset
     */
    protected $from;

    /**
     * @var date_offset|null
     */
    protected $to;

    /**
     * @var bool
     */
    private $ready_to_resolve;

    /**
     * @var string
     */
    protected $option_key;

    /**
     * @var string|null
     */
    protected $custom_data;

    /**
     * @var int|null
     */
    protected $now;

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
     * Set the "now" time, primarily for testing purposes.
     *
     * @param int $now
     * @return $this
     */
    public function set_time(int $now): self {
        $this->now = $now;

        return $this;
    }


    /**
     * Get the "now" time.
     * This method should be proffered over a direct time call to allow testability.
     *
     * @return int
     */
    protected function get_time(): int {
        return $this->now ?? time();
    }

    /**
     * @param date_offset $from
     * @param date_offset|null $to
     * @param string $option_key
     * @param array $reference_user_ids
     * @param array $reference_job_assignment_ids
     * @return dynamic_date_resolver
     */
    public function set_parameters(
        date_offset $from,
        ?date_offset $to,
        string $option_key,
        array $reference_user_ids,
        array $reference_job_assignment_ids = []
    ): dynamic_date_resolver {
        if (!$this->option_is_available($option_key)) {
            throw new coding_exception(sprintf('Invalid option key %s', $option_key));
        }

        $this->from = $from;
        $this->to = $to;
        $this->reference_user_ids = $reference_user_ids;
        $this->reference_job_assignment_ids = $reference_job_assignment_ids;
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
    public function get_start_for(int $user_id, ?int $job_assignment_id = null): ?int {
        $this->check_ready_to_resolve();

        if ($this->date_map === null) {
            $this->resolve();
        }

        if (!isset($this->date_map[$user_id])) {
            return null;
        }

        $reference_date = $this->date_map[$user_id];

        return $this->from->apply($reference_date);
    }

    /**
     * @inheritDoc
     */
    public function get_end_for(int $user_id, ?int $job_assignment_id = null): ?int {
        $this->check_ready_to_resolve();

        if ($this->date_map === null) {
            $this->resolve();
        }

        if ($this->to === null) {
            return null;
        }

        if (!isset($this->date_map[$user_id])) {
            return null;
        }

        $reference_date = $this->date_map[$user_id];

        // End dates are adjusted to "end of day".
        $reference_date += DAYSECS;

        return $this->to->apply($reference_date);
    }

    protected function check_ready_to_resolve(): void {
        if (!$this->ready_to_resolve) {
            throw new coding_exception('Can not call resolve before setting parameters');
        }
    }

    /**
     * This custom setting component name
     *
     * @return string|null
     */
    public function get_custom_setting_component(): ?string {
        return null;
    }

    /**
     * @return string|null
     */
    public function get_custom_data(): ?string {
        return $this->custom_data;
    }

    /**
     * @param string|null $custom_data
     */
    public function set_custom_data(?string $custom_data): void {
        if (!$this->is_valid_custom_data($custom_data)) {
            throw new \coding_exception('Invalid custom data');
        }
        $this->custom_data = $custom_data;
    }

    /**
     * Check the validity of custom data
     * need to implement this method if the resolver class contains custom data  
     *
     * @param string|null $custom_data
     *
     * @return bool
     */
    public function is_valid_custom_data(?string $custom_data): bool {
        return is_null($custom_data);
    }

    /**
     * Does this resolver use job assignments
     *
     * @return bool
     */
    public function is_job_based(): bool {
        return false;
    }

}
