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
 * @author David Curry <david.curry@totaralearning.com>
 * @package core
 */

namespace core\webapi\resolver\type;

use core\webapi\execution_context;
use core_course\formatter\course_section_formatter;
use core_availability\info as info;
use core\format;
use core\webapi\type_resolver;
use context_course;
use coursecat;

class course_section implements type_resolver {
    public static function resolve(string $field, $section, array $args, execution_context $ec) {
        global $CFG, $USER;

        if (!$section instanceof \section_info) {
            throw new \coding_exception('Only section_info objects are accepted: ' . gettype($section));
        }

        $format = $args['format'] ?? null;
        $course = $section->course;
        $context = \context_course::instance($course);
        // Note: Visibility should idealy be handled by the query to avoid empty records, but just in case...
        if (!$section->visible && !has_capability('moodle/course:viewhiddensections', $context, $USER->id)) {
            return null;
        }

        if (!self::authorize($field, $format, $context)) {
            return null;
        }

        $secinfo = new \stdClass();
        $secinfo->id = $section->id;

        $available = $section->available;
        if ($field == 'available') {
            $secinfo->available = $available;
        }

        if ($field == 'availablereason') {
            $secinfo->availablereason = [];
            if (!$available) {
                if (!empty($section->availableinfo)) {
                    if (is_string($section->availableinfo)) {
                        $secinfo->availablereason[] = $section->availableinfo;
                    } else {
                        $modinfo = $section->modinfo;

                        // Mimic half of core_availability::format_info() to get the cm names.
                        foreach ($section->availableinfo->items as $item) {
                            // Don't waste time if there are no special tags.
                            if (strpos($item, '<AVAILABILITY_') === false) {
                                $secinfo->availablereason[] = $item;
                                continue;
                            }

                            $info = preg_replace_callback('~<AVAILABILITY_CMNAME_([0-9]+)/>~',
                                        function($matches) use($modinfo, $context) {
                                            $cm = $modinfo->get_cm($matches[1]);
                                            if ($cm->has_view() and $cm->uservisible) {
                                                // Help student by providing a link to the module which is preventing availability.
                                                return \html_writer::link($cm->url, format_string($cm->name, true, array('context' => $context)));
                                            } else {
                                                return format_string($cm->name, true, array('context' => $context));
                                            }
                                        }, $item
                                    );
                            $secinfo->availablereason[] = $info;
                        }
                    }
                }
            }
        }

        if ($field == 'title') {
            $cformat = course_get_format($course);
            $secinfo->title = $cformat->get_section_name($section);
        }

        if ($field == 'summary') {
            $secinfo->summary = $section->summary;
        }

        // Transform the format field from the constants to a core_format string.
        if ($field == 'summaryformat') {
            switch ($section->summaryformat) {
                case FORMAT_MOODLE:
                case FORMAT_HTML:
                    return 'HTML';
                    break;
                case FORMAT_PLAIN:
                    return 'PLAIN';
                    break;
                case FORMAT_MARKDOWN:
                    return 'MARKDOWN';
                    break;
                case FORMAT_JSON_EDITOR:
                    return 'JSON_EDITOR';
                    break;
                default:
                    // Note: There is also FORMAT_WIKI but it has been deprecated since 2005.
                    throw new \coding_exception("Unrecognised summary format '{$section->summaryformat}'" );
                    break;
            }
        }

        if ($field == 'modules') {
            // Don't show the modules for restricted sections.
            if (!$section->available) {
                return [];
            }

            $seeall = has_capability('moodle/course:viewhiddenactivities', $context, $USER->id);
            $cms = $section->modinfo->get_cms();

            $sectionmodules = [];
            foreach ($cms as $mod) {
                // Limit this to visible course modules in the section.
                if ($mod->section == $section->id && ($seeall || $mod->visible)) {
                    $sectionmodules[] = $mod;
                }
            }

            return $sectionmodules;
        }

        $formatter = new course_section_formatter($secinfo, $ec->get_relevant_context());
        $formatted = $formatter->format($field, $format);

        // For mobile execution context, rewrite pluginfile urls in description and image_src fields.
        // This is clearly a hack, please suggest something more elegant.
        if (is_a($ec, 'totara_mobile\webapi\execution_context') && in_array($field, ['summary'])) {
            $formatted = str_replace($CFG->wwwroot . '/pluginfile.php', $CFG->wwwroot . '/totara/mobile/pluginfile.php', $formatted);
        }

        return $formatted;

    }

    public static function authorize(string $field, ?string $format, context_course $ec) {
        // Permission to see RAW formatted string fields
        if (in_array($field, ['title']) && $format == format::FORMAT_RAW) {
            return has_capability('moodle/course:update', $ec);
        }
        // Permission to see RAW formatted text fields
        if (in_array($field, ['summary']) && $format == format::FORMAT_RAW) {
            return has_capability('moodle/course:update', $ec);
        }
        return true;
    }
}
