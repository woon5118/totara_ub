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
 * @author Riana Rossouw <riana.rossouw@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\dates\resolvers\dynamic;

use core\collection;
use core\orm\query\builder;
use core_course\totara_catalog\course\dataholder_factory\startdate;

class job_assignment_start_date extends base_dynamic_date_resolver {

    public const JOB_ASSIGNMENT_START_KEY = 'job-assignment-start-date';

    /**
     * @inheritDoc
     */
    protected function resolve(): void {
        $this->date_map = builder::create()
            ->select(['id', 'userid', 'startdate'])
            ->from('job_assignment')
            ->where('id', $this->reference_job_assignment_ids)
            ->get()
            ->all(true);
    }

    /**
     * @inheritDoc
     */
    public function get_start_for(int $user_id, ?int $job_assignment_id = null): ?int {
        $this->check_ready_to_resolve();

        // If this is not per job_assignment, return null
        if ($job_assignment_id === null) {
            return null;
        }

        $reference_date = $this->get_start_date($user_id, $job_assignment_id);
        if ($reference_date === null) {
            return $reference_date;
        }

        return $this->from->apply($reference_date);
    }

    /**
     * @inheritDoc
     */
    public function get_end_for(int $user_id, ?int $job_assignment_id = null): ?int {
        $this->check_ready_to_resolve();

        // If this is not per job_assignment, return null
        if ($job_assignment_id === null) {
            return null;
        }

        if ($this->to === null) {
            return null;
        }

        $reference_date = $this->get_start_date($user_id, $job_assignment_id);
        if ($reference_date === null) {
            return $reference_date;
        }
        return $this->to->apply($reference_date);
    }


    /**
     * @inheritDoc
     */
    public function get_options(): collection {
        return new collection(
            [$this->make_option(static::JOB_ASSIGNMENT_START_KEY)]
        );
    }

    /**
     * @inheritDoc
     */
    public function option_is_available(string $option_key): bool {
        return in_array($option_key, $this->get_supported_option_keys(), true);
    }

    protected function get_supported_option_keys(): array {
        return [static::JOB_ASSIGNMENT_START_KEY];
    }

    protected function get_option_display_name(?string $option_key) {
        return get_string('job_assignment_start_date', 'mod_perform');
    }

    protected function make_option(string $option_key): dynamic_source {
        return new dynamic_source(
            $this,
            $option_key,
            $this->get_option_display_name($option_key)
        );
    }

    /**
     * @param int $user_id
     * @param int $job_assignment_id
     * @return int|null
     */
    private function get_start_date(int $user_id, int $job_assignment_id): ?int {
        if ($this->date_map === null) {
            $this->resolve();
        }

        if (!isset($this->date_map[$job_assignment_id]) || $this->date_map[$job_assignment_id]->startdate === null) {
            return null;
        }

        // TODO: Not sure whether this is needed
        // Job assignment user and track_user_assignment user differs
        if ($this->date_map[$job_assignment_id]->userid != $user_id) {
            throw new \coding_exception('Job assignment is not for the track_user_assignment user');
        }

        return (int) $this->date_map[$job_assignment_id]->startdate;
    }

    /**
     * @inheritDoc
     */
    public function is_job_based(): bool {
        return true;
    }

}
