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
            throw new \required_capability_exception(
                $context,
                'mod/perform:manage_activity',
                'nopermission',
                ''
            );
        }

        $errors = static::validate_inputs($track_schedule);

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
            $dynamic_units = array_flip(track::get_dynamic_schedule_units());
            $dynamic_directions = array_flip(track::get_dynamic_schedule_directions());
            if ($track_schedule['schedule_is_open']) {
                $track->set_schedule_open_dynamic(
                    $track_schedule['schedule_dynamic_count_from'],
                    $dynamic_units[$track_schedule['schedule_dynamic_unit']],
                    $dynamic_directions[$track_schedule['schedule_dynamic_direction']]
                );
            } else { // Closed.
                $track->set_schedule_closed_dynamic(
                    $track_schedule['schedule_dynamic_count_from'],
                    $track_schedule['schedule_dynamic_count_to'],
                    $dynamic_units[$track_schedule['schedule_dynamic_unit']],
                    $dynamic_directions[$track_schedule['schedule_dynamic_direction']]
                );
            }
        }

        // Due date (has a dependency on schedule_is_open and schedule_is_fixed).
        if ($track_schedule['due_date_is_enabled']) {
            $dynamic_units = array_flip(track::get_dynamic_schedule_units());
            if (!$track_schedule['schedule_is_open'] && $track_schedule['schedule_is_fixed']) {
                if ($track_schedule['due_date_is_fixed']) {
                    $track->set_due_date_fixed(
                        $track_schedule['due_date_fixed']
                    );
                } else { // Relative.
                    $track->set_due_date_relative(
                        $track_schedule['due_date_relative_count'],
                        $dynamic_units[$track_schedule['due_date_relative_unit']]
                    );
                }
            } else {
                $track->set_due_date_relative(
                    $track_schedule['due_date_relative_count'],
                    $dynamic_units[$track_schedule['due_date_relative_unit']]
                );
            }
        } else { // Disabled.
            $track->set_due_date_disabled();
        }

        $track->update();

        // Repeating.
        if ($track_schedule['repeating_is_enabled']) {
            $track->set_repeating_enabled(); // TODO add params
        } else { // Disabled.
            $track->set_repeating_disabled();
        }

        // Subject instance generation method.
        $subject_instance_generation_methods = array_flip(track::get_subject_instance_generation_methods());
        $track->set_subject_instace_generation(
            $subject_instance_generation_methods[$track_schedule['subject_instance_generation']]
        );

        $track->update();

        $ec->set_relevant_context($context);

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
     * @return array
     */
    private static function validate_inputs(array $schedule): array {
        $errors = [];

        // Only includes the optional fields, not required ones such as schedule_is_open.
        $all_fields = [
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

        foreach ($required_fields as $required_field) {
            if (!isset($schedule[$required_field])) {
                $errors[] = 'Given the specified configuration, a field was missing: ' . $required_field;
            }
        }

        $unwanted_fields = array_diff($all_fields, $required_fields);
        foreach ($unwanted_fields as $unwanted_field) {
            if (isset($schedule[$unwanted_field]) && !is_null($schedule[$unwanted_field])) {
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