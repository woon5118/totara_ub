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

class another_activity_date extends base_dynamic_date_resolver {

    public const ACTIVITY_COMPLETED_DAY = 'activity_completed_day';
    public const ACTIVITY_INSTANCE_CREATION_DAY = 'activity_instance_creation_day';

    /**
     * @todo this method will resolve the date
     * @inheritDoc
     */
    protected function resolve(): void {
        $this->date_map = [];
    }

    /**
     * return avaliable source options
     * @return collection
     * @throws \coding_exception
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
}