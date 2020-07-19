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
 * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>
 * @package totara_job
 */

namespace totara_job\relationship\resolvers;

use totara_core\relationship\relationship_resolver;
use totara_job\entities\job_assignment;

class appraiser extends relationship_resolver {

    /**
     * The name of this relationship resolver to display to the user.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('appraiser', 'totara_job');
    }

    /**
     * @inheritDoc
     */
    public static function get_name_plural(): string {
        return get_string('appraiser_plural', 'totara_job');
    }

    /**
     * Get a list of fields that can be provided to {@see get_users}
     *
     * @return string[][]
     */
    public static function get_accepted_fields(): array {
        return [
            ['job_assignment_id'],
            ['user_id'],
        ];
    }

    /**
     * Get the list of appraisers.
     *
     * @param array $data containing the fields specified by {@see get_accepted_fields}
     * @return int[] of user ids
     */
    protected static function get_data(array $data): array {
        $repository = job_assignment::repository();

        if (isset($data['job_assignment_id'])) {
            $repository->where('id', $data['job_assignment_id']);
        } else {
            $repository->where('userid', $data['user_id']);
        }

        return $repository
            ->select_raw('DISTINCT appraiserid')
            ->get()
            ->pluck('appraiserid');
    }

}
