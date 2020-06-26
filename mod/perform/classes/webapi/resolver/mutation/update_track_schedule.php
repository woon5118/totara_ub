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
 * @author Nathan Lewis <nathan.lewis@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\mutation;

use coding_exception;
use core\webapi\execution_context;
use core\webapi\mutation_resolver;
use core\webapi\middleware\require_advanced_feature;
use core\webapi\resolver\has_middleware;
use totara_core\dates\date_time_setting;
use mod_perform\dates\date_offset;
use mod_perform\dates\resolvers\dynamic\dynamic_source;
use mod_perform\models\activity\track;
use mod_perform\webapi\middleware\require_activity;
use mod_perform\webapi\middleware\require_manage_capability;

class update_track_schedule implements mutation_resolver, has_middleware {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $track_schedule = $args['track_schedule'];
        $track_id = $track_schedule['track_id'];
        $track = track::load_by_id($track_id);

        $errors = static::validate_inputs($track_schedule, $track);

        if ($errors) {
            throw new coding_exception(implode(', ', $errors));
        }

        // Fixed and dynamic schedules.
        if ($track_schedule['schedule_is_fixed']) {
            $from = date_time_setting::create_from_array($track_schedule['schedule_fixed_from']);

            if ($track_schedule['schedule_is_open']) {
                $track->set_schedule_open_fixed($from);
            } else { // Closed.
                $to = date_time_setting::create_from_array($track_schedule['schedule_fixed_to']);
                $track->set_schedule_closed_fixed($from, $to);
            }
        } else { // Dynamic.
            $dynamic_source = dynamic_source::create_from_json(
                $track_schedule['schedule_dynamic_source'],
                true
            );

            $dynamic_from = date_offset::create_from_json(
                $track_schedule['schedule_dynamic_from']
            );

            if ($track_schedule['schedule_is_open']) {
                $track->set_schedule_open_dynamic(
                    $dynamic_from,
                    $dynamic_source,
                    $track_schedule['schedule_use_anniversary']
                );
            } else { // Closed.
                $dynamic_to = date_offset::create_from_json(
                    $track_schedule['schedule_dynamic_to']
                );

                $track->set_schedule_closed_dynamic(
                    $dynamic_from,
                    $dynamic_to,
                    $dynamic_source,
                    $track_schedule['schedule_use_anniversary']
                );
            }
        }

        // Due date (has a dependency on schedule_is_open and schedule_is_fixed).
        if ($track_schedule['due_date_is_enabled']) {
            if (!$track_schedule['schedule_is_open'] && $track_schedule['schedule_is_fixed']) {
                if ($track_schedule['due_date_is_fixed']) {
                    $track->set_due_date_fixed(
                        date_time_setting::create_from_array($track_schedule['due_date_fixed'])
                    );
                } else { // Relative.
                    $due_date_offset = date_offset::create_from_json(
                        $track_schedule['due_date_offset']
                    );

                    $track->set_due_date_relative(
                        $due_date_offset
                    );
                }
            } else {
                $due_date_offset = date_offset::create_from_json(
                    $track_schedule['due_date_offset']
                );

                $track->set_due_date_relative(
                    $due_date_offset
                );
            }
        } else { // Disabled.
            $track->set_due_date_disabled();
        }

        // Repeating.
        if ($track_schedule['repeating_is_enabled']) {
            $repeating_offset = date_offset::create_from_json(
                $track_schedule['repeating_offset']
            );

            $track->set_repeating_enabled(
                track::mapped_value_from_string($track_schedule['repeating_type'],
                    track::get_repeating_types(),
                    'repeating type'
                ),
                $repeating_offset,
                $track_schedule['repeating_is_limited'] ? $track_schedule['repeating_limit'] : null
            );
        } else { // Disabled.
            $track->set_repeating_disabled();
        }

        // Subject instance generation method.
        $track->set_subject_instance_generation(
            track::mapped_value_from_string($track_schedule['subject_instance_generation'],
                track::get_subject_instance_generation_methods(),
                'instance generation method'
            )
        );

        $track->update();

        return [
            'track' => $track,
        ];
    }

    /**
     * Validates that the correct combinations of inputs have been provided
     *
     * Does not check that the values are valid. This checking is done within the model.
     *
     * @param array $schedule
     * @param track $track
     * @return array
     */
    private static function validate_inputs(array $schedule, track $track): array {
        $errors = [];

        // Only includes the optional fields, not required ones such as schedule_is_open.
        $all_fields = [
            'subject_instance_generation',
            'schedule_fixed_from',
            'schedule_fixed_to',
            'schedule_dynamic_from',
            'schedule_dynamic_to',
            'schedule_dynamic_source',
            'schedule_use_anniversary',
            'due_date_is_fixed',
            'due_date_fixed',
            'due_date_offset',
        ];

        $required_fields = [];

        // Fixed and dynamic schedules.
        if ($schedule['schedule_is_fixed']) {
            if ($schedule['schedule_is_open']) {
                $required_fields[] = 'schedule_fixed_from';
            } else { // Closed.
                $required_fields[] = 'schedule_fixed_from';
                $required_fields[] = 'schedule_fixed_to';
            }
        } else { // Dynamic.
            if ($schedule['schedule_is_open']) {
                $required_fields[] = 'schedule_dynamic_from';
                $required_fields[] = 'schedule_dynamic_source';
                $required_fields[] = 'schedule_use_anniversary';
            } else { // Closed.
                $required_fields[] = 'schedule_dynamic_from';
                $required_fields[] = 'schedule_dynamic_to';
                $required_fields[] = 'schedule_dynamic_source';
                $required_fields[] = 'schedule_use_anniversary';
            }
        }

        // Due date (has a dependency on schedule_is_open and schedule_is_fixed).
        if ($schedule['due_date_is_enabled']) {
            if (!$schedule['schedule_is_open'] && $schedule['schedule_is_fixed']) {
                $required_fields[] = 'due_date_is_fixed';
                if (!empty($schedule['due_date_is_fixed'])) {
                    $required_fields[] = 'due_date_fixed';
                } else { // Relative.
                    $required_fields[] = 'due_date_offset';
                }
            } else {
                $required_fields[] = 'due_date_offset';
            }
        }

        // Repeating.
        if ($schedule['repeating_is_enabled']) {
            $required_fields[] = 'repeating_type';
            $required_fields[] = 'repeating_offset';
            $required_fields[] = 'repeating_is_limited';
            if (!empty($schedule['repeating_is_limited']) && $schedule['repeating_is_limited']) {
                $required_fields[] = 'repeating_limit';
            }
        }

        // Subject instance generation.
        $required_fields[] = 'subject_instance_generation';

        // Check for missing fields.
        foreach ($required_fields as $required_field) {
            if (!isset($schedule[$required_field])) {
                $errors[] = 'Given the specified configuration, a field was missing: ' . $required_field;
            }
        }

        // Check for unwanted fields.
        $unwanted_fields = array_diff($all_fields, $required_fields);
        foreach ($unwanted_fields as $unwanted_field) {
            if (array_key_exists($unwanted_field, $schedule) && !is_null($schedule[$unwanted_field])) {
                $errors[] = 'Given the specified configuration, an unexpected field was found: ' . $unwanted_field;
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_middleware(): array {
        return [
            new require_advanced_feature('performance_activities'),
            require_activity::by_track_id('track_schedule.track_id', true),
            require_manage_capability::class
        ];
    }
}
