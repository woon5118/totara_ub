<?php
/**
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
 * @author Murali Nair <murali.nair@totaralearning.com>
 * @package mod_perform
 */

namespace mod_perform\webapi\resolver\type;

use \context_system;
use core\format;

use core\webapi\execution_context;
use core\webapi\type_resolver;

use mod_perform\models\activity\activity_type as activity_type_model;
use mod_perform\formatter\activity\activity_type as activity_type_formatter;

defined('MOODLE_INTERNAL') || die();

/**
 * Maps the activity_type model class into a GraphQL mod_perform_activity_type.
 */
class activity_type implements type_resolver {
    /**
     * Default formats.
     */
    private const DEF_FORMATS = [
        'name' => format::FORMAT_PLAIN,
        'display_name' => format::FORMAT_PLAIN
    ];

    /**
     * {@inheritdoc}
     */
    public static function resolve(string $field, $source, array $args, execution_context $ec) {
        if (!$source instanceof activity_type_model) {
            throw new \coding_exception(__METHOD__ . ' requires an activity type model');
        }

        $format = $args['format'] ?? self::DEF_FORMATS[$field] ?? null;
        $context = context_system::instance();
        $formatter = new activity_type_formatter($source, $context);

        return $formatter->format($field, $format);
    }
}