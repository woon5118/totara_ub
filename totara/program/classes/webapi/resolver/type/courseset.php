<?php

/**
 * This file is part of Totara Learn
 * 
 * Copyright (C) 2019 onwards Totara Learning Solutions LTD
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
 * @package totara_program
 * @author Simon Coggins <simon.coggins@totaralearning.com>
 */

namespace totara_program\webapi\resolver\type;

use totara_program\formatter\courseset_formatter;
use context_program;
use core\webapi\execution_context;
use core\webapi\type_resolver;
use stdClass;
use coding_exception;
use core\format;

/**
 * Courseset type
 *
 * Note: It is the responsibility of the query to ensure the user is permitted to see the courseset
 */
class courseset implements type_resolver {

    /**
     * Resolve courseset fields
     *
     * @param string $field
     * @param mixed $courseset
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
    public static function resolve(string $field, $courseset, array $args, execution_context $ec) {
        global $CFG;

        require_once($CFG->dirroot . '/totara/program/lib.php');

        if (!$courseset instanceof stdClass) {
            throw new coding_exception('Only courseset records from the database are accepted ' . gettype($courseset));
        }

        $format = $args['format'] ?? null;
        $programcontext = context_program::instance($courseset->programid);
        $ec->set_relevant_context($programcontext);

        if (!self::authorize($field, $format, $ec)) {
            return null;
        }

        $formatter = new courseset_formatter($courseset, $programcontext);
        return $formatter->format($field, $format);
    }

    public static function authorize(string $field, ?string $format, execution_context $ec) {
        // Permission to see RAW formatted string fields
        if (in_array($field, ['label']) && $format == format::FORMAT_RAW) {
            return has_capability('totara/program:configurecontent', $ec->get_relevant_context());
        }
        return true;
    }
}