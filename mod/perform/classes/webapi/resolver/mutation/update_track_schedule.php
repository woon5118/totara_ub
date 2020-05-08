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

        if ($track_schedule['is_fixed']) {
            if ($track_schedule['is_open']) {
                $track->update_schedule_open_fixed(
                    $track_schedule['fixed_from']
                );
            } else { // Closed.
                $track->update_schedule_closed_fixed(
                    $track_schedule['fixed_from'],
                    $track_schedule['fixed_to']
                );
            }
        } else { // Dynamic.
            $dynamic_units = array_flip(track::get_dynamic_schedule_units());
            $dynamic_directions = array_flip(track::get_dynamic_schedule_directions());
            if ($track_schedule['is_open']) {
                $track->update_schedule_open_dynamic(
                    $track_schedule['dynamic_count_from'],
                    $dynamic_units[$track_schedule['dynamic_unit']],
                    $dynamic_directions[$track_schedule['dynamic_direction']]
                );
            } else { // Closed.
                $track->update_schedule_closed_dynamic(
                    $track_schedule['dynamic_count_from'],
                    $track_schedule['dynamic_count_to'],
                    $dynamic_units[$track_schedule['dynamic_unit']],
                    $dynamic_directions[$track_schedule['dynamic_direction']]
                );
            }
        }

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

        $all_fields = [
            'fixed_from',
            'fixed_to',
            'dynamic_count_from',
            'dynamic_count_to',
            'dynamic_unit',
            'dynamic_direction',
        ];

        if ($schedule['is_fixed']) {
            if ($schedule['is_open']) {
                $required_fields = [
                    'fixed_from',
                ];
            } else { // Closed.
                $required_fields = [
                    'fixed_from',
                    'fixed_to',
                ];
            }
        } else { // Dynamic.
            if ($schedule['is_open']) {
                $required_fields = [
                    'dynamic_count_from',
                    'dynamic_unit',
                    'dynamic_direction',
                ];
            } else { // Closed.
                $required_fields = [
                    'dynamic_count_from',
                    'dynamic_count_to',
                    'dynamic_unit',
                    'dynamic_direction',
                ];
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

        if (isset($schedule['dynamic_unit'])) {
            $dynamic_units = array_flip(track::get_dynamic_schedule_units());
            if (!isset($dynamic_units[$schedule['dynamic_unit']])) {
                $errors[] = 'Invalid dynamic unit specified: ' . $schedule['dynamic_unit'];
            }
        }

        if (isset($schedule['dynamic_direction'])) {
            $dynamic_directions = array_flip(track::get_dynamic_schedule_directions());
            if (!isset($dynamic_directions[$schedule['dynamic_direction']])) {
                $errors[] = 'Invalid dynamic direction specified: ' . $schedule['dynamic_direction'];
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