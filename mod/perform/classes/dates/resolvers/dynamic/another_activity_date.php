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

use core\collection;
use core\orm\query\builder;
use mod_perform\entities\activity\subject_instance;
use mod_perform\entities\activity\track;
use mod_perform\entities\activity\track_user_assignment;
use mod_perform\dates\constants;

class another_activity_date extends base_dynamic_date_resolver {

    public const ACTIVITY_COMPLETED_DAY = 'activity_completed_day';
    public const ACTIVITY_INSTANCE_CREATION_DAY = 'activity_instance_creation_day';

    /**
     * @inheritDoc
     */
    protected function resolve(): void {
        $timestamp_field_name = ($this->option_key === self::ACTIVITY_COMPLETED_DAY)
            ? 'completed_at'
            : 'created_at';
        $custom_data = json_decode($this->get_custom_data(), true);
        $this->date_map = builder::create()
            ->select(['si.subject_user_id', "max(si.{$timestamp_field_name}) as user_reference_date"])
            ->from(subject_instance::TABLE , 'si')
            ->join([track_user_assignment::TABLE, 'tua'], 'si.track_user_assignment_id', 'id')
            ->join([track::TABLE, 'tr'], 'tua.track_id', 'id')
            ->where('tr.activity_id', $custom_data['activity'])
            ->where_not_null("si.{$timestamp_field_name}")
            ->where_in('si.subject_user_id', $this->bulk_fetch_keys)
            ->group_by('si.subject_user_id')
            ->get()
            ->map(function ($row) {
                // Using map (rather than pluck) to preserve keys.
                return $row->user_reference_date;
            })
            ->all(true);
    }

    /**
     * Return available source options.
     *
     * @return collection
     */
    public function get_options(): collection {
        $options = [
            new dynamic_source(
                $this,
                self::ACTIVITY_COMPLETED_DAY,
                get_string(
                    'schedule_dynamic_another_activity_completion_date',
                    'mod_perform'
                )
            ),
            new dynamic_source(
                $this,
                self::ACTIVITY_INSTANCE_CREATION_DAY,
                get_string(
                    'schedule_dynamic_another_activity_instance_creation_date',
                    'mod_perform'
                )
            )
        ];
        return new collection($options);
    }

    /**
     * @param string $option_key
     *
     * @return bool
     */
    public function option_is_available(string $option_key): bool {
        return in_array(
            $option_key,
            [self::ACTIVITY_INSTANCE_CREATION_DAY, self::ACTIVITY_COMPLETED_DAY]
        );
    }

    /**
     * get custom setting VUE component
     *
     * @return string|null
     */
    public function get_custom_setting_component(): ?string {
        return 'mod_perform/components/manage_activity/assignment/schedule/custom_settings/ActivitySelector';
    }

    /**
     * returns default values when custom data is empty
     *
     * @return string|null
     */
    public function get_custom_data(): ?string {
        if (!$this->custom_data) {
            return json_encode(['activity' => null]);
        }

        return $this->custom_data;
    }

    /**
     * @param string|null $custom_data
     *
     * @return bool
     */
    public function is_valid_custom_data(?string $custom_data): bool {
        $data = json_decode($custom_data, true);
        return isset($data['activity']) && is_number($data['activity']);
    }

    /**
     * @inheritDoc
     */
    public function get_resolver_base(): string {
        return constants::DATE_RESOLVER_USER_BASED;
    }

}
