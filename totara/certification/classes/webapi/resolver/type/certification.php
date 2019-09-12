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
 * @package totara_certification
 * @author David Curry <david.curry@totaralearning.com>
 */

namespace totara_certification\webapi\resolver\type;

use totara_certification\formatter\certification_formatter;
use core\format;
use context_program;
use core\webapi\execution_context;
use core\webapi\type_resolver;
use coding_exception;
use coursecat;

/**
 * Certification type
 *
 * Note: It is the responsibility of the query to ensure the user is permitted to see a certification
 */
class certification implements type_resolver {

    /**
     * Resolve certification fields
     *
     * @param string $field
     * @param mixed $certification
     * @param array $args
     * @param execution_context $ec
     * @return mixed
     */
    public static function resolve(string $field, $certification, array $args, execution_context $ec) {
        global $CFG;

        require_once($CFG->dirroot . '/totara/program/lib.php');
        require_once($CFG->dirroot . '/totara/program/program.class.php');

        if (!$certification instanceof \program) {
            // Note: Currently this is accepting program objects, but when for certifid <> 0.
            throw new \coding_exception('Only certification program objects are accepted: ' . gettype($certification));
        }

        $format = $args['format'] ?? null;
        $programcontext = context_program::instance($certification->id);
        $ec->set_relevant_context($programcontext);

        if (!self::authorize($field, $format, $programcontext)) {
            return null;
        }

        $datefields = ['availablefrom', 'availableuntil'];
        if (in_array($field, $datefields) && empty($certification->{$field})) {
            // Highly unlikely this is set to 1/1/1970, return null for notset dates.
            return null;
        }

        if ($field == 'summaryformat') {
            return FORMAT_HTML;
        }

        if ($field == 'category') {
            return \coursecat::get($certification->category);
        }

        if ($field == 'coursesets') {
            $prog = new \program($certification->id);
            $content = $prog->get_content();
            return $content->get_course_sets();
        }

        $formatter = new certification_formatter($certification, $programcontext);
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

