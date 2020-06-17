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
use mod_perform\models\activity\track;
use mod_perform\webapi\middleware\require_activity;
use moodle_exception;

class update_track_schedule implements mutation_resolver, has_middleware {
    /**
     * {@inheritdoc}
     */
    public static function resolve(array $args, execution_context $ec) {
        $track_schedule = $args['track_schedule'];
        $track_id = $track_schedule['track_id'];
        $track = track::load_by_id($track_id);
        $activity = $track->get_activity();
        $context = $activity->get_context();

        if (!$activity->can_manage()) {
            throw new moodle_exception('invalid_activity', 'mod_perform');
        }

        $ec->set_relevant_context($context);

        $errors = static::validate_inputs($track_schedule, $track);

        if ($errors) {
            throw new coding_exception(implode(', ', $errors));
        }

        // Fixed and dynamic schedules.
        if ($track_schedule['schedule_is_fixed']) {
            if ($track_schedule['schedule_is_open']) {
                $track->set_schedule_open_fixed(
                    $track_schedule['schedule_fixed_from']
                );
            } else { // Closed.
                $track->set_schedule_closed_fixed(
                    $track_schedule['schedule_fixed_from'],
                    $track_schedule['schedule_fixed_to']
                );
            }
        } else { // Dynamic.
            if ($track_schedule['schedule_is_open']) {
                $track->set_schedule_open_dynamic(
                    $track_schedule['schedule_dynamic_count_from'],
                    track::mapped_value_from_string($track_schedule['schedule_dynamic_unit'],
                        track::get_dynamic_schedule_units(),
                        'schedule dynamic unit'
                    ),
                    track::mapped_value_from_string($track_schedule['schedule_dynamic_direction'],
                        track::get_dynamic_schedule_directions(),
                        'schedule dynamic direction'
                    )
                );
            } else { // Closed.
                $track->set_schedule_closed_dynamic(
                    $track_schedule['schedule_dynamic_count_from'],
                    $track_schedule['schedule_dynamic_count_to'],
                    track::mapped_value_from_string($track_schedule['schedule_dynamic_unit'],
                        track::get_dynamic_schedule_units(),
                        'schedule dynamic unit'
                    ),
                    track::mapped_value_from_string($track_schedule['schedule_dynamic_direction'],
                        track::get_dynamic_schedule_directions(),
                        'schedule dynamic direction'
                    )
                );
            }
        }

        // Due date (has a dependency on schedule_is_open and schedule_is_fixed).
        if ($track_schedule['due_date_is_enabled']) {
            if (!$track_schedule['schedule_is_open'] && $track_schedule['schedule_is_fixed']) {
                if ($track_schedule['due_date_is_fixed']) {
                    $track->set_due_date_fixed(
                        $track_schedule['due_date_fixed']
                    );
                } else { // Relative.
                    $track->set_due_date_relative(
                        $track_schedule['due_date_relative_count'],
                        track::mapped_value_from_string($track_schedule['due_date_relative_unit'],
                            track::get_dynamic_schedule_units(),
                            'schedule dynamic direction'
                        )
                    );
                }
            } else {
                $track->set_due_date_relative(
                    $track_schedule['due_date_relative_count'],
                    track::mapped_value_from_string($track_schedule['due_date_relative_unit'],
                        track::get_dynamic_schedule_units(),
                        'due date relative unit'
                    )
                );
            }
        } else { // Disabled.
            $track->set_due_date_disabled();
        }

        // Repeating.
        if ($track_schedule['repeating_is_enabled']) {
            $track->set_repeating_enabled(
                track::mapped_value_from_string($track_schedule['repeating_relative_type'],
                    track::get_repeating_relative_types(),
                    'repeating relative type'
                ),
                $track_schedule['repeating_relative_count'],
                track::mapped_value_from_string($track_schedule['repeating_relative_unit'],
                    track::get_dynamic_schedule_units(),
                    'repeating relative unit'
                ),
                $track_schedule['repeating_is_limited'] ? $track_schedule['repeating_limit'] : null
            );
        } else { // Disabled.
            $track->set_repeating_disabled();
        }

        // Subject instance generation method.
        if ($track->get_subject_instance_generation_control_is_enabled()) {
            $subject_instance_generation_methods = array_flip(track::get_subject_instance_generation_methods());
            $track->set_subject_instace_generation(
                $subject_instance_generation_methods[$track_schedule['subject_instance_generation']]
            );
        }

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
            'schedule_dynamic_count_from',
            'schedule_dynamic_count_to',
            'schedule_dynamic_unit',
            'schedule_dynamic_direction',
            'due_date_is_fixed',
            'due_date_fixed',
            'due_date_relative_count',
            'due_date_relative_unit',
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
                $required_fields[] = 'schedule_dynamic_count_from';
                $required_fields[] = 'schedule_dynamic_unit';
                $required_fields[] = 'schedule_dynamic_direction';
            } else { // Closed.
                $required_fields[] = 'schedule_dynamic_count_from';
                $required_fields[] = 'schedule_dynamic_count_to';
                $required_fields[] = 'schedule_dynamic_unit';
                $required_fields[] = 'schedule_dynamic_direction';
            }
        }

        // Due date (has a dependency on schedule_is_open and schedule_is_fixed).
        if ($schedule['due_date_is_enabled']) {
            if (!$schedule['schedule_is_open'] && $schedule['schedule_is_fixed']) {
                $required_fields[] = 'due_date_is_fixed';
                if (!empty($schedule['due_date_is_fixed'])) {
                    $required_fields[] = 'due_date_fixed';
                } else { // Relative.
                    $required_fields[] = 'due_date_relative_count';
                    $required_fields[] = 'due_date_relative_unit';
                }
            } else {
                $required_fields[] = 'due_date_relative_count';
                $required_fields[] = 'due_date_relative_unit';
            }
        }

        // Repeating.
        if ($schedule['repeating_is_enabled']) {
            $required_fields[] = 'repeating_relative_type';
            $required_fields[] = 'repeating_relative_count';
            $required_fields[] = 'repeating_relative_unit';
            $required_fields[] = 'repeating_is_limited';
            if (!empty($schedule['repeating_is_limited']) && $schedule['repeating_is_limited']) {
                $required_fields[] = 'repeating_limit';
            }
        }

        // Subject instance generation.
        if ($track->get_subject_instance_generation_control_is_enabled()) {
            $required_fields[] = 'subject_instance_generation';
        }

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
            require_activity::by_track_id('track_schedule.track_id', true)
        ];
    }
}
