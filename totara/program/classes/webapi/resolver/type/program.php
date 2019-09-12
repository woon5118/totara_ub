<?php
/*
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

use totara_program\formatter\program_formatter;
use core\format;
use context_program;
use core\webapi\execution_context;
use core\webapi\type_resolver;
use coding_exception;
use coursecat;

/**
 * Program type
 *
 * Note: It is the responsibility of the query to ensure the user is permitted to see a program
 */
class program implements type_resolver {

    /**
     * Resolve program fields
     *
     * @param string $field
     * @param mixed $program
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
    public static function resolve(string $field, $program, array $args, execution_context $ec) {
        global $CFG;

        require_once($CFG->dirroot . '/totara/program/lib.php');
        require_once($CFG->dirroot . '/totara/program/program.class.php');

        if (!$program instanceof \program) {
             throw new \coding_exception('Only program objects are accepted: ' . gettype($program));
        }

        $format = $args['format'] ?? null;
        $programcontext = context_program::instance($program->id);
        $ec->set_relevant_context($programcontext);

        if (!self::authorize($field, $format, $programcontext)) {
            return null;
        }

        $datefields = ['availablefrom', 'availableuntil'];
        if (in_array($field, $datefields) && empty($program->{$field})) {
            // Highly unlikely this is set to 1/1/1970, return null for notset dates.
            return null;
        }

        if ($field == 'summaryformat') {
            // Programs don't actually have a summaryformat, they are just always HTML.
            return FORMAT_HTML;
        }

        if ($field == 'category') {
            return coursecat::get($program->category);
        }

        if ($field == 'coursesets') {
            $prog = new \program($program->id);
            /* @var $content \prog_content */
            $content = $prog->get_content();
            return $content->get_course_sets();
        }

        $formatter = new program_formatter($program, $programcontext);
        return $formatter->format($field, $format);
    }

    public static function authorize(string $field, ?string $format, context_program $programcontext) {
        // Permission to see RAW formatted string fields
        if (in_array($field, ['fullname', 'shortname']) && $format == format::FORMAT_RAW) {
            return has_capability('totara/program:configuredetails', $programcontext);
        }
        // Permission to see RAW formatted text fields
        if (in_array($field, ['summary']) && $format == format::FORMAT_RAW) {
            return has_capability('totara/program:configuredetails', $programcontext);
        }
        return true;
    }


}
