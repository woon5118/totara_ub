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

namespace mod_perform\webapi\resolver\type;

use core\format;
use core\webapi\execution_context;
use core\webapi\type_resolver;
use mod_perform\formatter\activity\activity as activity_formatter;
use mod_perform\models\activity\activity as activity_model;

/**
 * Note: It is the responsibility of the query to ensure the user is permitted to see an activity.
 */
class activity implements type_resolver {

    /**
     * @param string $field
     * @param activity_model $activity
     * @param array $args
     * @param execution_context $ec
     *
     * @return mixed
     */
    public static function resolve(string $field, $activity, array $args, execution_context $ec) {
        if (!$activity instanceof activity_model) {
            throw new \coding_exception('Expected activity model');
        }

        $format = $args['format'] ?? format::FORMAT_HTML;

        // The "subject_instance" type is not compatible with relevant context.
        // As well as this type ("activity") it also has "user" as a child type.
        // "User" is not compatible with a relevant execution context being set to a perform container.
        $context = $ec->has_relevant_context() ? $ec->get_relevant_context() : $activity->get_context();

        $formatter = new activity_formatter($activity, $context);

        return $formatter->format($field, $format);
    }
}